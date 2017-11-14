<?php
/**
 * Functions/hooks for regular form to post type
 *
 * @package   caldera_custom_fields
 * @copyright 2014-2015 CalderaWP LLC and David Cramer
 */
add_filter( 'caldera_forms_get_form_processors', 'cf_custom_fields_posttype_process' );
add_filter( 'caldera_forms_get_entry_detail', 'cf_custom_fields_entry_details', 10, 3);
add_filter( 'caldera_forms_render_get_entry', 'cf_custom_fields_get_post_type_entry', 10, 3);
add_filter( 'caldera_forms_get_entry_meta_db_storage', 'cf_custom_fields_meta_view');
add_action( 'caldera_forms_processor_templates', 'cf_custom_fields_meta_template');
add_filter( 'caldera_forms_render_pre_get_entry', 'cf_custom_fields_populate_form_edit', 11, 3 );
add_filter( 'caldera_forms_get_addons', 'cf_custom_fields_savetoposttype_addon' );
add_action( 'caldera_forms_submit_start', 'cf_custom_fields_init_pods' );

// Better upload attach if CF Core is 1.5.0.9
//see https://github.com/CalderaWP/caldera-custom-fields/issues/17
if (  method_exists( 'Caldera_Forms_Admin', 'is_page' ) ) {
	add_filter( 'caldera_forms_render_setup_field', 'cf_custom_fields_filter_featured_image', 25, 2 );
	add_filter( 'caldera_forms_render_get_field', 'cf_custom_fields_filter_featured_image', 25, 2 );
	add_filter( 'caldera_forms_file_upload_handler', 'cf_custom_fields_filter_upload_handler', 10, 3 );
}

/**
 * Register this as a processor.
 *
 * @uses "caldera_forms_get_form_processors" filter
 *
 * @param $processors
 *
 * @return mixed
 */
function cf_custom_fields_posttype_process($processors){
	
	$processors['post_type'] = array(
		"name"				=>	__( 'Save as Post Type', 'caldera-forms-metabox' ),
		"author"            =>  'Caldera Labs',
		"description"		=>	__( 'Store form entries as a post with custom fields.', 'caldera-forms-metabox' ),
		"pre_processor"     => 'cf_custom_fields_pre',
		"post_processor"	=>	'cf_custom_fields_capture_entry',
		"template"			=>	trailingslashit( CCF_PATH ) . "includes/to-post-type-config.php",
		"icon"				=>	CCF_URL . "/post-type.png",
		"default"			=>	array(
			'post_status'	=>	"draft"
		),
		"meta_template"		=>	trailingslashit( CCF_PATH ) . "includes/meta_template.php",
		"magic_tags"		=>	array(
			"ID",
			"permalink"
		)
	
	);
	return $processors;
	
}

/**
 * Prepared from data for edit post.
 *
 * @since 1.1.0
 *
 * @uses "caldera_forms_render_pre_get_entry" filter
 *
 * @param array $data Form data.
 * @param array $form Form config.
 * @param $entry_id
 *
 * @return array
 */
function cf_custom_fields_populate_form_edit( $data, $form, $entry_id ){
	
	$processors = Caldera_Forms::get_processor_by_type( 'post_type', $form );
	if( !empty( $processors ) ){
		foreach( $processors as $processor ){
			if( !empty( $processor['config']['ID'] ) ){
				// ooo ID!!!
				$ID = Caldera_Forms::do_magic_tags( $processor['config']['ID'], $entry_id );
				if( !empty( $ID ) ){
					$post = get_post( $ID );
				}
				if( empty( $post ) ){
					return $data;
				}
				
				$data[ $processor['config']['post_title'] ] = $post->post_title;
				$data[ $processor['config']['post_content'] ] = $post->post_content;
				foreach( $form['fields'] as $field_id => $field ){
					if( $post->{$field['slug']} ){
						$data[ $field_id ] = $post->{$field['slug']};
					}
				}
			}
		}
	}
	return $data;
}

