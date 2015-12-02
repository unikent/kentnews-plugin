<?php

$mm->the_field('primary_category');
$cat = $mm->get_the_value();
$selected="";

if (!empty($cat)) {
$catTerms = get_term_by('slug', $cat, 'category');
$categoryID = $catTerms->term_id;
$selected = "&selected=" . $categoryID;
}

?>
<div class="mm_input_group">
    <?php
    wp_dropdown_categories('show_option_none=Select category&name=' . $mm->get_the_name() . '&hide_empty=0' . $selected);
    ?>
</div>
<?php