<?php

add_action( 'init', 'kentnews_register_taxonomy_media_source');
add_action( 'media_source_add_form_fields', 'kentnews_media_source_taxonomy_add_new_meta_fields', 10, 2 );
add_action( 'media_source_edit_form_fields', 'kentnews_media_source_taxonomy_edit_meta_fields', 10, 2 );
add_action( 'edited_media_source', 'kentnews_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_media_source', 'kentnews_save_taxonomy_custom_meta', 10, 2 );

/**
 * Add a media_source taxonomy so that we can add media_sources to our posts.
 */
function kentnews_register_taxonomy_media_source() {

	$labels = array(
		'name' => _x( 'Media Sources', 'media_source' ),
		'singular_name' => _x( 'Media Source', 'media_source' ),
		'search_items' => _x( 'Search Media Sources', 'media_source' ),
		'popular_items' => _x( 'Popular Media Sources', 'media_source' ),
		'all_items' => _x( 'All Media Sources', 'media_source' ),
		'parent_item' => _x( 'Parent Media Source', 'media_source' ),
		'parent_item_colon' => _x( 'Parent Media Source:', 'media_source' ),
		'edit_item' => _x( 'Edit Media Source', 'media_source' ),
		'update_item' => _x( 'Update Media Source', 'media_source' ),
		'add_new_item' => _x( 'Add New Media Source', 'media_source' ),
		'new_item_name' => _x( 'New Media Source', 'media_source' ),
		'separate_items_with_commas' => _x( 'Separate Media Sources with commas', 'media_source' ),
		'add_or_remove_items' => _x( 'Add or remove Media Sources', 'media_source' ),
		'choose_from_most_used' => _x( 'Choose from most used Media Sources', 'media_source' ),
		'menu_name' => _x( 'Media Sources', 'media_source' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'show_in_nav_menus' => false,
		'show_ui' => true,
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

	register_taxonomy( 'media_source', array('post'), $args );
}


/**
 * Function to add custom fields (meta) to media_source taxonomy.
 */
function kentnews_media_source_taxonomy_add_new_meta_fields() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[url]"><?php _e( 'URL', 'media_source' ); ?></label>
		<input type="text" name="term_meta[url]" id="term_meta[url]" value="">
		<p class="description"><?php _e( 'Enter a URL for this media source, exclude https(s) etc.','media_source' ); ?></p>
	</div>
	<?php
}

/**
 * Function to edit custom fields (meta) in media_source taxonomy.
 */
function kentnews_media_source_taxonomy_edit_meta_fields($term) {

	// put the term ID into a variable
	$t_id = $term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[url]"><?php _e( 'URL', 'media_source' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[url]" id="term_meta[url]" value="<?php echo esc_attr( $term_meta['url'] ) ? esc_attr( $term_meta['url'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a URL for this media source, exclude https(s) etc.','media_source' ); ?></p>
		</td>
	</tr>
	<?php
}