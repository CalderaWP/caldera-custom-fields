<?php


/**
 * Class CF_Custom_Fields_Pods
 */
class CF_Custom_Fields_Pods {

	/**
	 * Tracks ID of file fields and attatchemnts
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	protected $files;


	/**
	 * Pods object for saving
	 *
	 * @since 2.2.0
	 *
	 * @var Pods
	 */
	protected  $pods;

	/**
	 * CF_Custom_Fields_Pods constructor.
	 *
	 * @since 2.2.0
	 *
	 */
	public function __construct(  ){
		$this->files = array();

	}

	/**
	 * Add hooks for class
	 *
	 * @since 2.2.0
	 */
	public function add_hooks(){
		add_action( 'caldera_forms_file_added_to_media_library', array( $this, 'file_uploaded' ), 10, 2 );
		add_action( 'cf_custom_fields_post_save_meta_key_to_post_type', array( $this, 'save' ), 10, 4 );
		add_filter( 'cf_custom_fields_use_uploader', '__return_false' );
	}

	/**
	 * Remove hooks for class
	 *
	 * @since 2.2.0
	 */
	public function remove_hooks(){
		remove_action( 'caldera_forms_file_added_to_media_library', array( $this, 'file_uploaded' ), 10 );
		remove_action( 'cf_custom_fields_post_save_meta_key_to_post_type', array( $this, 'save' ), 10 );

	}

	/**
	 * Set pods object ot use for saving
	 *
	 * @since 2.2.0
	 *
	 * @param Pods $pods Pods object to save with
	 */
	public function set_pods( Pods $pods ){
		$this->pods = $pods;
	}

	/**
	 * Track uploads
	 *
	 * @since 2.1.0
	 *
	 * @uses "caldera_forms_file_added_to_media_library" action
	 *
	 * @param int $id Attatchment ID
	 * @param array $field Field ID
	 */
	public function file_uploaded( $id, $field ){
		$this->files[ $field[ 'ID' ] ] = $id;
	}

	/**
	 * Save file field properly using Pods
	 *
	 * @since 2.2.0
	 *
	 * @uses "cf_custom_fields_post_save_meta_key_to_post_type" action
	 *
	 * @param $value
	 * @param $slug
	 * @param $entry_id
	 * @param $field
	 */
	public function save( $value, $slug, $entry_id, $field ){
		if( is_object( $this->pods ) && isset( $this->files[ $field[ 'ID' ] ] ) ){
			$this->pods->save( $slug, array(
				'image_field'  => $this->files[ $field[ 'ID' ] ] ,
			), $entry_id );

		}

	}

}