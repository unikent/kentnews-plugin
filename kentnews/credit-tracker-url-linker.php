<?php
/**
 * Credit tracker licenses should be HTML links.
 * This just changes the on save behavior to covert hand written links (starting with http) in to a HTML license link
 *
 */
add_filter('attachment_fields_to_save', function($post, $attachment){
	
	if(isset($attachment['credit-tracker-license']) && strpos($attachment['credit-tracker-license'], 'http')===0){
		$attachment['credit-tracker-license'] = "<a href=\"{$attachment['credit-tracker-license']}\">License</a>";
        update_post_meta($post['ID'], 'credit-tracker-license', $attachment['credit-tracker-license']);
	}

	return $post;
}, 11, 2);