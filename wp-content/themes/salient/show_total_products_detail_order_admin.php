<?php


function get_total_number_of_items_in_order( $order_id ) {
    $order = wc_get_order( $order_id );
    $total_quantity = 0;
    foreach ( $order->get_items() as $item_id => $item ) {
        $quantity = $item->get_quantity();
        $total_quantity += $quantity;
    }
    return $total_quantity;
}

function display_total_quantity_in_order_details( $order_id ) {
    $total_quantity = get_total_number_of_items_in_order( $order_id );
    ?>

    <tr>
        <td class="label">Итого штук:</td>
        <td width="1%"></td>
        <td class="amount">
            <?php echo $total_quantity;  ?>
        </td>
    </tr>

    <?php
}

add_action( 'woocommerce_admin_order_totals_after_discount', 'display_total_quantity_in_order_details', 10 );
