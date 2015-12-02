<strong>Related Links</strong>
<p class="help-text">add as many related links as you need, drag to reorder</p>
<div class="mm_input_group">
<?php while($mm->have_fields_and_multi('related')): ?>
	<?php $mm->the_group_open('div','div',true); ?>

	<?php $mm->the_field('title'); ?>
	<div class="mm_input_group">
	<label for="<?php $mm->the_name(); ?>">Title:</label>
	<input id="<?php $mm->the_name(); ?>" type="text" name="<?php $mm->the_name(); ?>" value="<?php $mm->the_value(); ?>"/>
	</div>
	<?php $mm->the_field('url'); ?>
	<div class="mm_input_group">
	<label for="<?php $mm->the_name(); ?>">URL:</label>
	<input id="<?php $mm->the_name(); ?>" type="text" name="<?php $mm->the_name(); ?>" value="<?php $mm->the_value(); ?>"/>
	</div>
	<p>
		<a href="#" class="mm_dodelete button"><span class="dashicons dashicons-trash"></span> Remove Related Link</a>
	</p>

	<?php $mm->the_group_close(); ?>
<?php endwhile; ?>
<a href="#" class="button <?php echo $mm->the_copy_button_class(true); ?>"><span class="dashicons dashicons-plus"></span> Add Related Link</a>
</div>
<strong>Media Coverage</strong>
<p class="help-text">add as many media coverage items as you need, drag to reorder</p>
<div class="mm_input_group">
<?php while($mm->have_fields_and_multi('coverage')): ?>
	<?php $mm->the_group_open('div','div',true); ?>

	<?php $mm->the_field('title'); ?>
	<div class="mm_input_group">
		<label for="<?php $mm->the_name(); ?>">Title:</label>
		<input id="<?php $mm->the_name(); ?>" type="text" name="<?php $mm->the_name(); ?>" value="<?php $mm->the_value(); ?>"/>
	</div>
	<?php $mm->the_field('url'); ?>
	<div class="mm_input_group">
		<label for="<?php $mm->the_name(); ?>">URL:</label>
		<input id="<?php $mm->the_name(); ?>" type="text" name="<?php $mm->the_name(); ?>" value="<?php $mm->the_value(); ?>"/>
	</div>
	<p>
		<a href="#" class="mm_dodelete button"><span class="dashicons dashicons-trash"></span> Remove Media Coverage</a>
	</p>

	<?php $mm->the_group_close(); ?>
<?php endwhile; ?>
<a href="#" class="button <?php echo $mm->the_copy_button_class(true); ?>"><span class="dashicons dashicons-plus"></span> Add Media Coverage</a>
</div>