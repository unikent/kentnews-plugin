<?php


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