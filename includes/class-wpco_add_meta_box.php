<?php
/**
 * Class WPCO_Add_Meta_Box
 *
 * @package WordPress Contributer
 */

class WPCO_Add_Meta_Box {
	/**
	 * WPCO_Add_Meta_Box constructor.
	 */
	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'wpco_add_custom_box' ) );
		add_action( 'save_post', array( $this, 'wpco_save_contributor_meta_data' ) );
	}

	/**
	 * Add meta box to the post editor screen.
	 */
	public function wpco_add_custom_box() {
		$screens = array( 'post', 'wporg_cpt' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wpco_box_id',
				__( 'Contributer(s)' ),
				array( $this, 'wpco_custom_box_html' ),
				$screen
			);
		}
	}

	/**
	 * Display the list if contributors you can select on add new post.
	 *
	 * @param {obj} $post Post variable.
	 */
	public function wpco_custom_box_html( $post ) {
		/**
		 * Use nonce for verification.
		 * This will create a hidden input field with id and name as 'wpco_add_contributor_nonce_name' and unique nonce input value.
		 */
		wp_nonce_field( plugin_basename( __FILE__ ), 'wpco_add_contributor_nonce_name' );
		$post_id           = $post->ID;
		$contributor_ids   = $this->wpco_get_contributors_ids( $post_id );
		?>
		<div class="wpco-ui-widget">
			<label for="wpco-search-input"></label>
			<input id="wpco-search-input" class="wpco-search-input" placeholder="Search Users">
			<div class="wpco-suggestions"></div>
		</div>
		<div class="wpco-selected-names-container"></div>
		<div class="wpco-selected-input-container">
			<?php
			$resultContent = '';
			$post_status   = $post->post_status;

			// If its a published post, then display the first author of the post.
			if ( 'publish' === $post_status ) {
				$post_author_id   = (int) $post->post_author;
				$post_author      = get_user_by( 'ID', $post_author_id );
				$post_author_name = $post_author ? $post_author->data->user_nicename : '';

				// Add the first author name of the post, which cannot be removed.
				$resultContent .=
					'<div class="wpco-selected-username wpco-author" >' .
					'<span>' . esc_html( $post_author_name ) . '</span>' .
					'<input type="hidden" class="wpco-contributors-input" name="wpco_post_authors[]" value="' . esc_html( $post_author_id ) . '">' .
					'</div>';
			}

			if ( is_array( $contributor_ids ) && ! empty( $contributor_ids ) ) {
				/**
				 * Add inputs for the contributors with values inside of them, if they were added before.
				 * These inputs will get saved along with the post.
				 */
				foreach ( $contributor_ids as $contributor_id ) {

					// If the contributor's id is same as the first author id, then skip to the next iteration as its already displayed above.
					if ( $contributor_id === (string) $post_author_id ) {
						continue;
					}

					$contributor      = get_user_by( 'ID', $contributor_id );
					$contributor_name = $contributor->data->user_nicename;
					$resultContent   .=
						'<div class="wpco-selected-username" >' .
						'<span class="wpco-selected-name">' . esc_html( $contributor_name ) . '</span>' .
						'<input type="hidden" class="wpco-contributors-input" name="wpco_post_authors[]" value="' . esc_html( $contributor_id ) . '">' .
						'<span class="dashicons dashicons-no-alt wpco-remove-contributor-icon"></span>' .
						'</div>';
				}
			}

			echo $resultContent;
			?>
		</div>
		<?php
	}

	/**
	 * Get an array of existing contributors ids if they were saved before.
	 *
	 * @param int $post_id Post Id.
	 *
	 * @return array $existing_contributors_ids existing contributors ids array for the given post id, if they were saved before.
	 */
	public function wpco_get_contributors_ids( $post_id ) {

		$existing_contributors_ids = get_post_meta( $post_id, 'wpco_post_contributor_ids' );
		$existing_contributors_ids = ( is_array( $existing_contributors_ids ) && ! empty( $existing_contributors_ids ) ) ? $existing_contributors_ids[0] : array(); // @todo Use [].
		return $existing_contributors_ids;
	}

	/**
	 * Save an array of user ids who are the contributor authors of the post, in wp_post_meta table.
	 * meta key is 'wpco_post_contributor_ids'
	 *
	 * @param int $post_id Post Id.
	 */
	public function wpco_save_contributor_meta_data( $post_id ) {
		/**
		 * When the post is saved or updated we get $_POST available.
		 * Check if the current user is authorised to do this action.
		 */
		if ( isset( $_POST['post_type'] ) && 'post' === $_POST['post_type'] && ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if the nonce valued we received is the same we created in 'ihs_display_custom_script_meta_boxes' function.
		if ( ! isset( $_POST['wpco_add_contributor_nonce_name'] ) || ! wp_verify_nonce( $_POST['wpco_add_contributor_nonce_name'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
		$contributors_ids_array = ( ! empty( $_POST['wpco_post_authors'] ) ? wp_unslash( $_POST['wpco_post_authors'] ) : array() );

		if ( $contributors_ids_array && is_array( $contributors_ids_array ) ) {
			$contributors_ids_array = array_unique( $contributors_ids_array );
			$post_ID                = intval( $_POST['post_ID'] );
			$post_ID                = ( ! empty( $post_ID ) ) ? $post_ID : - 1;

			// Save/Update the contributor author ids for the user authors selected into to the post_meta table.
			update_post_meta( $post_ID, 'wpco_post_contributor_ids', $contributors_ids_array );
		}
	}
}

new WPCO_Add_Meta_Box();
