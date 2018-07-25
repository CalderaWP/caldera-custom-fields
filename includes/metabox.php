<?php
/**
 * Functions/hooks for metabox
 *
 * @package   caldera_custom_fields
 * @copyright 2014-2017 CalderaWP LLC and David Cramer
 */

// add actions
add_action( 'add_meta_boxes', 'cf_custom_fields_form_as_metabox' );
add_action( 'save_post', 'cf_custom_fields_save_post' );

// add filters
add_filter('caldera_forms_get_form_processors', 'cf_custom_fields_register_metabox_processor');


/**
 * Before regular submit kicks in, check if is metabox save, verify nonce then remove regular path to process.
 *
 * @since 2.1.3
 *
 * Save will happen at "save_post" action
 */
add_action( 'wp_loaded', function(){
	if (isset($_POST['_cf_frm_id'], $_POST[ 'cf_metabox_forms']  ) ) {
		if ( isset( $_POST[ '_cf_verify' ] ) && Caldera_Forms_Render_Nonce::verify_nonce( $_POST[ '_cf_verify' ], $_POST[ '_cf_frm_id' ] ) ) {
			remove_action( 'wp_loaded', array( Caldera_Forms::get_instance(), 'cf_init_system' ), 25 );

		}

	}
}, 19 );



// admin filters & actions
if( is_admin() ){

	// disable redirect
	add_filter('caldera_forms_redirect_url', 'cf_custom_fields_prevent_redirect', 1, 4);

	// disable mailer
	add_filter('caldera_forms_get_form', 'cf_custom_fields_prevent_mailer', 1, 4);

	// save action to disable mailer
	add_filter('caldera_forms_presave_form', 'cf_custom_fields_metabox_save_form');


}

/**
 * Filter form database and mailer options if is set as a metabox
 *
 * @uses "caldera_forms_get_form"
 *
 * @param $form
 *
 * @return array
 */
function cf_custom_fields_prevent_mailer( $form ){
	$processors = Caldera_Forms::get_processor_by_type('cf_asmetabox', $form);
	if( !empty( $processors ) ){
		if( isset( $form['mailer']['on_insert'] ) ){
			unset( $form['mailer']['on_insert'] );
		}
		if( !empty( $form['mailer']['db_support'] ) ){
			$form['mailer']['db_support'] = false;
		}
	}
	return $form;
}
/**
 * Add the processor.
 *
 * @uses "caldera_forms_get_form_processors"
 *
 * @param $processors
 *
 * @return mixed
 */
function cf_custom_fields_register_metabox_processor($processors){
	$processors['cf_asmetabox'] = array(
		"name"				=>	__( 'Custom Fields Post Metabox', 'caldera-forms-metabox' ),
		"author"            =>  'Caldera Labs',
		"description"		=>	__( 'Use a form as a custom metabox in the post editor.', 'caldera-forms-metabox' ),
		"single"			=>	true,
		"processor"			=>	'cf_custom_fields_save_meta_data',
		"template"			=>	trailingslashit( CCF_PATH ) . "includes/metabox-config.php",
		"icon"				=>	CCF_URL . "/metabox-icon.png",
		"conditionals"		=>	false,
	);
	return $processors;

}

/**
 * Disable mailer/ form AJAX/ DB support for metabox forms.
 *
 * @since 1.?.?
 *
 * @uses "caldera_forms_presave_form" filter
 *
 * @param $form
 */
function cf_custom_fields_metabox_save_form($form){
	if ( isset( $form[ 'processors' ] ) ) {
		foreach ( $form[ 'processors' ] as $processor ) {
			if ( 'cf_asmetabox' == $processor[ 'type' ] ) {
				$form[ 'is_metabox' ] = true;
				// disable DB support
				$form['db_support'] = 0;

				// no ajax forms
				if( isset( $form['form_ajax'] ) ){
					unset( $form['form_ajax'] );
				}

				// disable mailer
				$form['mailer']['enable_mailer'] = 0;
				$form['db_support'] = 0;
				$form['mailer']['enable_mailer'] = 0;

				return $form;

			}
		}

	}

	return $form;

}




