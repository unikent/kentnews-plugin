<?php

add_action( 'init', 'kentnews_register_taxonomy_academic');
add_action( 'academic_add_form_fields', 'kentnews_academic_taxonomy_add_new_meta_fields', 10, 2 );
add_action( 'academic_edit_form_fields', 'kentnews_academic_taxonomy_edit_meta_fields', 10, 2 );
add_action( 'edited_academic', 'kentnews_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_academic', 'kentnews_save_taxonomy_custom_meta', 10, 2 );
add_filter( 'manage_edit-academic_columns', 'kentnews_academic_columns', 10, 1);


/**
 * Add a featured academic taxonomy so that we can add featured academics to our posts.
 */
function kentnews_register_taxonomy_academic() {

	$labels = array(
		'name' => _x( 'Featured Academics', 'academic' ),
		'singular_name' => _x( 'Featured Academic', 'academic' ),
		'search_items' => _x( 'Search Featured Academics', 'academic' ),
		'popular_items' => _x( 'Popular Featured Academics', 'academic' ),
		'all_items' => _x( 'All Featured Academics', 'academic' ),
		'parent_item' => _x( 'Parent Featured Academic', 'academic' ),
		'parent_item_colon' => _x( 'Parent Featured Academic:', 'academic' ),
		'edit_item' => _x( 'Edit Featured Academic', 'academic' ),
		'update_item' => _x( 'Update Featured Academic', 'academic' ),
		'add_new_item' => _x( 'Add New Featured Academic', 'academic' ),
		'new_item_name' => _x( 'New Featured Academic', 'academic' ),
		'separate_items_with_commas' => _x( 'Separate Featured Academics with commas', 'academic' ),
		'add_or_remove_items' => _x( 'Add or remove Featured Academics', 'academic' ),
		'choose_from_most_used' => _x( 'Choose from most used Featured Academics', 'academic' ),
		'menu_name' => _x( 'Featured Academics', 'academic' ),
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_nav_menus' => true,
		'show_ui' => true,
		'show_tagcloud' => true,
		'show_admin_column' => true,
		'hierarchical' => true,
		'rewrite' => true,
		'meta_box_cb' => 'post_categories_meta_box', /*TODO: this creates a bug when heirarchical is false. Fix it!*/
		'query_var' => true,
		/* TODO: find the right capabilities to use */
		'capabilities' => array(
			'manage_terms' => 'manage_categories',
			'assign_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories'
		)
	);

	register_taxonomy( 'academic', array('post'), $args );
}

/**
 * Function to add custom fields (meta) to academic taxonomy.
 */
function kentnews_academic_taxonomy_add_new_meta_fields() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[url]"><?php _e( 'URL', 'academic' ); ?></label>
		<input type="text" name="term_meta[url]" id="term_meta[url]" value="">
		<p class="description"><?php _e( 'Enter a URL for this academic','academic' ); ?></p>
	</div>
	<?php
}

/**
 * Function to edit custom fields (meta) in academic taxonomy.
 */
function kentnews_academic_taxonomy_edit_meta_fields($term) {

	// put the term ID into a variable
	$t_id = $term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[url]"><?php _e( 'URL', 'academic' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[url]" id="term_meta[url]" value="<?php echo esc_attr( $term_meta['url'] ) ? esc_attr( $term_meta['url'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a URL for this academic','academic' ); ?></p>
		</td>
	</tr>
	<?php
}

/**
 * Function to specify custom colums in academic taxonomy.
 */
function kentnews_academic_columns($academic_columns) {
	unset($academic_columns['description']);
	return $academic_columns;
}