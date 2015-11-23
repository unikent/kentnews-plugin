<?php

/**
 * Save data from custom taxonomy fields (meta).
 */
function kentnews_save_taxonomy_custom_meta( $term_id , $taxonomy) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
	do_action('updated_taxonomy_term_'.$taxonomy);
}


function kentnews_add_term_meta_to_api_output($data, &$term, $state ) {
	if( $state === 'read' ){
		$term_id = $term->term_id;

		$term_meta = get_option( "taxonomy_$term_id" );

		if(!empty($term_meta)) {
			$data->meta = (object)$term_meta;
		}
	}
	return $data;
}
add_filter( 'thermal_term_entity', 'kentnews_add_term_meta_to_api_output', 10, 3);


add_action('updated_taxonomy_term_meta_media_source','kentnews_set_media_source_map');