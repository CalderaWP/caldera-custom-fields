<?php
/**
 * Plugin Name: Caldera Custom Fields
 * Plugin URI:  https://CalderaWP.com/downloads/caldera-form-custom-fields
 * Description: Create custom fields with powerful conditionals and processors using a Caldera Forms as the metabox designer.
 * Version:     2.1.1
 * Author:      David Cramer for CalderaWP LLC
 * Author URI:  https://CalderaWP.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'CCF_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CCF_URL',  plugin_dir_url( __FILE__ ) );
define( 'CCF_VER', '2.1.1' );
define( 'CCF_CORE',  __FILE__ );


/**
 * Load plugin
 */
add_action( 'init', 'cf_custom_fields_init' );
function cf_custom_fields_init() {
	include_once( CCF_PATH . '/includes/metabox.php' );
	include_once( CCF_PATH . '/includes/to-post-type.php' );
}

/**
 * Get Caldera Forms
 *
 * Includes backwards compat for pre-Caldera Forms 1.3.4
 *
 * @since 2.0.5
 *
 * @return array|void
 */
function cf_custom_fields_get_forms(){
	if ( class_exists( 'Caldera_Forms_Forms' )  ) {
		$forms = Caldera_Forms_Forms::get_forms( true );
	} else {
		$forms = Caldera_Forms::get_forms();
	}

	return $forms;
}

/**
 * Get Caldera Forms
 *
 * Includes backwards compat for pre-Caldera Forms 1.3.4
 *
 * @since 2.0.5
 *
 * @param string $id_name ID or name of form
 *
 * @return array|void
 */
function cf_custom_fields_get_form( $id_name ){
	if ( class_exists( 'Caldera_Forms_Forms' )  ) {
		$form = Caldera_Forms_Forms::get_form( $id_name );
	} else {
		$form = Caldera_Forms::get_form( $id_name );
	}

	if( isset( $form[ 'ID' ] ) && ! isset( $form[ 'id' ] ) ){
		$form[ 'id' ] = $form[ 'ID' ];
	}

	return $form;

}
