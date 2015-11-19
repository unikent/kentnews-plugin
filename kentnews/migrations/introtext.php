<?php
// Get composer
require_once(dirname( __FILE__ )."/../../../../../vendor/autoload.php");

// Attempt to load config
$config = dirname( __FILE__ ) .'/../../../../../';
if (!file_exists($config.'/.env')) die("No config (.env) found :(");

Dotenv::load($config);
Dotenv::required(array('WP_SITEURL'));
$blog_url = parse_url(getenv('WP_SITEURL'));

// Fake some server vars to make WP happy - use .env to make this portable
$_SERVER = array(
"HTTP_HOST" => $blog_url['host'],
"SERVER_NAME" => "http://".$blog_url['host'],
"REQUEST_URI" => isset($blog_url['path']) ? $blog_url['path'] : '',
"REQUEST_METHOD" => "GET"
);

// Boot wp (Shortinit set)
require_once( dirname( __FILE__ ) . '/../../../../wp/wp-load.php' );

$posts = get_posts(array(
   'post_type'=>'post',
   'posts_per_page'=> -1
));

foreach($posts as $post){
	update_post_meta($post->ID,'introtext',$post->post_excerpt);
	update_post_meta($post->ID,'_introtext_fields',array('introtext'));
}

echo count($posts) . " excerpts migrated";