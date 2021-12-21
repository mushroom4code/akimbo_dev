<?php /* Template Name: look-collections-4 */
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
            <h1>Лаймовый бриз</h1>
            <p class="intro-text">Зелёный - ультра тренд сезона, а с появлением первой зелени станет еще актуальнее. Его
                уникальность в том, что он сочетается как с белым, так и с чёрным, поэтому в капсуле представлены белая
                и чёрная блузка, чёрные брюки и платье в хвойном оттенке в принт зебра.
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
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46392.jpg") no-repeat;background-size: cover; background-position: center;'>
                    <a href="/shop/bluzka-d-2054-gr-vl/" target="_blank" data-tooltip="Блузка Д-2054 ГР(ВЛ)"
                       style="height: 100%;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46026.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/zhaket-v-911-hit-vl/" target="_blank" data-tooltip="Жакет В-911 ХИТ(ВЛ) "
                       style="height: 100%; bottom: 60px; left:45px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46119.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-703-hit-vl/" target="_blank" data-tooltip="Брюки Б-703 ХИТ(ВЛ)"
                       style="height: 100%; bottom: -19px; left: 35px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44490.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/dzhemper-s-22-lev-vl/" target="_blank" data-tooltip="Джемпер С-22 ЛЭВ(ВЛ) "
                       style="height: 100%; bottom: 20px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_46128.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-703-unb-vl/" target="_blank" data-tooltip="Брюки Б-703 УНБ(ВЛ)"
                       style="height: 100%; bottom: -52px;left: 40px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style=' background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45614.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/plate-p-2114-pik-vl/" target="_blank" data-tooltip="Платье П-2114 ПИК(ВЛ)"
                       style="height: 100%;bottom:50px; left: 20px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
