<?php

add_action( 'init', 'kentnews_register_taxonomy_tag' );

/**
 * Add a custom Tag taxonomy so that we can permission tag creation.
 */
function kentnews_register_taxonomy_tag() {

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
		'show_in_nav_menus' => false,
		'show_ui' => true,
		'show_tagcloud' => false,
		'show_admin_column' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'meta_box_cb' => 'kentnews_predefined_tags_meta_box',
		'query_var' => 'tags',
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
 * Remove original tags admin box & admin page
 */
function kentnews_remove_default_tags() {
	remove_meta_box('tagsdiv-post_tag', 'post', 'side');
	remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');

}
add_action( 'admin_menu', 'kentnews_remove_default_tags' );

/**
 * Add meta box for editing predefined tags in a post
 *
 */
function kentnews_predefined_tags_meta_box($post, $box) {
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