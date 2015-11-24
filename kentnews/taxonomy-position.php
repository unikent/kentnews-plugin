<?php

add_action( 'init', 'kentnews_register_taxonomy_position');
/**
 * Add a position taxonomy so that we can add positions to our posts.
 */
function kentnews_register_taxonomy_position() {

	$labels = array(
		'name' => _x( 'Media Sources', 'position' ),
		'singular_name' => _x( 'Position', 'position' ),
		'search_items' => _x( 'Search Positions', 'position' ),
		'popular_items' => _x( 'Popular Positions', 'position' ),
		'all_items' => _x( 'All Positions', 'position' ),
		'parent_item' => _x( 'Parent Position', 'position' ),
		'parent_item_colon' => _x( 'Parent Position:', 'position' ),
		'edit_item' => _x( 'Edit Position', 'position' ),
		'update_item' => _x( 'Update Position', 'position' ),
		'add_new_item' => _x( 'Add New Position', 'position' ),
		'new_item_name' => _x( 'New Position', 'position' ),
		'separate_items_with_commas' => _x( 'Separate Positions with commas', 'position' ),
		'add_or_remove_items' => _x( 'Add or remove Positions', 'position' ),
		'choose_from_most_used' => _x( 'Choose from most used Positions', 'position' ),
		'menu_name' => _x( 'Positions', 'position' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => false,
		'show_ui' => false,
		'show_in_menu' => true,
		'show_in_quick_edit' =>false,
		'show_tagcloud' => false,
		'show_admin_column' => false,
		'hierarchical' => false,
		'rewrite' => true,
		'query_var' => true,
		'meta_box_cb'=>function(){},
		'capabilities' => array(
			'manage_terms' => 'manage_categories',
			'assign_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories'
		)
	);

	register_taxonomy( 'position', array('post'), $args );

}

