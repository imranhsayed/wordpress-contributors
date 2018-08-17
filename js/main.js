/* global wpcoPostData */
( function( $ ) {
	"use strict";
	var contributors = {

		/**
		 * Adds two numbers together.
		 *
		 * @returns {void}
		 */
		init: function () {
			this.addSpinnerEvents();
			this.selectContributor();
			this.removeSelectedContributorEvent();
		},

		/**
		 * Add event to select the contributor from the suggestion.
		 * Get the id from the 'data-user-id' attribute of selected element.
		 * Append the input element for each contributor selected.
		 * Remove the suggestions and text in the search input.
		 *
		 * @returns {void}
		 */
		selectContributor: function () {
			var targetEl, selectedUserId, selectedUserName, contributorTemplate, templateData,
				suggestionsContainer = $( '.wpco-suggestions' ),
				searchInputContainer = $( '.wpco-search-input' );
			suggestionsContainer.on( 'click', '.wpco-suggestion-item', function () {

				// Add selected contributor inputs.
				targetEl = $( event.target );
				selectedUserId = targetEl.attr( 'data-user-id' );
				selectedUserName = targetEl.text();

				// Using wp.template to insert selected users content.
				contributorTemplate = wp.template( 'contributor-template' );
				templateData = {
					selectedUserName: selectedUserName,
					selectedUserId: selectedUserId
				};
				$( '.wpco-selected-input-container' ).append( contributorTemplate( templateData ) );

				// Make the suggestion box and the search input box empty
				suggestionsContainer.empty();
				searchInputContainer.val( '' );
			} );
		},

		/**
		 * Remove the selected contributor on click of its cross icon.
		 *
		 * @returns {void}
		 */
		removeSelectedContributorEvent: function () {
			$( '.wpco-selected-input-container' ).on( 'click', '.wpco-remove-contributor-icon', function() {
				$( event.target ).parent().remove()
			} );
		},

		/**
		 * Add Spinner Events.
		 * On key down the class for adding spinner gif is added and on key up its removed.
		 *
		 * @returns {void}
		 */
		addSpinnerEvents: function () {
			var userInput = $( '#wpco-search-input' ),
				minStringLength = 2;

			userInput.on( 'keydown', function () {
				userInput.addClass( 'wpco-autocomplete-loading' );
			} );

			userInput.on( 'keyup', function () {
				userInput.removeClass( 'wpco-autocomplete-loading' );
				var queryString = $( '#wpco-search-input' ).val();
				if ( minStringLength <= queryString.length ) {
				    contributors.ajaxRequestForUsers( queryString );
				}
			} );
		},

		/**
		 * This ajax request is made to get the data from the database for the users matching the queryString.
		 *
		 * @param {String} queryString String that the user enters.
		 * @returns {void}
		 */
		ajaxRequestForUsers: function ( queryString ) {
			var request = $.post(
				wpcoPostData.ajax_url,
				{
					action: 'wpco_ajax_hook',
					security: wpcoPostData.ajax_nonce,
					query:   queryString
				}
			);

			request.done( function ( response ) {
				var usersArray = response.data.users,
					suggestionsContainer = $( '.wpco-suggestions' ),
					resultContent = '',
					userData, userId, userName, userRole, isSubscriber, i;

				// Check if there were any users found.
				if ( usersArray.length ) {
					resultContent = '<div class="wpco-suggestions-container">';
					for ( i = 0; i < usersArray.length; i ++ ) {
						userRole = usersArray[i].roles;
						isSubscriber = userRole.indexOf( 'subscriber' );

						// If its not the subscriber, create a content containing matched users to show as suggestions
						if ( -1 === isSubscriber ) {
							userData = usersArray[ i ].data;
							userId = userData.ID;
							userName = userData.display_name;
							resultContent += '<p class="wpco-suggestion-item" data-user-id="' + userId + '">' + userName + '</p>';
						}
					}
					resultContent += '<div>';

					// Add the created content for the matched user as suggestions.
					suggestionsContainer.html( resultContent );
				}
			} );
		}
	};

	contributors.init();

} )( jQuery );