/**
 * Register the add-on
 *
 * @since 1.1.0
 *
 * @uses "caldera_forms_get_addons" filter
 *
 * @param array $addons
 *
 * @return array
 */
function cf_custom_fields_savetoposttype_addon($addons){
	$addons['savetoposttype'] = __FILE__;
	return $addons;
	
}

/**
 * Set template for meta fields
 *
 * @since 1.1.0
 *
 * @uses "caldera_forms_processor_templates" action
 */
function cf_custom_fields_meta_template(){
	?>
    <script type="text/html" id="post-meta-field-tmpl">
        <div class="caldera-config-group">
            <label>Field Name<input type="text" class="block-input field-config" name="{{_name}}[metakey][]" value="{{metakey}}"></label>
            <div class="caldera-config-field">
                <div>Value Field</div>
                <div style="width: 280px; display:inline-block;">{{{_field slug="meta_field" array="true"}}}</div>
                <button class="button remove-meta-field{{_id}}" type="button"><?php echo __('Remove', 'caldera-forms'); ?></button>
            </div>
        </div>
    </script>
	<?php
}

/**
 * Prepare DB storage
 *
 * @uses "caldera_forms_get_entry_meta_db_storage" filter
 *
 * @param $meta
 *
 * @return mixed
 */
function cf_custom_fields_meta_view($meta){
	$postid = $meta['meta_value'];
	$meta['meta_key'] = _('Post Name', 'caldera-forms-metabox' );
	$meta['meta_value'] = get_the_title($meta['meta_value']);
	$meta['meta_value'] .= '<div><a href="post.php?post='.$postid.'&action=edit" target="_blank">'.__('Edit').'</a> | <a href="' . get_permalink( $postid ) . '" target="_blank">'.__('View').'</a></div>';
	$meta['post'] = get_post( $postid );
	return $meta;
}

/**
 * Check if a form has the post type processor
 *
 * @since 1.1.0
 *
 * @param array $form Form config.
 * @param string $entry_id Entry ID.
 *
 * @return array|bool False if not in processor, else post object/ config.
 */
function cf_custom_fields_has_pr_processor($form, $entry_id){
	if(!empty($form['processors'])){
		foreach($form['processors'] as $processor){
			if($processor['type'] === 'db_storage'){
				$post = get_post($entry_id);
				if(empty($post)){
					return false;
				}
				
				if($post->post_type === $processor['config']['post_type']){
					return array(
						'post'	=> $post,
						'config'=> $processor['config']
					);
				}
			}
		}
	}
	
	return false;
}

/**
 * Get entry details
 *
 * @since 1.1.0
 *
 * @param array $entry Entry details
 * @param string $entry_id Entry ID
 * @param array $form
 *
 * @return array
 */
function cf_custom_fields_entry_details($entry, $entry_id, $form){
	
	
	if($processor = cf_custom_fields_has_pr_processor($form,$entry_id)){
		
		$entry = array(
			'id'		=>	$entry_id,
			'form_id'	=>	$form['ID'],
			'user_id'	=>	$processor['post']->post_author,
			'datestamp' =>	$processor['post']->post_date
		);
		
	}
	
	return $entry;
	
}

/**
 * Render saved entry when editing posts.
 *
 * @since 1.1.0
 *
 * @uses "caldera_forms_render_get_entry" filter
 *
 * @param array $data Rendered data.
 * @param array $form Form config.
 * @param string $entry_id Entry ID.
 *
 * @return array
 */
