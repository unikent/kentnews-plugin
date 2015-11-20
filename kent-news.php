<?php

require_once('kentnews/taxonomy-general.php');
require_once('kentnews/taxonomy-tag.php');
require_once('kentnews/taxonomy-school.php');
require_once('kentnews/taxonomy-academic.php');
require_once('kentnews/taxonomy-media-source.php');

require_once('kentnews/post-metaboxes.php');

require_once('kentnews/frontend_redirect.php');



add_action( 'init', 'kentnews_register_taxonomy_coverage' );
/**
 * @deprecated remove once migrated
 */
function kentnews_register_taxonomy_coverage() {

	$labels = array(
		'name' => _x( 'Media Coverage', 'coverage' ),
		'singular_name' => _x( 'Media Coverage', 'coverage' ),
		'search_items' => _x( 'Search Media Coverage', 'coverage' ),
		'popular_items' => _x( 'Popular Media Coverage', 'coverage' ),
		'all_items' => _x( 'All Media Coverage', 'coverage' ),
		'parent_item' => _x( 'Parent Media Coverage', 'coverage' ),
		'parent_item_colon' => _x( 'Parent Media Coverage:', 'coverage' ),
		'edit_item' => _x( 'Edit Media Coverage', 'coverage' ),
		'update_item' => _x( 'Update Media Coverage', 'coverage' ),
		'add_new_item' => _x( 'Add New Media Coverage', 'coverage' ),
		'new_item_name' => _x( 'New Media Coverage', 'coverage' ),
		'separate_items_with_commas' => _x( 'Separate Media Coverage with commas', 'coverage' ),
		'add_or_remove_items' => _x( 'Add or remove Media Coverage', 'coverage' ),
		'choose_from_most_used' => _x( 'Choose from most used Media Coverage', 'coverage' ),
		'menu_name' => _x( 'Media Coverage', 'coverage' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_in_nav_menus' => false,
		'show_ui' => false,
		'show_tagcloud' => false,
		'show_admin_column' => false,
		'hierarchical' => true,
		'rewrite' => false,
		'meta_box_cb' => 'post_categories_meta_box', /*TODO: this creates a bug when heirarchical is false. Fix it!*/
		'query_var' => false,
		/* TODO: find the right capabilities to use */
		'capabilities' => array(
			'manage_terms' => 'manage_categories',
			'assign_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories'
		)
	);

	register_taxonomy( 'coverage', array('post'), $args );
}


class KentNews {

	/**
	 * Our constructor
	 */
	public function __construct() {

		//$this->add_media_custom_fields_to_api();
		$this->add_term_custom_fields_to_api();

		// Authenticate API

	}

	/**
	 * Add term custom fields to the api.
	 */
	function add_term_custom_fields_to_api(){
		add_filter( 'thermal_term_entity', function($data, &$term, $state ) {
			if( $state === 'read' ){
				$term_id = $term->term_id;

				$term_meta = get_option( "taxonomy_$term_id" );

				if(!empty($term_meta)) {
					$data->meta = (object)$term_meta;
				}
			}
			return $data;
		}, 10, 3);
	}
	
}

$news = new KentNews();


