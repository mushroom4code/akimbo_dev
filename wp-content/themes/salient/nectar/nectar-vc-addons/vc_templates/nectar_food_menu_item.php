<?php
extract(shortcode_atts(array("style" => "default", "item_name" => '', "item_id" => '', 'item_name_heading_tag' => 'h4', "item_price" => "", 'item_description' => '', 'class' => ''), $atts));
$price = '';
$date = '';
$item_id = trim($item_id);
$first_date = get_post_meta($item_id, 'first_date', true);
$planned_date = get_post_meta($item_id, 'planned_date', true);
if(!empty($first_date)){
    $date = '';
} else if (!empty($planned_date)) {
    $date = '<div style="text-align: center; padding-top: 5px" class="plain_date">
            <b style="font-weight: 600;font-size: 13px;color: #545252;margin-right: 9px;">Плановое поступление</b>
            <span style="font-weight: 500;font-size: 15px;color: #af8a6e;">' . $planned_date . '</span>
            </div>';
}

if (is_user_logged_in()) {
    $price = '<div class="item_price"><' . $item_name_heading_tag . '>' . $item_price . '</' . $item_name_heading_tag . '></div>';
}
$line_markup = '<div class="line_spacer"></div>';

echo '<div class="nectar_food_menu_item ' . $class . '" data-style="' . $style . '">
<div class="inner tets"><div class="item_name">
<' . $item_name_heading_tag . '>' . $item_name . '</' . $item_name_heading_tag . '>
</div>' . $line_markup . $price . ' ' . $date . '</div>
<div class="item_description">' . $item_description . '</div>
</div>';
?>