<?php /* Template Name: look-collections-9  */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

get_header();
nectar_page_header($post->ID);
$nectar_fp_options = nectar_get_full_page_options();

?>
<div class="container-wrap">
    <a href='/lookbook/' class=back-url>К списку коллекций</a>
    <div class="row" style="padding-bottom: 200px">
        <div style='display: flex; margin-left: 11%; flex-direction: column; width: 80%;'>
            <h1>Ниагара</h1>
            <p class="intro-text">Рабочие будни превратятся в отдых у водопада, благодаря образам из этой капсулы.
                Комплект из рубашки и брюк из плотной ткани может играть роль как в деловом, так и в стиле кэужал. А для
                особого выхода платье-рубашка с объемными рукавами в оттенке синий блюз.
            </p>
        </div>
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44490.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/dzhemper-s-22-lev-vl/" target="_blank" data-tooltip="Джемпер С-22 ЛЭВ(ВЛ) "
                       style="height: 100%; bottom: 20px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45205.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/bluzka-d-913-bud-vl/" target="_blank" data-tooltip="Блузка Д-913 БУД(ВЛ)"
                       style="height: 100%; bottom: 100px; left: 30px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45157.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/bluzka-d-2026-den-vl/" target="_blank" data-tooltip="Блузка Д-2026 ДЕН(ВЛ)"
                       style="height: 100%; bottom: 70px; left: 0; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45175.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-2007-den-vl/" target="_blank" data-tooltip="Брюки Б-2007 ДЕН(ВЛ)"
                       style="height: 100%; bottom: -70px; left: 33px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45101.jpg") no-repeat;
                     background-size: cover;background-position: center;'>
                    <a href="/shop/plate-p-2106-den-vl/" target="_blank" data-tooltip="Платье П-2106 ДЕН(ВЛ)"
                       style="height: 100%; bottom: 70px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

        </div>
    </div><!--/row-->
</div><!--/container-->

<?php get_footer(); ?>

