<?php
/**
 * Make video embeds responsive.
 *
 * @return string
 */
function kent_responsive_videos_embed_html( $html ) {
	if ( empty( $html ) || ! is_string( $html ) ) return $html;
	// wrap in container
	return '<figure class="video-container">' . $html . '</figure>';
}

// After theme setup, init responsive video's
add_action( 'after_setup_theme', function(){

	// Hook to filters
	add_filter( 'wp_video_shortcode', 'kent_responsive_videos_embed_html' );
	add_filter( 'embed_oembed_html',  'kent_responsive_videos_embed_html' );
	add_filter( 'video_embed_html',   'kent_responsive_videos_embed_html' );

}, 99);



