<?php /* Template Name: look-collections-2 */
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
            <h1>Сицилия</h1>
            <p class="intro-text">Голубое небо, синее море и лимонные деревья. Комплект из свитшота-анорака с
                расслабленными брюками -это новое прочтение «гусиной» лапки. Но не обошлось и без романтичного платья и
                блузы с рюшами в темно-синем оттенке. Сочетание спорта и женственности это лучший рецепт для
                повседневного образа.
            </p>
        </div>

        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 2-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44981.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-2011-oni-vl/" target="_blank" data-tooltip="Брюки Б-2011 ОНИ(ВЛ) "
                       style="height: 100%; bottom: -19px; left: 48px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45868.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bluzka-d-2030-oza-vl/" target="_blank" data-tooltip="Блузка Д-2030 ОЗА(ВЛ)"
                       style="height: 100%; bottom: 60px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45815.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-2042-yant-vl/" target="_blank" data-tooltip="Платье П-2042 ЯНТ(ВЛ)"
                       style="height: 100%; bottom: 60px; left:30px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl "
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46198.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-2045-pir-vl/" target="_blank" data-tooltip="Блузка Д-2045 ПИР(ВЛ)"
                       style="height: 100%; left: 15px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46188.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bryuki-b-2007-pir-vl/" target="_blank" data-tooltip="Брюки Б-2007 ПИР(ВЛ)"
                       style="height: 100%; bottom: -20px;left: 44px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46160.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/dzhemper-s-24-cher-vl/" target="_blank" data-tooltip="Джемпер С-24 ЧЕР(ВЛ)"
                       style="height: 100%; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
