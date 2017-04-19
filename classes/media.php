<?php


/**
 * Class CF_CF_Media
 */
class CF_CF_Media {


	public static function upload_handler_filter( $handler, $form, $field ){
		$e = Caldera_Forms::do_magic_tags( '{entry_id', null, $form );
		$e =  		$netry_id = Caldera_Forms::do_magic_tags( '{entry_id' );

		if( cf_custom_fields_has_pr_processor( $form, $e  ) ){

		}

		return $handler;

	}
}