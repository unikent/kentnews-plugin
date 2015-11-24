<?php
$positions = get_terms('position',array('orderby' => 'id'));

$current = "standard-item";
$pos = wp_get_post_terms($post->ID,'position');
if(!empty($pos)){
	$current = $pos[0]->slug;
}
$mm->the_field('homepage_position');

foreach($positions as $p){
	?>
<label class="button"><input type="radio" name="<?php $mm->the_name(); ?>" value="<?php echo $p->slug; ?>"<?php echo $p->slug == $current?' checked="checked"':''; ?>> <?php echo $p->name; ?></label>
<?php }