/**
 * Prevent redirect.
 *
 * @since 1.?.?
 *
 * @uses "caldera_forms_redirect_url" filter;
 *
 * @param string $url Redirect URL
 * @param array $data Submission data.
 * @param array $form Form config.
 *
 * @return bool
 */
function cf_custom_fields_prevent_redirect($url, $form, $process_id){

	if( !empty($form['is_metabox'])){
		return false;
	}

	return $url;
}

/**
 * Save meta data from form.
 *
 * @since 1.?.?
 *
 * @param array $config Processor config.
 * @param array $form Form config.
 */
function cf_custom_fields_save_meta_data($config, $form){
	global $post;

	if(!is_admin()){
		return;
	}

	$data = Caldera_Forms::get_submission_data($form);

	$field_toremove = array();

	foreach($form['fields'] as $field_id => $field){
		if( 'button' == Caldera_Forms_Field_Util::get_type($field, $form ) && $field['config']['type'] === 'submit' ){
			unset( $form[ 'fields' ][ $field_id ] );
		}elseif ( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
			unset( $form[ 'fields' ][ $field_id ] );
		}elseif( Caldera_Forms_Fields::not_support( Caldera_Forms_Field_Util::get_type( $field, $form ), 'entry_list')){
			unset( $form[ 'fields' ][ $field_id ] );
		}


		delete_post_meta( $post->ID, $field['slug'] );
	}

	// add filter to remove submit buttons
	add_filter('caldera_forms_render_setup_field', 'cf_custom_fields_submit_button_removal', 10, 2 );

	// add filter to remove file fields
	add_filter('caldera_forms_render_setup_field', 'cf_custom_fields_remove_file_fields', 10, 2 );

	foreach($data as $key=>$value){
		if(empty($form['fields'][$key])){
			continue;
		}

		$slug = $form['fields'][$key]['slug'];

		/**
		 * Filter value before saving using to metabox processor
		 * @since 2.0.3
		 *
		 * @param mixed $value The value to be saved
		 * @param string $slug Slug of field
		 * @param int $post_id ID of post
		 */
		$value = apply_filters( 'cf_custom_fields_pre_save_meta_key_metabox', $value, $slug, $post->ID );
		if( is_array( $value ) ){
			delete_post_meta( $post->ID, $slug );
			foreach( $value as $single_value ){
				add_post_meta( $post->ID, $slug, $single_value );				
			}
		}else{
			update_post_meta( $post->ID, $slug, $value );
		}
		if(isset($field_toremove[$form['fields'][$key]['slug']])){
			unset($field_toremove[$form['fields'][$key]['slug']]);
		}
	}

	return $data;
}


/**
 * Setup form in the admin
 *
 * @uses "add_meta_boxes" action
 *
 * @since 1.?.?
 */
function cf_custom_fields_form_as_metabox() {
	$forms = Caldera_Forms_Forms::get_forms( true );
	if(empty($forms)){
		return;
	}
	foreach($forms as $form){
		$form = Caldera_Forms_Forms::get_form( $form[ 'ID' ] );
		if( ! is_array( $form ) ){
			continue;
		}
		$processors = Caldera_Forms::get_processor_by_type('cf_asmetabox', $form);
		
		if(!empty( $processors )){
		
			$processor = $processors[0];

			// is metabox processor
			if(!empty($form['processors'][ $processor['ID'] ]['config']['posttypes'])){

				// add filter to get details of entry
				add_filter('caldera_forms_get_entry_detail', 'cf_custom_fields_get_post_details', 10, 3 );

				// add filter to remove submit buttons
				add_filter('caldera_forms_render_setup_field', 'cf_custom_fields_submit_button_removal', 10, 2 );

				// add filter to remove file fields
				add_filter('caldera_forms_render_setup_field', 'cf_custom_fields_remove_file_fields', 10, 2 );

				foreach( $form['processors'][ $processor['ID'] ]['config']['posttypes'] as $screen=>$enabled){
					add_meta_box(
						$form['ID'],
						$form['name'],
						'cf_custom_fields_render',
						$screen,
						$form['processors'][ $processor['ID'] ]['config']['context'],
						$form['processors'][ $processor['ID'] ]['config']['priority']
					);
				}
			}

			Caldera_Forms_Admin_Assets::admin_common();
			Caldera_Forms_Admin_Assets::form_editor();
			Caldera_Forms_Render_Assets::enqueue_all_fields();

			// metabox & gridcss
			wp_enqueue_style( 'cf-metabox-grid-styles', CCF_URL . 'css/metagrid.css');
			wp_enqueue_style( 'cf-metabox-styles', CCF_URL . 'css/metabox.css');
		}
	}

}

