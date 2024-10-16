<?php
/**
 * The template for displaying the footer.
 *
 * @package Salient WordPress Theme
 * @version 9.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = get_nectar_theme_options();
global $post;

$header_format = ( ! empty( $options['header_format'] ) ) ? $options['header_format'] : 'default';
$theme_skin    = ( ! empty( $options['theme-skin'] ) ) ? $options['theme-skin'] : 'original';
if ( 'centered-menu-bottom-bar' == $header_format ) {
	$theme_skin = 'material';
}

$using_footer_widget_area  = ( ! empty( $options['enable-main-footer-area'] ) && $options['enable-main-footer-area'] == 1 ) ? 'true' : 'false';
$disable_footer_copyright  = ( ! empty( $options['disable-copyright-footer-area'] ) && $options['disable-copyright-footer-area'] == 1 ) ? 'true' : 'false';
$footer_reveal             = ( ! empty( $options['footer-reveal'] ) ) ? $options['footer-reveal'] : 'false';
$footer_full_width         = ( ! empty( $options['footer-full-width'] ) ) ? $options['footer-full-width'] : 'false';
$copyright_line            = ( ! empty( $options['footer-copyright-line'] ) && $options['footer-copyright-line'] == 1 ) ? 'true' : 'false';
$footer_columns            = ( ! empty( $options['footer_columns'] ) ) ? $options['footer_columns'] : '4';
$footer_bg_image_overlay   = ( ! empty( $options['footer-background-image-overlay'] ) ) ? $options['footer-background-image-overlay'] : '0.8';
$footer_bg_image           = ( ! empty( $options['footer-background-image'] ) && ! empty( $options['footer-background-image']['url'] ) ) ? nectar_options_img( $options['footer-background-image'] ) : false;
$footer_bg_color           = ( ! empty( $options['footer-background-color'] ) ) ? $options['footer-background-color'] : 'default-footer-color';
$footer_copyright_bg_color = ( ! empty( $options['footer-copyright-background-color'] ) ) ? $options['footer-copyright-background-color'] : 'default-footer-copyright-color';
$footer_custom_color       = ( ! empty( $options['footer-custom-color'] ) && $options['footer-custom-color'] == '1' ) ? 'true' : 'false';

$using_footer_bg_img    = 'false';
$footer_bg_image_markup = '';

if ( $footer_bg_image && ! empty( $footer_bg_image ) ) {
	$using_footer_bg_img    = 'true';
	$footer_bg_image_markup = 'style="background-image:url(' . $footer_bg_image . ');"';
}

$matching_footer_color = 'false';

if ( $footer_custom_color == 'true' ) {
	$matching_footer_color = ( $footer_bg_color == $footer_copyright_bg_color ) ? 'true' : 'false';
} elseif ( $footer_custom_color == 'false' && $theme_skin == 'material' || $footer_custom_color == 'false' && $theme_skin == 'ascend' ) {
	$matching_footer_color = 'true';
}


?>

<div id="footer-outer" <?php echo ( $footer_reveal != '1' ) ? 'data-midnight="light"' : ''; ?>
     data-cols="<?php echo esc_attr( $footer_columns ); ?>"
     data-custom-color="<?php echo esc_attr( $footer_custom_color ); ?>"
     data-disable-copyright="<?php echo esc_attr( $disable_footer_copyright ); ?>"
     data-matching-section-color="<?php echo esc_attr( $matching_footer_color ); ?>"
     data-copyright-line="<?php echo esc_attr( $copyright_line ); ?>"
     data-using-bg-img="<?php echo esc_attr( $using_footer_bg_img ); ?>"
     data-bg-img-overlay="<?php echo esc_attr( $footer_bg_image_overlay ); ?>"
     data-full-width="<?php echo esc_attr( $footer_full_width ); ?>"
     data-using-widget-area="<?php echo esc_attr( $using_footer_widget_area ); ?>" <?php echo $footer_bg_image_markup; // WPCS: XSS ok. ?>>

	<?php

	get_template_part( 'includes/partials/footer/call-to-action' );

	get_template_part( 'includes/partials/footer/main-widgets' );

	get_template_part( 'includes/partials/footer/copyright-bar' );

	?>

</div><!--/footer-outer-->

<?php

get_template_part( 'includes/partials/footer/off-canvas-navigation' );

?>


</div> <!--/ajax-content-wrap-->


<?php
if ( ! empty( $options['boxed_layout'] ) && $options['boxed_layout'] == '1' && $header_format != 'left-header' ) {
	echo '</div><!--/boxed closing div-->';
}


get_template_part( 'includes/partials/footer/back-to-top' );

get_template_part( 'includes/partials/footer/body-border' );

wp_footer();

if ( 'material' == $theme_skin ) {
	echo '</div></div><!--/ocm-effect-wrap-->';
}

nectar_hook_before_body_close();

?>
<link href="<?= bloginfo( 'template_directory' ); ?>/css/ee_gfonts.css" rel="stylesheet" type="text/css"/>

<div id="metrika">
<!-- Yandex.Metrika counter -->
<script type="text/javascript" async>
    setTimeout(function(){
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        ym(34416330, "init", {
            id: 34416330,
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: false
        });
    },8000);

    jQuery.event.special.mousewheel = {
        setup: function( _, ns, handle ) {
            this.addEventListener("mousewheel", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };

</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/34416330" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript> <!-- /Yandex.Metrika counter -->
</div>
</body>
</html>