function cf_custom_fields_get_post_type_entry($data, $form, $entry_id){

	if($processor = cf_custom_fields_has_pr_processor($form, $entry_id)){
		$fields = $form['fields'];
		
		$data = array();
		$data[$fields[$processor['config']['post_title']]['slug']] = $processor['post']->post_title;
		unset($fields[$processor['config']['post_title']]);
		
		if(!empty($processor['config']['post_content'])){
			$data[$fields[$processor['config']['post_content']]['slug']] = $processor['post']->post_content;
			unset($fields[$processor['config']['post_content']]);
		}
		
		
		foreach($fields as $field){
			$data[$field['slug']] = get_post_meta( $processor['post']->ID, $field['slug'], true );
		}
	}
	return $data;
}



/**
 * Process entry and save as post/ post meta.
 *
 * @since 1.1.0
 *
 * @param array $config Processor config.
 * @param array $form From config.
 *
 * @return array
 */
function cf_custom_fields_capture_entry($config, $form){
	
	$user_id = get_current_user_id();
	if( !empty( $config['post_author'] ) ){
		$user_id = Caldera_Forms::do_magic_tags( $config['post_author'] );
	}
	
	$entry = array(
		'post_title'    => Caldera_Forms::get_field_data( $config['post_title'], $form ),
		'post_status'   => Caldera_Forms::do_magic_tags( $config['post_status'] ),
		'post_type'		=> $config['post_type'],
		'post_content'	=> Caldera_Forms::get_field_data( $config['post_content'], $form ),
		'post_parent'	=> Caldera_Forms::do_magic_tags( $config['post_parent'] ),
		'to_ping'		=> Caldera_Forms::do_magic_tags( $config['to_ping'] ),
		'post_password'	=> Caldera_Forms::do_magic_tags( $config['post_password'] ),
		'post_excerpt'	=> Caldera_Forms::do_magic_tags( $config['post_excerpt'] ),
		'comment_status'=> $config['comment_status'],
	);
	
	if( empty( $entry[ 'post_content' ] ) ){
		$entry[ 'post_content' ] = '';
	}
	
	
	
	// set the ID
	if( !empty( $config['ID'] ) ){
		$is_post_id = Caldera_Forms::do_magic_tags( $config['ID'] );
		$post = get_post( $is_post_id );
		if( !empty( $post ) && $post->post_type == $entry['post_type'] ){
			$entry['ID'] = $is_post_id;
		}
		
	}
	
	// set author
	if( !empty( $user_id ) ){
		$entry['post_author'] = $user_id;
	}
	
	$entry_id = null;
	
	//is edit?
	if(!empty($_POST['_cf_frm_edt'])){
		// need to work on this still. SIGH!
	}else{
		// Insert the post into the database
		$entry_id = wp_insert_post( $entry );
		if(empty($entry_id)){
			return;
			
		}
		$post = get_post( $entry_id );
		
	}
	
	/** @var CF_Custom_Fields_Pods $cf_custom_fields_pods */
	global  $cf_custom_fields_pods;
	if( is_object( $cf_custom_fields_pods ) && function_exists( 'pods' ) && pods_api()->pod_exists( $post->post_type ) && is_object( $cf_custom_fields_pods ) ){
		$cf_custom_fields_pods->set_pods( pods( $post->post_type, $post->ID ) );
	}else{
		if( is_object( $cf_custom_fields_pods ) ){
			$cf_custom_fields_pods->remove_hooks();
		}
	}
	
	// do upload + attach before 1.5.0.9
	//see https://github.com/CalderaWP/caldera-custom-fields/issues/17
	if ( ! method_exists( 'Caldera_Forms_Admin', 'is_page' ) ) {
		if ( ! empty( $config[ 'featured_image' ] ) ) {
			$featured_image = Caldera_Forms::get_field_data( $config[ 'featured_image' ], $form );
			foreach ( (array) $featured_image as $filename ) {
				$featured_image = cf_custom_fields_attach_file( $filename, $entry_id );
				update_post_meta( $entry_id, '_thumbnail_id', $featured_image );
			}
		}
	}else{
		if( !empty( $config['featured_image'] ) ){
			$featured_image = 	Caldera_Forms_Transient::get_transient( 'cf_cf_featured_' . $form[ 'ID' ] );
			if (  is_numeric( $featured_image ) ) {
				set_post_thumbnail( $post, $featured_image );
			}
			
		}
		
	}
	
	
	//handle taxonomies
	$terms_saved = false;
	$tax_fields = cf_custom_fields_get_taxonomy_fields( $config );
	if ( ! empty( $tax_fields ) ) {
		$terms_saved = cf_custom_fields_save_terms( $tax_fields, $entry_id );
		if ( $terms_saved ) {
			$term_values = wp_list_pluck( $tax_fields, 'terms' );
		}
	}
	
	//get post fields into an array of fields not to save as meta.
	$post_fields = array_keys( $entry );
	$mapped_post_fields = array();
	foreach ( $config as $field => $bound ){
		if( ! empty( $bound ) && in_array( $field, $post_fields ) ){
			$mapped_post_fields[ $field ] = $bound;
		}
		
	}
	
	// get all submission data
	$data = Caldera_Forms::get_submission_data( $form );
	update_post_meta( $entry_id, '_cf_form_id', $form['ID'] );
	foreach ( $data as $field_id => $value ) {
		
		$field = Caldera_Forms_Field_Util::get_field( $field_id, $form );
		if( empty( $field ) ){
			continue;
		}
		$slug = $field[ 'slug' ];
		if( in_array( $slug, $mapped_post_fields ) || in_array( $field[ 'ID' ], $mapped_post_fields ) ){
			continue;
		}
		
		if( in_array( '%' . $slug . '%', $mapped_post_fields   ) ){
			continue;
		}
		
		$type = Caldera_Forms_Field_Util::get_type( $field, $form );
		if( Caldera_Forms_Fields::not_support( $type, 'entry_list')){
			continue;
		}
		
		if ( '_entry_token' != $field && '_entry_id' != $field ) {
			if ( in_array( $field, $post_fields )  || in_array( $form['fields'][ $field_id ]['ID'], $post_fields ) ) {
				continue;
			}
			
		}
		
		if ( $terms_saved && is_array( $term_values ) ) {
			if ( is_array( $value ) ) {
				$_value = implode( ', ', $value );
			} else {
				$_value = $value;
			}
			
			if( in_array( $_value, $term_values  ) ){
				continue;
				
			}
		}
		
		
		if( Caldera_Forms_Field_Util::is_file_field( $field, $form ) ){
			if( $field['ID'] == $config['featured_image'] ){
				continue; // dont attach twice.
			}
			foreach( (array) $value as $file ){
				/**
				 * Option to prevent this add-ons uplpoad handler from being called on non-featured image fields.
				 *
				 * @since  2.1.5
				 *
				 * @param bool $use Should be used?
				 * @param array $field Field config
				 * @param array $config Processor config
				 */
				if( apply_filters( 'cf_custom_fields_use_uploader', true, $field, $form, $config ) ){
					cf_custom_fields_attach_file( $file , $entry_id );
				}
			}
		}
		
		
		
		/**
		 * Filter value before saving using to post type processor
		 *
		 * @since 2.0.3
		 *
		 * @param mixed $value The value to be saved
		 * @param string $slug Slug of field
		 * @param int $entry ID of post
		 * @param array $field Field config @since 2.2.0
		 * @param array $form Form config @since 2.2.0
		 */
		 $value = Caldera_Forms::do_magic_tags( $value, $entry );
		 $value = apply_filters( 'cf_custom_fields_pre_save_meta_key_to_post_type', $value, $slug, $entry_id, $field, $form );
		 update_post_meta( $entry_id, $slug, $value );
		
		/**
		 * Runs after value is saved using to post type processor
		 *
		 * @since 2.2.0
		 *
		 * @param mixed $value The value that was saved
		 * @param string $slug Slug of field
		 * @param int $entry ID of post
		 * @param array $fie;d Field config
		 * @param array $form Form config
		 */
		do_action( 'cf_custom_fields_post_save_meta_key_to_post_type',  $value, $slug, $entry_id, $field, $form );
	}
	
	return array('Post ID' => $entry_id, 'ID' => $entry_id, 'permalink' => get_permalink( $entry_id ) );
}

