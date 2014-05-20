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

		// academic taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_academic' )  );
		add_action( 'academic_add_form_fields', array( $this, 'academic_taxonomy_add_new_meta_fields' ), 10, 2 );
		add_action( 'academic_edit_form_fields', array( $this, 'academic_taxonomy_edit_meta_fields' ), 10, 2 );
		add_action( 'edited_academic', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );  
		add_action( 'create_academic', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );

		add_action( 'init', array( $this, 'register_taxonomy_school' )  );
	}

	/**
	 * Add a featured academic taxonomy so that we can add featured academics to our posts.
	 */
	function register_taxonomy_academic() {

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
	 * Add a school taxonomy so that we can add schools to our posts.
	 */
	function register_taxonomy_school() {

	    $labels = array( 
	        'name' => _x( 'Schools', 'school' ),
	        'singular_name' => _x( 'School', 'school' ),
	        'search_items' => _x( 'Search Schools', 'school' ),
	        'popular_items' => _x( 'Popular Schools', 'school' ),
	        'all_items' => _x( 'All Schools', 'school' ),
	        'parent_item' => _x( 'Parent School', 'school' ),
	        'parent_item_colon' => _x( 'Parent School:', 'school' ),
	        'edit_item' => _x( 'Edit School', 'school' ),
	        'update_item' => _x( 'Update School', 'school' ),
	        'add_new_item' => _x( 'Add New School', 'school' ),
	        'new_item_name' => _x( 'New School', 'school' ),
	        'separate_items_with_commas' => _x( 'Separate Schools with commas', 'school' ),
	        'add_or_remove_items' => _x( 'Add or remove Schools', 'school' ),
	        'choose_from_most_used' => _x( 'Choose from most used Schools', 'school' ),
	        'menu_name' => _x( 'Schools', 'school' ),
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
	 * Function to add custom fields (meta) to academic taxonomy.
	 */
	function academic_taxonomy_add_new_meta_fields() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[url]"><?php _e( 'URL', 'academic' ); ?></label>
			<input type="text" name="term_meta[url]" id="term_meta[url]" value="">
			<p class="description"><?php _e( 'Enter a value for this field','academic' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Function to edit custom fields (meta) in academic taxonomy.
	 */
	function academic_taxonomy_edit_meta_fields($term) {
	 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); 
		?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[url]"><?php _e( 'URL', 'academic' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[url]" id="term_meta[url]" value="<?php echo esc_attr( $term_meta['url'] ) ? esc_attr( $term_meta['url'] ) : ''; ?>">
				<p class="description"><?php _e( 'Enter a value for this field','academic' ); ?></p>
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

$news = new KentNews();



/**
 * restrict tags editing in a post
*/
// remove the old tag box
function remove_default_tags_box() {
    remove_meta_box('tagsdiv-post_tag', 'post', 'side');
}
add_action( 'admin_menu', 'remove_default_tags_box' );


// add the new tag box
function add_custom_tags_box() {
    add_meta_box('tagsdiv-post_tag', 'Tags', 'custom_post_tags_meta_box', 'post', 'side', 'low', array( 'taxonomy' => 'tag' ));
}

add_action('add_meta_boxes', 'add_custom_tags_box');


/**
 * Display new tag box
 *
 * @param object $post
 */
function custom_post_tags_meta_box($post, $box) {
	$defaults = array('taxonomy' => 'post_tag');
	if ( !isset($box['args']) || !is_array($box['args']) )
		$args = array();
	else
		$args = $box['args'];
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	$tax_name = esc_attr('post_tag');
	$taxonomy = get_taxonomy('post_tag');
?>
<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	<div class="jaxtag">
		<div class="nojs-tags hide-if-js">
			<p>Sorry you must enable javascript to add tags.</p>
			<textarea name="<?php echo "tax_input[$tax_name]"; ?>" class="the-tags" id="tax-input-<?php echo $tax_name; ?>"></textarea>
		</div>
	</div>
	<div class="tagchecklist" style="padding-top:20px;"></div>
</div>
<div style="padding-top:20px;"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>">Add a tag</a></div>

<?php
}


