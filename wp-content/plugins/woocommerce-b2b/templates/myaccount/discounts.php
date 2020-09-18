<?php
/**
 * Discounts
 *
 * Shows group category discounts on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/wcb2b-discounts.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  Code4Life
 * @package WooCommerce/Templates
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php if ( $categories ) : ?>

<table class="shop_table shop_table_responsive my_account_wcb2b-discounts">
    
    <?php foreach ( $categories as $category ) : ?>
    <tr>
    	<td><?php echo $category->name; ?></td>
    	<td>
    		<?php
    			if ( isset( $discounts[$category->term_id] ) ) {
    				echo number_format( $discounts[$category->term_id], 
                        wc_get_price_decimals(),
                        wc_get_price_decimal_separator(),
                        wc_get_price_thousand_separator()
                    ) . '%';
                } else {
                	echo '-';
                }
            ?>
        </td>
    </tr>
	<?php endforeach; ?>

</table>

<?php endif; ?>