/**
 * Handle file fields.
 *
 * @since 1.1.0
 *
 * @param string $file File path.
 * @param string $entry_id The entry ID
 *
 * @return int Attachment ID.
 */
function cf_custom_fields_attach_file( $file, $entry_id ){
	
	// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	
	// Check the type of file. We'll use this as the 'post_mime_type'.
	$filetype = wp_check_filetype( basename( $file ), null );
	
	// Get the path to the upload directory.
	$wp_upload_dir = wp_upload_dir();
	
	$filename = $wp_upload_dir['path'] . '/' . basename( $file );
	$attachment = array(
		'guid'           => $wp_upload_dir['url'] . '/' . basename( $file ),
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);
	
	// Insert the attachment.
	$attach_id = wp_insert_attachment( $attachment, $filename, $entry_id );
	
	// Generate the metadata for the attachment, and update the database record.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	wp_update_attachment_metadata( $attach_id, $attach_data );
	
	return $attach_id;
	
}


function cf_custom_fields_taxonomy_ui(){
	$taxonomies = get_taxonomies( array(), 'objects' );
	$fields = array();
	$args = array(
		'magic' => true,
		'block' => true,
		'type'  => 'text',
	);
	foreach( $taxonomies as $taxonomy => $obj ){
		$args[ 'id' ] = 'cf-custom-fields-tax-' . $taxonomy;
		$args[ 'label' ] = $obj->labels->singular_name;
		$args[ 'extra_classes' ] = $taxonomy;
		$fields[] = Caldera_Forms_Processor_UI::config_field(  $args );
	}
	
	return implode( "\n\n", $fields );
}

