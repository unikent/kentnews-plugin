<?php
use HaddowG\MetaMaterial\MM_Metabox;

MM_Metabox::getInstance('introtext', array(
	'title'=> 'IntroText',
	'hide_title' =>true,
	'context'=>'after_title',
	'mode' => HaddowG\MetaMaterial\Metamaterial::STORAGE_MODE_EXTRACT,
	'template'=> dirname(__FILE__).'/metaboxes/introtext.php'
));


function kentnews_enqueue_post_metabox_styles($hook_suffix) {
	if( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
		wp_enqueue_style( 'post_metaboxes', content_url() . '/mu-plugins/kentnews/metaboxes/css/post_metaboxes.css');
  }
}
add_action( 'admin_enqueue_scripts', 'kentnews_enqueue_post_metabox_styles' );