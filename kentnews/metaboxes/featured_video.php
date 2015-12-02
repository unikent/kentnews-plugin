<?php
$mm->the_field('featured_video');
?>
<div class="mm_input_group">
    <label for="<?php $mm->the_name();?>">Featured Video:</label>
    <input id="<?php $mm->the_name();?>" type="text" name="<?php $mm->the_name();?>" value="<?php $mm->the_value(); ?>">
    <p class="help-text">a youtube url from the 'share' tab on youtube. eg. http://youtu.be/4JilOCxIjm8</p>
</div>
