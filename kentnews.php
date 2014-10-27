<?php
/*
	Plugin Name: KentNews
	Plugin URI: 
	Description: Kent's own news plugin. Adds 'Featured Academics' and 'Schools' taxonomies to our posts.
	Version: 0.0
	Depends: Thermal API
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
		add_filter( 'manage_edit-academic_columns', array( $this, 'academic_columns'), 10, 1); 

		// school taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_school' )  );
		add_action( 'school_add_form_fields', array( $this, 'school_taxonomy_add_new_meta_fields' ), 10, 2 );
		add_action( 'school_edit_form_fields', array( $this, 'school_taxonomy_edit_meta_fields' ), 10, 2 );
		add_action( 'edited_school', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );  
		add_action( 'create_school', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
		add_filter( 'manage_edit-school_columns', array( $this, 'school_columns'), 10, 1); 
		add_filter( 'manage_school_custom_column', array( $this, 'manage_school_columns'), 10, 3);

		// Custom tag taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_tag' )  );

		// primary category
		add_action('admin_init', array($this, 'primary_category_init'));
		add_action('save_post', array($this, 'save_category_details'));

		//add all custom fields
		$this->add_post_custom_fields_to_api();
		$this->add_media_custom_fields_to_api();

		// Authenticate API
		add_action('dispatch_api',  array($this, 'authenticate_api'));

		// Enable custom preview
		add_filter('preview_post_link',  array($this, 'front_end_preview'));
	}

	/**
	 * validate access to thermal API's
	 *
	 */
	function authenticate_api(){

		if(!defined("API_KEY") || API_KEY === '') die("Disabled. Auth key not set.");

		// Unless auth key is passed, disallow any connection to Thermal
		if(!isset($_GET['api_key']) || $_GET['api_key'] !== API_KEY){
			die("Authorization required.");
		}

		// If key is valid, allow full api access
		// Uses additional filter "is_user_logged_in" in wordpress-saml 

		// Since this bit is somewhat weird, whats happening is:

		// First is_user_logged_in pass(s) triggers "false" (as no id) - this doesn't matter as API 
		// access is allowed to none users (this is just a general check).
		// Second is_user_logged_in pass (once API has been invoked and this code has run), requires
		// additional permissions. These are granted by setting current user to 1.
		// Since SAML imposes additional checks, to avoid it kicking the session out (and triggering wp_logout)
		// when we don't have valid SAML tokens, additionally override the "is_user_logged_in" via a filter
		// of the same name.

		// This code can only be initiated during API use & is read-only.
		add_filter("is_user_logged_in", function(){
		 	return ($_GET['api_key'] === API_KEY); 
		});
		wp_set_current_user(1);
	}

	/**
	 * Rewrite preview link to point to news front end
	 */
	function front_end_preview($link) {
		
		// Get post ID & STATUS
		$id = get_the_ID();
		$staus = get_post_status($id);

		// If published post, use get autosave method to find "preview" id.
		// Else just use id, if this is a draft post already
	 	if($staus === 'publish'){
	 		$preview = wp_get_post_autosave($id);
	 		$preview_id = $preview->ID;
	 		
	 	}else{
	 		$preview_id = $id;
	 	}

		// Build link (frontend url is configured in evn file)
		return WP_FRONTEND."preview/".$preview_id."?preview&time=".time();
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
	 * Add a custom Tag taxonomy so that we can permission tag creation.
	 */
	function register_taxonomy_tag() {

		$labels = array( 
			'name' => _x( 'Tags', 'tag' ),
			'singular_name' => _x( 'tag', 'tag' ),
			'search_items' => _x( 'Search Tag', 'tag' ),
			'popular_items' => _x( 'Popular Tag', 'tag' ),
			'all_items' => _x( 'All Tag', 'tag' ),
			'parent_item' => _x( 'Parent tag', 'tag' ),
			'parent_item_colon' => _x( 'Parent tag:', 'tag' ),
			'edit_item' => _x( 'Edit tag', 'tag' ),
			'update_item' => _x( 'Update tag', 'tag' ),
			'add_new_item' => _x( 'Add New tag', 'tag' ),
			'new_item_name' => _x( 'New tag', 'tag' ),
			'separate_items_with_commas' => _x( 'Separate Tag with commas', 'tag' ),
			'add_or_remove_items' => _x( 'Add or remove Tag', 'tag' ),
			'choose_from_most_used' => _x( 'Choose from most used Tag', 'tag' ),
			'menu_name' => _x( 'Tags', 'tag' ),
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
			'meta_box_cb' => 'predefined_tags_meta_box',
			'query_var' => true,
			/* TODO: find the right capabilities to use */
			'capabilities' => array(
				'manage_terms' => 'manage_categories',
				'assign_terms' => 'manage_categories',
				'edit_terms' => 'manage_categories',
				'delete_terms' => 'manage_categories'
				)
			);

		register_taxonomy( 'tag', array('post'), $args );
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
			<p class="description"><?php _e( 'Enter a URL for this academic','academic' ); ?></p>
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
				<p class="description"><?php _e( 'Enter a URL for this academic','academic' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Function to specify custom colums in academic taxonomy.
	 */
	function academic_columns($academic_columns) {
		unset($academic_columns['description']);
		return $academic_columns;
	}

	/**
	 * Function to add custom fields (meta) to school taxonomy.
	 */
	function school_taxonomy_add_new_meta_fields() {
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
	function school_taxonomy_edit_meta_fields($term) {

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
	function school_columns($school_columns) {
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
	function manage_school_columns($out, $column_name, $term_id) {

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

	/**
	 * Add extra data to media in the api.
	 */
	function add_media_custom_fields_to_api(){
		add_filter( 'thermal_post_entity', function($data, &$post, $state ) {
			if( $state === 'read' ){
				foreach ($data->media as &$media_item) {
					$media_post = get_post($media_item['id']);

					//add more data
					$media_item['name'] = $media_post->post_name;
					$media_item['description'] = $media_post->post_content;
					$media_item['caption'] = $media_post->post_excerpt;
					$media_item['title'] = $media_post->post_title;

					// add custom fields
					$custom_fields = KentNews::get_custom_fields_from_post($media_post->ID);
					foreach ($custom_fields->additional as $field_name => $field_value) {
						$media_item[$field_name] = $field_value;
					}

				}
			}
			return $data;
		}, 10, 3);
	}

	/**
	 * Add post custom fields to the api.
	 */
	function add_post_custom_fields_to_api(){
		add_filter( 'thermal_post_entity', function($data, &$post, $state ) {
			if( $state === 'read' ){

				$custom_fields = KentNews::get_custom_fields_from_post($post->ID);

				$data->meta->sections = $custom_fields->sections;
				$data->meta->custom_fields = $custom_fields->additional;
			}
			return $data;
		}, 10, 3);
	}

	/**
	 * Retrieve all custom fields for a given post
	 * reference https://gist.github.com/fardog/9356458
	 */
	static function get_custom_fields_from_post($id){
		$custom_fields = new StdClass;

		// get all of the post's metadata
		$custom_fields_raw = (object) get_post_meta($id);

		// uncomment the following to include the raw data for testing
		//$data->meta->custom_fields_raw = $custom_fields_raw;

		// create an object for storing all the post data
		$sections = new StdClass;
		$custom_fields_additional = array();

		foreach ($custom_fields_raw as $fieldName => $content) {
			// remove any fields that start with an underscore, as it's a private one
			if (substr((string)$fieldName, 0, 1) == '_') continue;

			$custom_fields_additional[$fieldName] = KentNews::north_cast_api_data($content[0]);

		}

		$custom_fields->sections = $sections;
		$custom_fields->additional = (object) $custom_fields_additional;

		return $custom_fields;
	}

	static function north_cast_api_data($content) {
		if (is_numeric($content)) $content = intval($content);
		else {
			$unserialized_content = @unserialize($content);
			// we got serialized content
			if ($unserialized_content !== false) {
				// make sure that integers are represented as such, instead of str
				foreach ($unserialized_content as $fn => &$c) {
					if (is_numeric($c)) $c = intval($c);
				}
				$content = $unserialized_content;
			}
		}
		return $content;
	}

	function primary_category_init() {
		add_meta_box("category-meta", "Default Category", array($this, "primary_category"), "post", "side", "low");
	}

	function primary_category() {
		global $post;
		$catKey = "primary_category";
		$catSlug = get_post_meta($post->ID, $catKey, true);

		$selected = "";
		if (!empty($catSlug)) {
			$catTerms = get_term_by('slug', $catSlug, 'category');
			$categoryID = $catTerms->term_id;
			$selected = "&selected=" . $categoryID;
		}

		?>
		<label>Category:</label>
		<?php

		wp_dropdown_categories('show_option_none=Select category&name=primary_category&hide_empty=0' . $selected);
	}

	function save_category_details($post_id) {
		global $post;
		$catID = isset($_POST["primary_category"]) ? $_POST["primary_category"] : "";

		if(!empty($catID)) {
			$catTerms = get_term_by('term_id', $catID, 'category');

			if(isset($catTerms->slug)){
				$catName = $catTerms->slug;
				update_post_meta($post->ID, "primary_category", $catName);
			}
		}
	}
	
}

$news = new KentNews();

/**
 * Remove original tags admin box & admin page
*/
function remove_default_tags_box() {
	remove_meta_box('tagsdiv-post_tag', 'post', 'side');
	remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');

}
add_action( 'admin_menu', 'remove_default_tags_box' );

/**
 * Add meta box for editing predefined tags in a post
 *
 */
function predefined_tags_meta_box($post, $box) {
        $defaults = array('taxonomy' => 'post_tag');
        if ( !isset($box['args']) || !is_array($box['args']) )
                $args = array();
        else
                $args = $box['args'];
        extract( wp_parse_args($args, $defaults), EXTR_SKIP );


        $tax_name = esc_attr($taxonomy);
        $taxonomy = get_taxonomy($taxonomy);
        $disabled = !current_user_can($taxonomy->cap->assign_terms) ? 'disabled="disabled"' : '';
?>
<div class="tagsdiv" id="<?php echo $tax_name; ?>">
        <div class="jaxtag">
        <div class="nojs-tags hide-if-js">
        <p><?php echo $taxonomy->labels->add_or_remove_items; ?></p>
        <textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php echo $disabled; ?>><?php echo get_terms_to_edit( $post->ID, $tax_name ); // textarea_escaped by esc_attr() ?></textarea></div>
        <?php if ( current_user_can($taxonomy->cap->assign_terms) ) : ?>
        <div class="ajaxtag hide-if-no-js">
                <label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $box['title']; ?></label>
                <div class="taghint"><?php echo $taxonomy->labels->add_new_item; ?></div>
                <p>

                <?php   
                // Generate select list of all defined tags. Then use js to populate a hidden tag name box in order to keep the WP javascript working happily.
                // Is probably bipassable if someone "REALLLY" wanted, but this is a staff only system anyway.
                // tags can now be defined from tags menu page only.
                $tag_option = get_terms($tax_name, array('hide_empty' => 0,'orderby' => 'name', 'hierarchical' => 0));  ?>
                <select id="tag-selector" class="postform" tabindex="3" style='width:70%;' onchange="document.getElementById('new-tag-<?php echo $tax_name; ?>').value = this.options[this.selectedIndex].value" >
					<option value="-1" >— Tag — </option>
					<?php foreach($tag_option as $tag): ?>
						<option class="level-0" value="<?php echo $tag->name; ?>"><?php echo $tag->name; ?></option>
					<?php endforeach; ?>
				</select>
                <input type="hidden" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" value="" />

                <input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" tabindex="3" /></p>
        </div>
        <p class="howto"><?php echo esc_attr( $taxonomy->labels->separate_items_with_commas ); ?></p>
        <?php endif; ?>
        </div>
        <div class="tagchecklist"></div>
</div>
<?php if ( current_user_can($taxonomy->cap->assign_terms) ) : ?>
<p class="hide-if-no-js"><a href="#titlediv" class="tagcloud-link" id="link-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->choose_from_most_used; ?></a></p>
<?php endif; ?>
<?php
}