/**
 * Save form meta data.
 *
 * @since 1.?.?
 *
 * @uses "caldera_forms_render_get_entry" filter
 *
 * @param $data
 * @param $form
 *
 * @return array
 */
function cf_custom_fields_get_meta_data($data, $form){
	global $post;
	$entry = array();
	foreach($form['fields'] as $fieldslug => $field ){
		$data = get_post_meta( $post->ID, $field['slug'] );
		if( count( $data ) > 1 ){
			foreach( $data as $item ){
				$entry[$fieldslug][] = $item;
			}
		}else{

			if( is_array( $data ) && isset( $data[0] ) && is_scalar( $data[0] )    ){
                $data = $data[0];
			}elseif( is_array( $data ) ){
			    $data = '';
            }
			
			$entry[$fieldslug] = $data;
		}
		
	}
	return $entry;
}


/**
 * Process data on post save.
 *
 * @since 1.?.?
 *
 * @uses "save_post" action
 */
function cf_custom_fields_save_post(){

	if(is_admin()){
		if(isset($_POST['cf_metabox_forms'])){

			foreach( $_POST['cf_metabox_forms'] as $metaForm ){
				// add filter to get details of entry
				$_POST['_cf_frm_id'] = $metaForm;
				add_filter('caldera_forms_get_entry_detail', 'cf_custom_fields_get_post_details', 10, 3);
				Caldera_Forms::process_submission();

			}
		}
	}
}


/**
 * Render fields in editor.
 *
 * @since 1.?.?
 *
 * @param object $post Post object.
 * @param array $args Args
 */
function cf_custom_fields_render($post, $args){
	if(isset($_GET['cf_su'])){
		unset($_GET['cf_su']);
	}
	add_filter( 'caldera_forms_render_pre_get_entry', 'cf_custom_fields_get_meta_data', 10, 2);
	add_filter( 'caldera_forms_render_form_element', function( $element ){
		return 'div';
	} );
	ob_start();
	$form =  Caldera_Forms::render_form( $args['id'] );

	// register this form for processing'
	echo '<input type="hidden" name="cf_metabox_forms[]" value="' . $args['id'] . '">';
	echo $form;

}


/**
 * Get details from entry.
 *
 * @since 1.?.?
 *
 * @uses "caldera_forms_get_entry_detail" fitler
 *
 * @param $details
 * @param $entry
 * @param $form
 *
 * @return array
 */
function cf_custom_fields_get_post_details($details, $entry, $form){
	global $post;

	return array(
		'id' 		=> $post->ID,
		'form_id' 	=> $form['ID'],
		'user_id' 	=> get_current_user_id(),
		'datestamp'	=> $post->post_date
	);
}

/**
 * Remove submit button.
 *
 * @since 1.?.?
 *
 * @uses "caldera_forms_render_setup_field" filter
 * @param array $field
 * @param array $form
 *
 * @return bool|array
 */
function cf_custom_fields_submit_button_removal($field, $form ){
	$type = Caldera_Forms_Field_Util::get_type($field, $form );
	if( 'button' == $type ){
		$field['config']['class'] .= ' button';
		if( $field['config']['type'] === 'submit' ){
			return false;
		}
	}
	return $field;
	
}

/**
 * Remove file fields
 *
 * @since 2.1.3
 *
 * @uses "caldera_forms_render_setup_field" filter
 * @param array $field
 * @param array $form
 *
 * @return bool|array
 */
function cf_custom_fields_remove_file_fields( $field, $form ){
	if( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
		return false;
	}

	return $field;

}


















