<?php
/**
 * Plugin Name: Caldera Custom Fields
 * Plugin URI:  
 * Description: Create custom fields with powerful conditionals and processors using a Caldera Forms as the metabox designer.
 * Version:     2.0.1
 * Author:      David Cramer for CalderaWP LLC
 * Author URI:  
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'CCF_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CCF_URL',  plugin_dir_url( __FILE__ ) );
define( 'CCF_VER', '2.0.1' );
define( 'CCF_CORE',  __FILE__ );


/**
 * Load plugin
 */
add_action( 'init', 'cf_custom_fields_init' );
function cf_custom_fields_init() {
	include_once( CCF_PATH . '/includes/metabox.php' );
	include_once( CCF_PATH . '/includes/to-post-type.php' );
}
