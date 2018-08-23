<?php
/**
 * Class WPCO_Get_User_Data class.
 *
 * @package WordPress Contributer
 */
class WPCO_Get_User_Data {
	/**
	 * WPCO_Get_User_Data constructor.
	 */
	function __construct() {
		add_action( 'wp_ajax_wpco_ajax_hook', array( $this, 'wpco_get_users_data' ) );
	}

	/**
	 * Get the data for matched users searched by the query string and pass it as success response to the request.done function in main.js
	 *
	 */
	function wpco_get_users_data() {

		// If nonce verification fails die.
		$nonce = ( ! empty( $_POST['security'] ) ) ? wp_unslash( $_POST['security'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'wpco_nonce_action_name' ) ) {
			wp_die();
		}

		$query       = sanitize_text_field( wp_unslash( $_POST['query'] ) );
		$string_length = 2;
		$logged_in_user_id = get_current_user_id();
		$user_id_array_to_be_excluded = ( $logged_in_user_id ) ? array( $logged_in_user_id ) : array();
		$users_found = array();

		if ( ! empty( $query ) && $string_length < strlen( $query ) ) {
			/**
			 * Perform query to get users by their name or email.
			 * Exclude the currently logged in user from the search.
			 */
			$users = new WP_User_Query(
				array(
					'search'         => '*' . esc_attr( $query ) . '*',
					'search_columns' => array(
						'user_nicename',
						'user_email',
					),
					'exclude' => $user_id_array_to_be_excluded,
				)
			);

			$users_found = $users->get_results();
		}

		wp_send_json_success(
			array(
				'users' => $users_found,
			)
		);
	}
}

new WPCO_Get_User_Data();
