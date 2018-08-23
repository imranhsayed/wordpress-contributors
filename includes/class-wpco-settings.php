<?php
/**
 * Custom functions for creating admin menu settings for the plugin.
 *
 * @package WordPress Contributors
 */

class WPCO_Settings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wpco_create_menu' ) );
	}

	/**
	 * Creates Menu for Orion Plugin in the dashboard.
	 */
	public function wpco_create_menu() {

		$menu_plugin_title = __( 'WPCO Settings', 'orion-sms-orion-sms-otp-verification' );

		// Create new top-level menu.
		add_menu_page( __(
			'WPCO Plugin Settings',
			'wordpress-contributors' ),
			$menu_plugin_title,
			'administrator',
			__FILE__,
			array( $this, 'wpco_plugin_settings_page_content' ),
			'dashicons-admin-generic'
		);

		// Call register settings function.
		add_action( 'admin_init', array( $this, 'register_wpco_plugin_settings' ) );
	}

	/**
	 * Register our settings.
	 */
	public function register_wpco_plugin_settings() {
		register_setting( 'wpco-plugin-settings-group', 'wpco_post_types' );
	}

	/**
	 * Settings Page Content for Orion Plugin.
	 */
	public function wpco_plugin_settings_page_content() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$cpt_array = $this->wpco_get_cpt();

		/**
		 * Add error/update messages.
		 * Check if the user have submitted the settings.
		 * Wordpress will add the "settings-updated" $_GET parameter to the url.
		 */
		if ( isset( $_GET['settings-updated'] ) ) {

			// Add settings saved message with the class of "updated".
			add_settings_error( 'wpco_messages', 'wpco_message', __( 'Settings Saved', 'wordpress-contributors' ), 'updated' );
		}

		// Show error/update messages.
		settings_errors( 'wpco_messages' );

		include_once WPCO_TEMPLATE_PATH . 'settings-form-template.php';
	}

	/**
	 * Returns all the registered custom post types.
	 */
	public function wpco_get_cpt() {

		// The'_builtin' false will return only custom post types.
		$args = array( '_builtin' => false, );
		$cpt_array = get_post_types( $args );
		return ( ! empty( $cpt_array ) ) ? $cpt_array : array();
	}
}

new WPCO_Settings();