/**
 * Find taxonomy fields and values
 *
 * @since 2.1.0
 *
 * @param $all_fields
 *
 * @return array
 */
function cf_custom_fields_get_taxonomy_fields( $all_fields ){
	$tax_fields = array();
	foreach( $all_fields as $field => $value ){
		if( false !== strpos( $field, 'cf-custom-fields-tax-') ){
			if ( ! empty( $value ) ) {
				$tax_fields[ $field ] = array(
					'taxonomy' => str_replace( 'cf-custom-fields-tax-', '', $field ),
					'terms'    => Caldera_Forms::do_magic_tags( $value )
				);
			}
		}
	}
	
	return $tax_fields;
}
/**
 * Check if string value is slug or name
 *
 * @since 2.2.1
 *
 * @param array $tax_fields Taxonomy fields to save
 * @param int $post_id Post ID
 *
 * @return bool
 */
function cf_check_string_kind( $string, $value, $taxonomy ) {
	if( !empty($string) ) {
		$string = get_term_by( 'slug', $value, $taxonomy );
		if( is_a( $string, 'WP_Term') ){
			$string = $string->term_id;
		}else{
			$string = get_term_by( 'name', $value, $taxonomy );
			if( is_a( $string, 'WP_Term') ){
				$string = $string->term_id;
			}
		}
	}
}

/**
 * Save taxonomy terms
 *
 * @since 2.1.0
 *
 * @param array $tax_fields Taxonomy fields to save
 * @param int $post_id Post ID
 *
 * @return bool
 */
