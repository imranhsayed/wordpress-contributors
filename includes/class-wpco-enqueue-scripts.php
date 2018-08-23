<?php
/**
 * Class WPCO_Enqueue_Scripts.
 *
 * @package WordPress Contributer
 */
class WPCO_Enqueue_Scripts {
	/**
	 * WPCO_Enqueue_Scripts constructor.
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wpco_enqueue_scripts_dashboard' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpco_enqueue_style_front_end' ) );
		add_action( 'admin_footer', array( $this, 'wpco_contributors_templates' ) );
	}

	/**
	 * Register Styles and scripts for plugin for Dashboard
	 *
	 * @param {string} $hook Hook.
	 */
	function wpco_enqueue_scripts_dashboard( $hook ) {

		// Apply the styles and scripts only on 'add new post' and 'edit post' page.
		if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
			wp_register_style( 'wpco_post_meta_css', WPCO_CSS_URI . 'add-post-meta.css', '', '', false );
			wp_register_script( 'wpco_main_js', WPCO_JS_URI . 'main.js', array( 'jquery' ), '', true );

			wp_enqueue_style( 'wpco_post_meta_css' );
			wp_enqueue_script( 'wpco_main_js' );

			wp_localize_script(
				'wpco_main_js', 'wpcoPostData', array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ), // admin_url( 'admin-ajax.php' ) returns the url till admin-ajax.php file of wordpress.
					'ajax_nonce' => wp_create_nonce( 'wpco_nonce_action_name' ),  // Create nonce and send it to js file in wpcoPostData.ajax_nonce.
				)
			);
		}

		// Add style on WPCO Settings page.
		if ( 'toplevel_page_wordpress-contributors/includes/class-wpco-settings' === $hook ) {
			wp_register_style( 'wpco_plugin_settings_css', WPCO_CSS_URI . 'plugin-settings.css', '', '', false );
			wp_enqueue_style( 'wpco_plugin_settings_css' );
		}
	}

	/**
	 * Add style for post contributors sections on front end.
	 */
	function wpco_enqueue_style_front_end() {
		wp_register_style( 'wpco_post_contributors_css', WPCO_CSS_URI . 'post-contributors.css', '', '', false );
		wp_enqueue_style( 'wpco_post_contributors_css' );
	}

	/**
	 * Contributors suggestions template script
	 */
	function wpco_contributors_templates() {

		// Markup for a contributors suggestions template when inserted into the DOM.
		?>
		<script type="text/html" id="tmpl-contributor-template">
			<div class="wpco-selected-username">
				<span class="wpco-selected-name">{{data.selectedUserName}}</span>
				<input type="hidden" class="wpco-contributors-input" name="wpco_post_authors[]" value="{{{data.selectedUserId}}}">
				<span class="dashicons dashicons-no-alt wpco-remove-contributor-icon"></span>
			</div>
		</script>
		<?php
	}
}

new WPCO_Enqueue_Scripts();
