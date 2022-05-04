<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

global $nectar_options;

$tab_style = (!empty($nectar_options['product_tab_position'])) ? $nectar_options['product_tab_position'] : 'default';
$fullwidth_tabs = (!empty($nectar_options['product_tab_position']) && $nectar_options['product_tab_position'] == 'fullwidth'
 || !empty($nectar_options['product_tab_position']) && $nectar_options['product_tab_position'] == 'fullwidth_centered') ? true : false;

if (!empty($tabs)) { ?>
</div>
	<div class="woocommerce-tabs wc-tabs-wrapper <?php if($fullwidth_tabs == true) {echo 'full-width-tabs';}?>" data-tab-style="<?= esc_attr( $tab_style ); ?>">
		<?php if($fullwidth_tabs == true) {
            echo '<div class="full-width-content" data-tab-style="'. $tab_style .'"> 
                    <div class="tab-container container">';}?>
			<ul class="tabs">
				<?php foreach ( $tabs as $key => $tab ) {?>
					<li class="<?= esc_attr( $key ); ?>_tab">
						<a href="#tab-<?= esc_attr( $key ); ?>">
                            <?= apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ); ?>
                        </a>
					</li>
				<?php } ?>
			</ul>
		<?php if($fullwidth_tabs == true) {
            echo '</div></div>';
        } foreach ( $tabs as $key => $tab ) { ?>
			<div class="panel entry-content" id="tab-<?= esc_attr( $key ); ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ); ?>
			</div>
		<?php } ?>
	</div>
<?php }