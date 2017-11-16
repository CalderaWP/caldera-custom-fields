<?php
/**
 * Plugin Name: Caldera Custom Fields
 * Plugin URI:  https://calderaforms.com/downloads/caldera-form-custom-fields
 * Description: Create custom fields with powerful conditionals and processors using a Caldera Forms as the metabox designer.
 * Version:    2.2.1
 * Author:      Caldera Labs
 * Author URI:  https://CalderaLabs.org
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

define( 'CCF_PATH',  plugin_dir_path( __FILE__ ) );
define( 'CCF_URL',  plugin_dir_url( __FILE__ ) );
define( 'CCF_VER', '2.2.1' );
define( 'CCF_CORE',  __FILE__ );


/**
 * Load plugin
 */
add_action( 'init', 'cf_custom_fields_init' );
function cf_custom_fields_init() {
	if( class_exists( 'Caldera_Forms_Fields' ) ){
		include_once( CCF_PATH . '/includes/metabox.php' );
		include_once( CCF_PATH . '/includes/to-post-type.php' );
	}else{
		add_action( 'admin_notices', 'cf_custom_fields_need_cf_update_notice' );
	}



}

add_action( 'caldera_forms_includes_complete', function(){
	Caldera_Forms_Autoloader::add_root( 'CF_Custom_Fields', CCF_PATH . 'classes' );
});

/**
 * Adds an admin notice if plugin has wrong Caldera Forms core version
 *
 * @since 1.5.0
 *
 * @uses "admin_notices" action
 */
function cf_custom_fields_need_cf_update_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php esc_html_e( 'Caldera Forms Custom Fields Requires Caldera Forms 1.5 or later. Please update Caldera Forms or disable this add-on.', 'caldera-form-metabox' ); ?>
		</p>
	</div>
	<?php
}


/**
 * Get Caldera Forms
 *
 * Includes backwards compat for pre-Caldera Forms 1.3.4
 *
 * @since 2.0.5
 *
 * @deprecated 2.1.3
 *
 * @return array|void
 */
function cf_custom_fields_get_forms(){
	_deprecated_function( __FUNCTION__, '2.1.3', 'Caldera_Forms_Forms::get_forms( true )' );
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
 * @deprecated 2.1.3
 *
 *
 * @param string $id_name ID or name of form
 *
 * @return array|void
 */
function cf_custom_fields_get_form( $id_name ){
	_deprecated_function( __FUNCTION__, '2.1.3', 'Caldera_Forms_Forms::get_form()' );

	$form = Caldera_Forms_Forms::get_form( $id_name );


	if( isset( $form[ 'ID' ] ) && ! isset( $form[ 'id' ] ) ){
		$form[ 'id' ] = $form[ 'ID' ];
	}

	return $form;

}
