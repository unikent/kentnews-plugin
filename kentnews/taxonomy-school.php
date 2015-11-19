<?php

add_action( 'init', 'kentnews_register_taxonomy_school');
add_action( 'school_add_form_fields', 'kentnews_school_taxonomy_add_new_meta_fields', 10, 2 );
add_action( 'school_edit_form_fields', 'kentnews_school_taxonomy_edit_meta_fields', 10, 2 );
add_action( 'edited_school', 'kentnews_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_school', 'kentnews_save_taxonomy_custom_meta', 10, 2 );
add_filter( 'manage_edit-school_columns', 'kentnews_school_columns', 10, 1);
add_filter( 'manage_school_custom_column', 'kentnews_manage_school_columns', 10, 3);


/**
 * Add a school taxonomy so that we can add schools to our posts.
 */
function kentnews_register_taxonomy_school() {

	$labels = array(
		'name' => _x( 'Schools and Departments', 'school' ),
		'singular_name' => _x( 'School', 'school' ),
		'search_items' => _x( 'Search Schools and Departments', 'school' ),
		'popular_items' => _x( 'Popular Schools and Departments', 'school' ),
		'all_items' => _x( 'All Schools and Departments', 'school' ),
		'parent_item' => _x( 'Parent School/Department', 'school' ),
		'parent_item_colon' => _x( 'Parent School/Department:', 'school' ),
		'edit_item' => _x( 'Edit School/Department', 'school' ),
		'update_item' => _x( 'Update School/Department', 'school' ),
		'add_new_item' => _x( 'Add New School/Department', 'school' ),
		'new_item_name' => _x( 'New School', 'school' ),
		'separate_items_with_commas' => _x( 'Separate Schools with commas', 'school' ),
		'add_or_remove_items' => _x( 'Add or remove Schools/Departments', 'school' ),
		'choose_from_most_used' => _x( 'Choose from most used Schools/Departments', 'school' ),
		'menu_name' => _x( 'Schools and Departments', 'school' ),
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

	register_taxonomy( 'school', array('post'), $args );
}


/**
 * Function to add custom fields (meta) to school taxonomy.
 */
function kentnews_school_taxonomy_add_new_meta_fields() {
	// this will add the custom meta field to the add new term page
	?>
	<div class="form-field">
		<label for="term_meta[short_name]"><?php _e( 'Short Name', 'school' ); ?></label>
		<input type="text" name="term_meta[short_name]" id="term_meta[short_name]" value="">
		<p class="description"><?php _e( 'Enter a short name for this school. E.g KBS for Kent Business School.','school' ); ?></p>
	</div>
	<?php
}

/**
 * Function to edit custom fields (meta) in school taxonomy.
 */
function kentnews_school_taxonomy_edit_meta_fields($term) {

	// put the term ID into a variable
	$t_id = $term->term_id;

	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" );
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[short_name]"><?php _e( 'Short Name', 'school' ); ?></label></th>
		<td>
			<input type="text" name="term_meta[short_name]" id="term_meta[short_name]" value="<?php echo esc_attr( $term_meta['short_name'] ) ? esc_attr( $term_meta['short_name'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a short name for this school. E.g KBS for Kent Business School.','school' ); ?></p>
		</td>
	</tr>
	<?php
}

/**
 * Function to specify custom colums in school taxonomy.
 */
function kentnews_school_columns($school_columns) {
	$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Name'),
		'short_name' => 'Short Name',
		'slug' => __('Slug'),
		'posts' => __('Count')
	);
	return $new_columns;
}

/**
 * Function to set custom colums in school taxonomy.
 */
function kentnews_manage_school_columns($out, $column_name, $term_id) {

	// retrieve the existing value(s) for this meta field. This returns an array
	$school_meta = get_option( "taxonomy_$term_id" );

	//$school = get_term($term_id, 'school');

	$column_value = '';

	switch ($column_name) {
		case 'short_name':
			$column_value = isset($school_meta['short_name']) ? $school_meta['short_name'] : '';
			break;
		default:
			break;
	}

	return $column_value;
}