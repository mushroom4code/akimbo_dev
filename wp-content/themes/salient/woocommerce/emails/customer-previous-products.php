<?php
/** @var $user */
if (!defined('ABSPATH')) {
    exit;
} ?>
<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>AKIMBO — Классическая женская одежда оптом от производителя</title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"
      class="kt-woo-wrap order-items-normal k-responsive-normal title-style-none email-id-new_order">
<div id="wrapper" dir="ltr"
     style="z-index: 999990; background-color: #fcfaf7; margin: 0; padding: 70px 0; width: 100%; -webkit-text-size-adjust: none;">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tbody>
        <tr>
            <td align="center" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container"
                       style="background-color: #ffffff; border: 1px solid #e3e1de; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); border-radius: 3px;">
                    <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Header -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header"
                                   style="background-color: #af8a6e; color: #ffffff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; border-radius: 3px 3px 0 0;">
                                <tbody>
                                <tr>
                                    <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                        <h1 style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;
												font-size: 30px; font-weight: 300; line-height: 150%; margin: 0;
												 text-align: left; text-shadow: 0 1px 0 #bfa18b; color: #ffffff;
												  background-color: inherit;">Ваши просмотренные товары</h1>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- End Header -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                <tbody>
                                <tr>
                                    <td valign="top" id="body_content" style="background-color: #ffffff;">
                                        <!-- Content -->
                                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tbody>
                                            <tr>
                                                <td valign="top" style="padding: 48px 48px 32px;">
                                                    <div id="body_content_inner"
                                                         style="margin-bottom: 35px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%; text-align: left;">
                                                        <h2 style="color: #af8a6e; display: block;
                                                        font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;
                                                        font-size: 18px; font-weight: bold; line-height: 130%;
                                                        margin: 0 0 18px; text-align: left;">Здравствуйте!</h2>
                                                        <p style="color: black; margin: 0 0 16px;">Вы недавно посещали наш сайт и
                                                            просматривали эти модели одежды на сайте akimbo-moda.ru. Мы
                                                            их сохранили, чтобы в удобное время, вы могли вернуться к
                                                            ним. Если вы хотите зарезервировать их, добавьте
                                                            интересующие Вас модели в корзину.</p>
                                                        <p style="color: black; margin: 0 0 16px;">Срок резерва без предоплаты 5 дней.
                                                            Минимальный заказ 35 000 рублей.</p>
                                                    </div>
                                                    <div>
                                                        <?php
                                                        $viewed_products = (array)explode('|', get_user_meta($user->ID, 'recently_viewed_products')[0]);
                                                        foreach ($viewed_products as $productId) {
                                                            $product = wc_get_product($productId);
                                                            if ($product->get_stock_quantity() < 1) {
                                                                continue;
                                                            }
                                                            ?>
                                                            <div style="display: flex; margin-bottom: 10px;">
                                                                <?php
                                                                $url = esc_url($product->get_permalink());
                                                                echo '<div style="width: 50%; margin-right: 20px;">';
                                                                echo '<a style="text-decoration: none; color: none;" href="' . $url . '">';
                                                                echo '<img style="height: fit-content; width: 100%; border-radius: 5px;"
                                                                src="' . wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0] . '"/>';
                                                                echo '</a>';
                                                                echo '</div>';
                                                                echo '<div style="width: 50%;">';
                                                                echo '<a style="color: black; text-decoration: none;" href="' . $url . '">';
                                                                echo '<h2 style="font-size: 18px;font-weight: bold;
                                                                    line-height: 130%;text-align: left;">'
                                                                    . $product->get_title() . '</h2>';
                                                                echo '</a>';
                                                                echo '<div class="price-hover-wrap">';

                                                                $infoMessage = '';
                                                                $handle = new WC_Product_Variable($product->get_id());
                                                                $variations1 = $handle->get_children();
                                                                $i = 0;
                                                                $emptyStock = array();
                                                                foreach ($variations1 as $value) {
                                                                    $single_variation = new WC_Product_Variation($value);
                                                                    if ($single_variation->stock_status == 'outofstock') {
                                                                        $emptyStock [] = 1;
                                                                    }
                                                                    $i++;
                                                                }
                                                                if (count($emptyStock) == $i) {
                                                                    $infoMessage = 'Нет в наличии';
                                                                }

                                                                $first_date = get_post_meta($product->get_id(), 'first_date', true);
                                                                $planned_date = get_post_meta($product->get_id(), 'planned_date', true);
                                                                $Date = '';
                                                                if (!empty($first_date) && $first_date !== 'false') {
                                                                    $Date = '';
                                                                } else if (!empty($planned_date)) {
                                                                    $Date = '<div style="padding: 10px 0;" class="plain_date">
            <b style="font-weight: 600;font-size: 13px;color: #545252;margin-right: 9px;">Плановое поступление</b>
            <span style="font-weight: 500;font-size: 15px;color: #af8a6e;">' . $planned_date . '</span>
            </div>';
                                                                }

                                                                if ($Date === '') {
                                                                    $Date = $infoMessage;
                                                                }

                                                                if ($product->get_price() == 0 && $product->get_stock_quantity() == 0 && $product->get_backorders() == 'yes') {
                                                                    ?>
                                                                    <span style="margin-top: 0; font-size: 15px"
                                                                          class="CustomEmptyPrice">В производстве</span>
                                                                <?php } else {
                                                                    if ($price_html = $product->get_price_html()) : ?>
                                                                        <span style="color: #af8a6e;"
                                                                              class="price"><?php echo $price_html . ' ' . $Date ?></span>
                                                                    <?php endif;
                                                                }
                                                                echo '</div></div>';
                                                                ?>
                                                            </div>
                                                            <?php
                                                        } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <!-- End Content -->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- End Body -->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <!-- Footer -->
                <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                    <tbody>
                    <tr>
                        <td valign="top" style="padding: 0; border-radius: 6px;">
                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                <tbody>
                                <tr>
                                    <td colspan="2" valign="middle" id="credit"
                                        style="border-radius: 6px; border: 0; color: #707070; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 12px; line-height: 150%; text-align: center; padding: 24px 0;">
                                        <p style="margin: 0 0 16px;"><span style="color: #af8a6e;"><strong>AKIMBO —  Классическая женская одежда оптом</strong></span>
                                        </p>
                                        <p style="margin: 0 0 16px;"><a style="text-align: center;" href="<?=get_site_url().'/my-account/?ViewedProductsNewsletter=false';?>">Отписаться от рассылки</a>
                                        </p>
                                        <p style="margin: 0 0 16px;"><?= get_site_url(); ?></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- End Footer -->
            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>


