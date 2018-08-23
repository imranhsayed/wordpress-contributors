<?php
/**
 * Class WPCO_Add_Option
 *
 * @package WordPress Contributors
 */
class WPCO_Add_Option {
	/**
	 * WPCO_Add_Option constructor.
	 * Registers activation hook for the plugin.
	 */
	public function __construct() {
		register_activation_hook( WPCO_PLUGIN_PATH, array( $this, 'wpco_add_settings_option' ) );
	}

	/**
	 * Add the option key and value for default post, if it does not already exists.
	 */
	public function wpco_add_settings_option() {
		$existing_option = get_option( 'wpco_post_types' );
		if ( ! $existing_option ) {
			$option_val = array( 'post' );
			add_option( 'wpco_post_types', $option_val );
		}
	}
}

new WPCO_Add_Option();
