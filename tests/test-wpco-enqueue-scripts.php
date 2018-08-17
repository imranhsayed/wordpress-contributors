<?php
/**
 * Class WPCO_Enqueue_Scripts_Test
 *
 * @package WordPress_Contributer
 */
class WPCO_Enqueue_Scripts_Test extends WP_UnitTestCase {

	/**
	 * Test for constructor function
	 */
	function test_constructor() {
		$enqueue_scripts = new WPCO_Enqueue_Scripts();

		// Check if both actions are registered.
		$dashboard_script_action_hooked = has_action( 'admin_enqueue_scripts', [ $enqueue_scripts, 'wpco_enqueue_scripts_dashboard' ] );
		$front_end_style_action_hooked = has_action( 'wp_enqueue_scripts', [ $enqueue_scripts, 'wpco_enqueue_style_front_end' ] );
		$template_action_hooked = has_action( 'admin_footer', [ $enqueue_scripts, 'wpco_contributors_templates' ] );

		$actions_registered = ( 10 === $dashboard_script_action_hooked && 10 === $front_end_style_action_hooked && $template_action_hooked );

		$this->assertTrue( $actions_registered );
	}

	/**
	 * Test for wpco_enqueue_scripts_dashboard()
	 */
	function test_wpco_enqueue_scripts_dashboard() {
		global $wp_scripts;

		$enqueue_scripts = new WPCO_Enqueue_Scripts();
		$hook = 'post-new.php';
		$enqueue_scripts->wpco_enqueue_scripts_dashboard( $hook );

		// Check if the scripts are enqueued, wp_style_is and wp_script_is will return true if they are enqueued.
		$enqueued_post_meta_css = wp_style_is( 'wpco_post_meta_css' );
		$enqueued_wpco_main_js = wp_script_is( 'wpco_main_js' );

		// $wp_scripts contains the data for all the registered scripts for our theme.
		$wpcoPostData = $wp_scripts->registered['wpco_main_js']->extra['data'];

		// Check if the $wpcoPostData contains admin-ajax.php in the url and the nonce.
		$has_admin_ajax_path = strpos( $wpcoPostData, 'admin-ajax.php' );
		$has_nonce = strpos( $wpcoPostData, 'ajax_nonce' );

		$test_result = ( $enqueued_post_meta_css && $enqueued_wpco_main_js && $has_admin_ajax_path && $has_nonce );

		$this->assertTrue( $test_result );
	}

	/**
	 * Test for swpco_enqueue_style_front_end()
	 */
	function test_wpco_enqueue_style_front_end() {
		$enqueue_style = new WPCO_Enqueue_Scripts();
		$enqueue_style->wpco_enqueue_style_front_end();

		// Check if the stylesheet is enqueued, wp_style_is will return true if its enqueued.
		$enqueued_post_meta_css = wp_style_is( 'wpco_post_contributors_css' );

		$this->assertTrue( $enqueued_post_meta_css );
	}

	/**
	 * Test for wpco_contributors_templates()
	 */
	function test_wpco_contributors_templates() {
		$template = new WPCO_Enqueue_Scripts();

		ob_start();
		$template->wpco_contributors_templates();
		$template_content = ob_get_clean();

		$string_pos = strpos( $template_content, 'data.selectedUserName' );
		$hasValidContent = ( $string_pos ) ? 'valid' : 'invalid';

		$this->assertTrue( 'valid' === $hasValidContent );
	}
}