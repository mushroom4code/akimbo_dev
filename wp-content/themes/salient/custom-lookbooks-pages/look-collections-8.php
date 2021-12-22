<?php /* Template Name: look-collections-8  */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

get_header();
nectar_page_header($post->ID);
$nectar_fp_options = nectar_get_full_page_options();

?>
<div class="container-wrap">
    <a href='/lookbook' class=back-url>К списку коллекций</a>
    <div class="row" style="padding-bottom: 200px">
        <div style='display: flex; margin-left: 11%; flex-direction: column; width: 80%;'>
            <h1>Пинк Ройал</h1>
            <p class="intro-text">В этой капсуле вы найдёте все для первого праздника весны, ведь все образы в ней
                невероятно женственные и нежные. Мягкий трикотаж в розовых оттенках и яркие брюки, освежат и украсят
                гардероб.
            </p>
        </div>
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44363.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/dzhemper-s-12-pod-vl/" target="_blank" data-tooltip="Джемпер С-12 ПОД(ВЛ)"
                       style="height: 100%;bottom: -90px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44701.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/bluzka-d-2040-mart-vl/" target="_blank" data-tooltip="Блузка Д-2040 МАРТ(ВЛ)"
                       style="height: 100%; bottom: 130px; left: 30px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44716.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/yubka-a-2009-mart-vl/" target="_blank" data-tooltip="Юбка А-2009 МАРТ(ВЛ)"
                       style="height: 100%; bottom: -150px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45470.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/plate-p-849-mart-vl/" target="_blank" data-tooltip="Платье П-849 МАРТ(ВЛ)"
                       style="height: 100%;bottom:60px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44734.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-2011-pyr-vl/" target="_blank" data-tooltip="Брюки Б-2011 ПЫР(ВЛ)"
                       style="height: 100%;bottom:-40px;left: 20px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

        </div>
    </div><!--/row-->
</div><!--/container-->

<?php get_footer(); ?>

