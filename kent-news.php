<?php

require_once('kentnews/taxonomy-general.php');
require_once('kentnews/taxonomy-tag.php');
require_once('kentnews/taxonomy-school.php');
require_once('kentnews/taxonomy-academic.php');

require_once('../../../vendor/haddowg/metamaterial/MetaMaterial.php');
require_once('../../../vendor/haddowg/metamaterial/MM_Loop.php');
require_once('../../../vendor/haddowg/metamaterial/MM_Metabox.php');

require_once('kentnews/post-metaboxes.php');

class KentNews {

	/**
	 * Our constructor
	 */
	public function __construct() {


		// Custom tag taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_tag' )  );

		// coverage taxonomy
		add_action( 'init', array( $this, 'register_taxonomy_coverage' )  );
		add_action( 'coverage_add_form_fields', array( $this, 'coverage_taxonomy_add_new_meta_fields' ), 10, 2 );
		add_action( 'coverage_edit_form_fields', array( $this, 'coverage_taxonomy_edit_meta_fields' ), 10, 2 );
		add_action( 'edited_coverage', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );  
		add_action( 'create_coverage', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
		add_filter( 'manage_edit-coverage_columns', array( $this, 'coverage_columns'), 10, 1);
		add_filter( 'manage_coverage_custom_column', array( $this, 'manage_coverage_columns'), 10, 3);

		// primary category
		add_action('admin_init', array($this, 'primary_category_init'));
		add_action('save_post', array($this, 'save_category_details'));

		//add all custom fields
		$this->add_post_custom_fields_to_api();
		$this->add_media_custom_fields_to_api();
		$this->add_term_custom_fields_to_api();

		// Authenticate API
		add_action('dispatch_api',  array($this, 'authenticate_api'));

		// Force frontend redirect from news backend public site
		add_action('get_header',  array($this, 'redirect_frontend'));

		// Enable custom preview
		add_filter('preview_post_link',  array($this, 'front_end_preview'));
	}

	/**
	 * If a single article is being viewed, auto redirect to front end version
	 */
	function redirect_frontend(){
		if(is_single() && isset($_GET['preview_id'])){
			return wp_redirect( $this->front_end_preview() );
		}
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
	function front_end_preview($link='') {
		
		// Get post ID & STATUS
		$id = get_the_ID();
		$status = get_post_status($id);

		// If published post, use get autosave method to find "preview" id.
		// Else just use id, if this is a draft post already
	 	if($status === 'publish'){
	 		$preview = wp_get_post_autosave($id);
	 		// if preview id found, use it.
	 		if($preview) $id = $preview->ID;
	 	}	

	 	// Build preview key
	 	$preview_key = md5($id);

		// Build link (frontend url is configured in .env file)
		return WP_FRONTEND."preview/{$id}?preview_key={$preview_key}&time=".time();
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
	 * Add a media coverage taxonomy so that we can add media coverage to our posts.
	 */
	function register_taxonomy_coverage() {

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

		register_taxonomy( 'coverage', array('post'), $args );
	}





	/**
	 * Function to add custom fields (meta) to coverage taxonomy.
	 */
	function coverage_taxonomy_add_new_meta_fields() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[url]"><?php _e( 'URL', 'coverage' ); ?></label>
			<input type="text" name="term_meta[url]" id="term_meta[url]" value="">
			<p class="description"><?php _e( 'Enter a URL for this coverage','coverage' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Function to edit custom fields (meta) in coverage taxonomy.
	 */
	function coverage_taxonomy_edit_meta_fields($term) {

		// put the term ID into a variable
		$t_id = $term->term_id;

		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); 
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[url]"><?php _e( 'URL', 'coverage' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[url]" id="term_meta[url]" value="<?php echo esc_attr( $term_meta['url'] ) ? esc_attr( $term_meta['url'] ) : ''; ?>">
				<p class="description"><?php _e( 'Enter a URL for this coverage','coverage' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Function to specify custom colums in coverage taxonomy.
	 */
	function coverage_columns($coverage_columns) {
		// unset($coverage_columns['description']);
		// return $coverage_columns;

		$new_columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Name'),
			'url' => 'URL',
			'slug' => __('Slug'),
			'posts' => __('Count')
		);
		return $new_columns;
	}

	/**
	 * Function to set custom colums in coverage taxonomy.
	 */
	function manage_coverage_columns($out, $column_name, $term_id) {

		// retrieve the existing value(s) for this meta field. This returns an array
		$coverage_meta = get_option( "taxonomy_$term_id" ); 

		//$coverage = get_term($term_id, 'coverage');

		$column_value = '';

		switch ($column_name) {
			case 'url': 
				$column_value = isset($coverage_meta['url']) ? $coverage_meta['url'] : ''; 
				break;
			default:
				break;
		}
		
		return $column_value;    
	}


	/**
	 * Add extra data to media in the api.
	 */
	function add_media_custom_fields_to_api(){
		add_filter( 'thermal_post_entity', function($data, &$post, $state ) {
			if( $state === 'read' ){
				$featured_image_id = (isset($data->featured_image) && !empty($data->featured_image)) ? $data->featured_image['id'] : false;
				foreach ($data->media as &$media_item) {

					if ($featured_image_id) {
						if ($media_item['id'] === $featured_image_id) {
							$data->featured_image = $media_item;
						}
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

				$data->meta->custom_fields = $custom_fields;
			}
			return $data;
		}, 10, 3);
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
		$custom_fields_additional = array();

		foreach ($custom_fields_raw as $fieldName => $content) {
			// remove any fields that start with an underscore, as it's a private one
			if (substr((string)$fieldName, 0, 1) == '_') continue;

			$custom_fields_additional[$fieldName] = KentNews::north_cast_api_data($content[0]);

		}

		return (object) $custom_fields_additional;
	}

	static function north_cast_api_data($content) {
		if (is_numeric($content)) $content = intval($content);
		else {
			$content = maybe_unserialize($content);
			if (is_array($content)) {
				// make sure that integers are represented as such, instead of str
				foreach ($content as $fn => &$c) {
					if (is_numeric($c)) $c = intval($c);
				}
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


