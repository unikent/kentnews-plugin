<?php
use HaddowG\MetaMaterial\MM_Metabox;

MM_Metabox::getInstance('introtext', array(
	'title'=> 'IntroText',
	'context'=>'after_title',
	'lock' =>true,
	'view' => MM_Metabox::VIEW_ALWAYS_OPEN,
	'mode' => HaddowG\MetaMaterial\Metamaterial::STORAGE_MODE_EXTRACT,
	'template'=> dirname(__FILE__).'/metaboxes/introtext.php'
));

MM_Metabox::getInstance('postmeta', array(
	'title'=> 'Additional Post Info',
	'view' => MM_Metabox::VIEW_ALWAYS_OPEN,
	'lock' =>true,
	'mode' => HaddowG\MetaMaterial\Metamaterial::STORAGE_MODE_EXTRACT,
	'template'=> dirname(__FILE__).'/metaboxes/postmeta.php',
	'save_filter'=>'kentnews_postmeta_save_filter'
));


function kentnews_postmeta_save_filter($meta, $post_id, $is_ajax){

	if($meta['primary_category'] == -1){
		unset($meta['primary_category']);
	}else{
		$term = get_term($meta['primary_category'],'category');
		$meta['primary_category'] = $term->slug;
	}

	if(isset($meta['coverage']) && !empty($meta['coverage'])){

		$sources = get_transient('media_sources_map');

		if($sources === false){
			kentnews_set_media_source_map();
		}

		foreach($meta['coverage'] as &$item){

			foreach($sources as $source=>$url){
				if(strpos($item['url'], $url) !== false) {
					$item['source'] = $source;
					break;
				}
			}

			if(empty($item['source'])){
				$item['source'] = parse_url($item['url'],PHP_URL_HOST);
			}

		}
	}

	return $meta;
}

function kentnews_set_media_source_map(){
	set_transient('media_sources_map',kentnews_build_media_source_map(),0);
}

function kentnews_build_media_source_map(){

	$sources=array();
	$media_sources = get_terms('media_source',array('hide_empty'=>false));

	foreach($media_sources as $source){

		$meta = get_option('taxonomy_'.$source->term_id);
		if(!empty($meta) && isset($meta['url'])) {
			$sources[$source->name] = $meta['url'];
		}
	}

	return $sources;
}

function kentnews_enqueue_post_metabox_styles($hook_suffix) {
	if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
		wp_enqueue_style( 'post_metaboxes', content_url() . '/mu-plugins/kentnews/metaboxes/css/post_metaboxes.css');
  }
}
add_action( 'admin_enqueue_scripts', 'kentnews_enqueue_post_metabox_styles' );


/**
 * Add post custom fields to the api.
 */
function kentnews_add_post_meta_to_api($data, &$post, $state ) {
	if( $state === 'read' ){

		$intro = MM_Metabox::getInstance('introtext')->meta($post->ID);
		$postmeta = MM_Metabox::getInstance('postmeta')->meta($post->ID);

		$custom_fields = array_merge($intro,$postmeta);

		$data->meta->custom_fields = $custom_fields;
	}
	return $data;
}
add_filter( 'thermal_post_entity', 'kentnews_add_post_meta_to_api', 10, 3);


function kentnews_post_excerpt_meta_box($post) {
	remove_meta_box( 'postexcerpt' , $post->post_type , 'normal' );  ?>
	<div id="excerpt_metabox" class="postbox" style="margin-top: 10px;">
		<h3><span>Excerpt</span></h3>
		<div class="inside">
			<label class="screen-reader-text" for="excerpt"><?php _e('Excerpt') ?></label>
             <textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo $post->post_excerpt; ?></textarea>
		</div>
	</div>
<?php }

add_action('edit_form_after_title', 'kentnews_post_excerpt_meta_box');