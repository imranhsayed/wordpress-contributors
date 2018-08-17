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
		$users_found = array();

		if ( ! empty( $query ) && 2 < strlen( $query ) ) {
			$users = new WP_User_Query(
				array(
					'search'         => '*' . esc_attr( $query ) . '*',
					'search_columns' => array(
						'user_nicename',
					),
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
