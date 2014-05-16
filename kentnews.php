<?php
/*
	Plugin Name: KentNews
	Plugin URI: 
	Description: Kent's own news plugin. Adds 'Featured Academics' and 'Schools' taxonomies to our posts.
	Version: 0.0
	Author: Justice Addison
	Author URI: http://blogs.kent.ac.uk/webdev/
*/

class KentNews {
	
	/**
	 * Our constructor
	 */
	public function __construct() {

		// academics taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_academics' )  );
		add_action( 'academics_add_form_fields', array( $this, 'academics_taxonomy_add_new_meta_fields' ), 10, 2 );
		add_action( 'academics_edit_form_fields', array( $this, 'academics_taxonomy_edit_meta_fields' ), 10, 2 );
		add_action( 'edited_academics', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );  
		add_action( 'create_academics', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );

		add_action( 'init', array( $this, 'register_taxonomy_schools' )  );
	}

	/**
	 * Add a featured academics taxonomy so that we can add featured academics to our posts.
	 */
	function register_taxonomy_academics() {

	    $labels = array( 
	        'name' => _x( 'Featured Academics', 'academics' ),
	        'singular_name' => _x( 'Featured Academic', 'academics' ),
	        'search_items' => _x( 'Search Featured Academics', 'academics' ),
	        'popular_items' => _x( 'Popular Featured Academics', 'academics' ),
	        'all_items' => _x( 'All Featured Academics', 'academics' ),
	        'parent_item' => _x( 'Parent Featured Academic', 'academics' ),
	        'parent_item_colon' => _x( 'Parent Featured Academic:', 'academics' ),
	        'edit_item' => _x( 'Edit Featured Academic', 'academics' ),
	        'update_item' => _x( 'Update Featured Academic', 'academics' ),
	        'add_new_item' => _x( 'Add New Featured Academic', 'academics' ),
	        'new_item_name' => _x( 'New Featured Academic', 'academics' ),
	        'separate_items_with_commas' => _x( 'Separate Featured Academics with commas', 'academics' ),
	        'add_or_remove_items' => _x( 'Add or remove Featured Academics', 'academics' ),
	        'choose_from_most_used' => _x( 'Choose from most used Featured Academics', 'academics' ),
	        'menu_name' => _x( 'Featured Academics', 'academics' ),
	    );

	    $args = array( 
	        'labels' => $labels,
	        'public' => true,
	        'show_in_nav_menus' => true,
	        'show_ui' => true,
	        'show_tagcloud' => true,
	        'show_admin_column' => true,
	        'hierarchical' => false,
	        'rewrite' => true,
	        'meta_box_cb' => 'post_categories_meta_box', /*TODO: this creates a bug where. Fix it!*/
	        'query_var' => true,
	        /* TODO: find the right capabilities to use */
	        'capabilities' => array(
	        	'manage_terms' => 'manage_categories',
				'assign_terms' => 'manage_categories',
				'edit_terms' => 'manage_categories',
				'delete_terms' => 'manage_categories'
			)
	    );

	    register_taxonomy( 'academics', array('post'), $args );
	}

	/**
	 * Add a schools taxonomy so that we can add schools to our posts.
	 */
	function register_taxonomy_schools() {

	    $labels = array( 
	        'name' => _x( 'Schools', 'schools' ),
	        'singular_name' => _x( 'School', 'schools' ),
	        'search_items' => _x( 'Search Schools', 'schools' ),
	        'popular_items' => _x( 'Popular Schools', 'schools' ),
	        'all_items' => _x( 'All Schools', 'schools' ),
	        'parent_item' => _x( 'Parent School', 'schools' ),
	        'parent_item_colon' => _x( 'Parent School:', 'schools' ),
	        'edit_item' => _x( 'Edit School', 'schools' ),
	        'update_item' => _x( 'Update School', 'schools' ),
	        'add_new_item' => _x( 'Add New School', 'schools' ),
	        'new_item_name' => _x( 'New School', 'schools' ),
	        'separate_items_with_commas' => _x( 'Separate Schools with commas', 'schools' ),
	        'add_or_remove_items' => _x( 'Add or remove Schools', 'schools' ),
	        'choose_from_most_used' => _x( 'Choose from most used Schools', 'schools' ),
	        'menu_name' => _x( 'Schools', 'schools' ),
	    );

	    $args = array( 
	        'labels' => $labels,
	        'public' => true,
	        'show_in_nav_menus' => true,
	        'show_ui' => true,
	        'show_tagcloud' => true,
	        'show_admin_column' => true,
	        'hierarchical' => false,
	        'rewrite' => true,
	        'query_var' => true,
	        /* TODO: find the right capabilities to use */
	        'capabilities' => array(
	        	'manage_terms' => 'manage_categories',
				'assign_terms' => 'manage_categories',
				'edit_terms' => 'manage_categories',
				'delete_terms' => 'manage_categories'
			)
	    );

	    register_taxonomy( 'schools', array('post'), $args );
	}

	/**
	 * Function to add custom fields (meta) to academics taxonomy.
	 */
	function academics_taxonomy_add_new_meta_fields() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[url]"><?php _e( 'URL', 'academics' ); ?></label>
			<input type="text" name="term_meta[url]" id="term_meta[url]" value="">
			<p class="description"><?php _e( 'Enter a value for this field','academics' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Function to edit custom fields (meta) in academics taxonomy.
	 */
	function academics_taxonomy_edit_meta_fields($term) {
	 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); 
		?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[url]"><?php _e( 'URL', 'academics' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[url]" id="term_meta[url]" value="<?php echo esc_attr( $term_meta['url'] ) ? esc_attr( $term_meta['url'] ) : ''; ?>">
				<p class="description"><?php _e( 'Enter a value for this field','academics' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save data from custom taxonomy fields (meta).
	 */
	function save_taxonomy_custom_meta( $term_id ) {
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
	}
	

}

$compuses = new KentNews();