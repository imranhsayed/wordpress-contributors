<?php
/**
 * Class WPCO_Add_Meta_Box_Test
 *
 * @package WordPress_Contributer
 */

class WPCO_Add_Meta_Box_Test extends WP_UnitTestCase {

	/**
	 * Test function for Constructor Function.
	 */
	function test_constructor() {
		$add_meta_box = new WPCO_Add_Meta_Box();

		// Check if both actions are registered.
		$meta_action_hooked = has_action( 'add_meta_boxes', [ $add_meta_box, 'wpco_add_custom_box' ] );
		$post_action_hooked = has_action( 'save_post', [ $add_meta_box, 'wpco_save_contributor_meta_data' ] );

		$actions_registered = ( 10 === $meta_action_hooked && 10 === $post_action_hooked ) ? 'registered' : 'not registered';

		$this->assertTrue( 'registered' === $actions_registered );
	}

	/**
	 * Test function for adding meta boxes on add new post and edit post screens
	 */
	function test_wpco_add_custom_box() {
		global $wp_meta_boxes;

		$add_meta_box = new WPCO_Add_Meta_Box();
		$add_meta_box->wpco_add_custom_box();

		// Check if the two meta boxes are added on the add new post and edit post screens.
		$add_post_screen_id = $wp_meta_boxes['post']['advanced']['default']['wpco_box_id']['id'];
		$edit_post_screen_id = $wp_meta_boxes['wporg_cpt']['advanced']['default']['wpco_box_id']['id'];
		$meta_boxes_added = ( 'wpco_box_id' === $add_post_screen_id && 'wpco_box_id' === $edit_post_screen_id ) ? 'added' : 'not added';

		$this->assertTrue( 'added' === $meta_boxes_added );
	}

	/**
	 * Test function for adding custom meta box html.
	 */
	function test_wpco_custom_box_html() {
		global $wp_query;
		global $post;

		$add_meta_box = new WPCO_Add_Meta_Box();

		// Create two Dummy user ids.
		$user_ids = $this->factory->user->create_many( 2 );

		// Create a dummy post using the 'WP_UnitTest_Factory_For_Post' class and give the post author's user ud as 2.
		$post_id = $this->factory->post->create( [
			'post_status' => 'publish',
			'post_title'  => 'Test 1',
			'post_content' => 'Test Content',
			'post_author' => 2,
			'post_type' => 'post'
		] );

		// Create a custom query for the post with the above created post id.
		$wp_query = new WP_Query( [
			'post__in' => [ $post_id ],
			'posts_per_page' => 1,
		] );

		// Run the WordPress loop through this query to set the global $post.
		if ( $wp_query->have_posts() ) {
			while( $wp_query->have_posts() ) {
				$wp_query->the_post();
			}
		}

		// Set the array of user ids to post meta with meta key 'wpco_post_contributor_ids', with the above created post id.
		update_post_meta( $post_id, 'wpco_post_contributor_ids', $user_ids );

		// Store the echoed value of the wpco_custom_box_html() into $custom_box_html using output buffering.
		ob_start();
		$add_meta_box->wpco_custom_box_html( $post );
		$custom_box_html = ob_get_clean();

		// Validate the output string contains the class names we are expecting.
		$author_string = strpos( $custom_box_html, 'wpco-author' );
		$contributor_string = strpos( $custom_box_html, 'wpco-selected-name' );


		$custom_box_html_output = ( $author_string && $contributor_string );

		$this->assertTrue( $custom_box_html_output );

        wp_reset_postdata();
	}

	/**
	 * Test for getting contributors id.
	 */
	function test_wpco_get_contributors_ids() {
		$add_meta_box = new WPCO_Add_Meta_Box();

		// Create two Dummy user ids.
		$this->factory->user->create_many( 2 );

		// Create a dummy post using the 'WP_UnitTest_Factory_For_Post' class and give the post author's user ud as 2.
		$post_id = $this->factory->post->create( [
			'post_status' => 'publish',
			'post_title'  => 'Test 1',
			'post_content' => 'Test Content',
			'post_author' => 2,
		] );

		// Set the array of user ids to post meta with meta key 'wpco_post_contributor_ids', with the above created post id.
		update_post_meta( $post_id, 'wpco_post_contributor_ids', array( 2, 3 ) );

		$contributors_id = $add_meta_box->wpco_get_contributors_ids( $post_id );
		$contributors_id_validity = ( 2 === $contributors_id[0] && 3 === $contributors_id[1] );
		$this->assertTrue( $contributors_id_validity );
	}

}