<?php
/**
 * Class WPCO_Get_User_Data_Test
 *
 * @package WordPress_Contributer
 */
class Test_Ajax_WPCO_Get_User_Data extends WP_Ajax_UnitTestCase {

    public function setUp() {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    /**
     * Helper to keep it DRY
     *
     * @param string $action Action.
     */
    protected function make_ajax_call( $action ) {
        // Make the request.
        try {
            $this->_handleAjax( $action );
        } catch ( WPAjaxDieContinueException $e ) {
            unset( $e );
        }
    }

    /**
     * Testing successful ajax_insert_auto_draft_post
     *
     * @see WP_Customize_Posts::ajax_insert_auto_draft_post()
     */
    function test_ajax_wpco_get_users_data() {
        // Create a user with nicename 'Amy' , using WP_UnitTestCase factory.
        $user_id = $this->factory->user->create( [
            'user_nicename' => 'Amy',
        ] );
        $_POST =  array(
            'action' => 'wpco_ajax_hook',
            'security' => wp_create_nonce( 'wpco_nonce_action_name' ),
            'post_type' => 'post',
            'query' => 'Amy'
        );
        $this->make_ajax_call( 'wpco_ajax_hook' );
        // Get the results.
        $response = json_decode( $this->_last_response, true );

        $this->assertTrue( $response['success'] );
    }
}