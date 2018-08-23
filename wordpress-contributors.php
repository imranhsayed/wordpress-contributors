<?php
/**
 * Plugin Name: WordPress Contributors
 * Plugin URI: http://imransayed.com/wordpress-contributors
 * Description: This plugin allows you to display more than one author-name on a post.
 * Version: 1.0.0
 * Author: Imran Sayed
 * Author URI: https://profiles.wordpress.org/gsayed786
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordpress-contributors
 * Domain Path: /languages
 *
 * @package WordPress Contributors
 */

// Define Constants.
define( 'WPCO_URI', plugins_url( 'wordpress-contributors' ) );
define( 'WPCO_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'templates/' );
define( 'WPCO_JS_URI', plugins_url( 'wordpress-contributors' ) . '/js/' );
define( 'WPCO_CSS_URI', plugins_url( 'wordpress-contributors' ) . '/css/' );

// File Includes.
include_once 'includes/class-wpco-enqueue-scripts.php';
include_once 'includes/class-wpco-get-user-data.php';
include_once 'includes/class-wpco_add_meta_box.php';
include_once 'includes/class-wpco-settings.php';
include_once 'includes/class-wpco_filter_post_content.php';
