<?php
/**
 * Plugin Settings form template
 *
 * @package WordPress Contributors
 */
?>
<div class="wpco-settings-wrapper">
	<!--Header-->
	<div class="wpco-settings-header">
		<h2><?php echo __( 'WordPress Contributor Plugins Settings', 'wordpress-contributors' ); ?></h2>
		<p><?php echo __( 'Choose pages to show the contributor box', 'wordpress-contributors' ); ?></p>
	</div>
	<!--Form-->
	<form method="post" class="wpco-settings-form" action="options.php">
		<?php
		settings_fields( 'wpco-plugin-settings-group' );
		do_settings_sections( 'wpco-plugin-settings-group' );
		$option_val_array = get_option( 'wpco_post_types' );
		$default_post_type = 'post';
		array_push( $cpt_array, $default_post_type  );
		?>

		<h4 class="wpco-post-type-heading"><?php echo __( 'Check the post type to enable the contributors box. Uncheck to disable it:', 'wordpress-contributors' ); ?></h4>
		<?php
			if ( is_array( $cpt_array ) && ! empty( $cpt_array ) ) {
				foreach (  $cpt_array as $post_type ) {
					$checked = ( in_array( $post_type, $option_val_array ) ) ? 'checked' : '';
					?>
					<div class="wpco-form-group">
						<label for="wpco-<?php echo $post_type; ?>" class="wpco-label">
							<input class="wpco-form-control" id="wpco-<?php echo $post_type; ?>" type="checkbox" name="wpco_post_types[]" value="<?php echo $post_type; ?>" <?php echo $checked; ?>>
							<?php echo ucfirst( $post_type ); ?>
						</label>
					</div>
					<?php
				}
			}
		?>
		<!--Submit Button-->
		<div class="wpco-save-btn-container"><?php submit_button(); ?></div>
	</form>
</div>