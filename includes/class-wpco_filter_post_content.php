<?php
/**
 * Class WPCO_Filter_Post_Content
 * Filters post content and display the list auth authors.
 *
 * @package WordPress Contributors
 */

class WPCO_Filter_Post_Content {
	/**
	 * WPCO_Filter_Post_Content constructor.
	 */
	function __construct() {
		add_filter( 'the_content', array( $this, 'wpco_display_contributors' ) );
	}

	/**
	 * Generate the list of Contributors with their name, avatar and author link, and appends it to the content
	 * of each post, if there are contributors set for those posts.
	 *
	 * @param string $content Content of post.
	 *
	 * @return string $new_content Content with Contributors list.
	 */
	public function wpco_display_contributors( $content ) {
		if ( ! is_single() && ! is_author() && ! is_category() && ! is_tag() ) {
			return;
		}

		$post_id              = get_the_ID();
		$contributor_ids      = get_post_meta( $post_id, 'wpco_post_contributor_ids', true );
		$contributors_content = '';

		if ( is_array( $contributor_ids ) && ! empty( $contributor_ids ) ) {
			$contributors_content =
				'<div class="wpco_contributors-wrapper">' .
					'<h3 class="wpco_contributors-title">' . __( 'Contributors' ) . '</h3>' .
					'<div class="wpco_contributors-container">';

			foreach ( $contributor_ids as $id ) {
				$user_data  = get_userdata( $id );
				$user_name  = $user_data->user_nicename;
				$author_url = get_author_posts_url( $id );
				$avatar_img = ( ! is_category() && ! is_tag() ) ? get_avatar( $id, 50 ) : '';

				$contributors_content .=
					'<div class="wpco_avatar_container">' .
						'<a href="' . esc_url( $author_url ) . '">' .
							$avatar_img .
							'<span class="wpco_avatar-username">@' . $user_name . '</span>' .
						'</a>' .
					'</div>';
			}
			$contributors_content .=
					'</div>' .
				'</div>';
		}

		$new_content = $content . $contributors_content;
		return $new_content;
	}
}

new WPCO_Filter_Post_Content();