function cf_custom_fields_save_terms( $tax_fields, $post_id ){
	if ( is_array( $tax_fields ) ) {
		foreach ( $tax_fields as $taxonomy => $data ) {
			if( empty( $data[ 'terms' ] ) ){
				continue;
			}
			$terms = $data[ 'terms' ];
			if( is_numeric( $terms ) && false === strpos( $terms, ',' ) ){
				$terms = (string) $terms;
				
			}elseif( is_string( $terms ) && false != strpos( $terms, ',' ) ){
				$terms = explode( ',', $terms );
				foreach( $terms as $i => $term ){
					if( is_numeric($term) ) {
						$terms[ $i ] = (int)$term;
					}elseif ( is_string( $term ) ){
						cf_check_string_kind( $term, $term, $data[ 'taxonomy'] );
					}else{
						continue;
					}
				}
			}elseif ( is_string( $terms ) ){
				cf_check_string_kind( $terms, $data[ 'terms' ], $data[ 'taxonomy'] );
			} elseif( is_array( $terms ) ){
				//yolo(?)
			}else {
				continue;
			}
			
			$updated = wp_set_object_terms( $post_id, $terms, $data[ 'taxonomy'] );
			
		}
		
		
	}
	
	return true;
}

/**
 * Use custom handler for featured image
 *
 * @uses "caldera_forms_file_upload_handler" filter
 *
 * @since 2.1.4
 *
 * @param array|string|callable Callable
 * @param array $form Form config
 * @param array $field Field config
 *
 * @return string
 */
function cf_custom_fields_filter_upload_handler( $handler, $form, $field ){
	if( cf_custom_fields_is_featured_image_field( $field, $form ) ){
		return 'cf_custom_fields_upload_handler';
	}
	
	return $handler;
	
}

/**
 * Handle featured image uploads
 *
 * @since 2.1.4
 *
 * @param array $file
 * @param array $args
 *
 * @return array
 */
function cf_custom_fields_upload_handler( $file, $args = array() ){
	$args['private'] = false; //Featured image needs to be public
	$upload = Caldera_Forms_Files::upload( $file, $args );
	if( isset( $args[ 'field_id' ] ) ){
		$form = null;
		if( isset( $args[ 'form_id' ] ) ){
			$form = Caldera_Forms_Forms::get_form( $args[ 'form_id' ] );
		}
		
		$field = Caldera_Forms_Field_Util::get_field( $args[ 'field_id' ], $form  );
		
		
	}else{
		$field = array();
	}
	
	$attachment_id = Caldera_Forms_Files::add_to_media_library( $upload, $field );
	if ( is_numeric( $attachment_id ) ) {
		Caldera_Forms_Transient::set_transient( 'cf_cf_featured_' . $args['form_id'], $attachment_id );
	}
	
	return $upload;
}

/**
 * Set featured image fields NOT to add to media library since they already are
 *
 * @since 2.1.4
 *
 * @uses "caldera_forms_render_setup_field" filter
 *
 * @param array $field Field config
 * @param array $form Form config
 *
 * @return mixed
 */
function cf_custom_fields_filter_featured_image( $field, $form ){
	if( cf_custom_fields_is_featured_image_field( $field, $form ) ){
		$field['config']['media_lib'] = false;
	}
	
	return $field;
	
}

/**
 * Check if field is the featured image field
 *
 * @since 2.1.4
 *
 * @param array $field Field config
 * @param array $form Form config
 *
 * @return bool
 */
function cf_custom_fields_is_featured_image_field( $field, $form ){
	$has = Caldera_Forms::get_processor_by_type( 'post_type', $form );
	if( $has && $field[ 'ID' ] == $has[0]['config'][ 'featured_image'] ){
		return true;
	}
	
	return false;
}

/**
 * Initialize Pods file field handling
 *
 * @since 2.2.0
 *
 * @uses "caldera_forms_submit_start" action
 */
function cf_custom_fields_init_pods() {
	//return if not CF 1.5.1+ or Pods isn't active
	if( ! class_exists( 'Caldera_Forms_DB_Tables' ) || ! function_exists( 'pods' ) ){
		return;
	}
	global $cf_custom_fields_pods;
	$cf_custom_fields_pods = new CF_Custom_Fields_Pods();
	$cf_custom_fields_pods->add_hooks();
	
}