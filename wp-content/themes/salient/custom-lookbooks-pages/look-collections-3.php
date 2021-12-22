<?php /* Template Name: look-collections-3 */
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
            <h1>Утренний Лотос</h1>
            <p class="intro-text">Комплект из жакета и брюк- это хорошо, а костюм нежно-голубого оттенка ещё лучше! Тем
                более, он прекрасно сочетается с розовым пастельным оттенком цветущего лотоса. Платье из шифона в
                цветочном принте - такой образ сразу расскажет всем, что весна пришла!
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
                <!--элемент 2-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44691.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/zhaket-v-911-led-vl/" target="_blank" data-tooltip="Жакет В-911 ЛЕД(ВЛ)"
                       style="height: 100%;bottom: -27px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_44673.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-703-led-vl/" target="_blank" data-tooltip="Брюки Б-703 ЛЕД(ВЛ)"
                       style="height: 100%; bottom: -23px;left: 45px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/09/15_09_2021_AKIMBO_1502.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bluzka-d-2048-zv-o1/" target="_blank" data-tooltip="Блузка Д-2048 ЗВ(О1)"
                       style="height: 100%; left: 15px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/09/15_09_2021_AKIMBO_1522.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/zhaket-v-915-vir-o1/" target="_blank" data-tooltip="Жакет В-915 ВИР(О1)"
                       style="height: 100%; left: 40px;bottom: 100px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/09/15_09_2021_AKIMBO_1484.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/bryuki-b-2004-vir-o1/" target="_blank" data-tooltip="Брюки Б-2004 ВИР(О1)"
                       style="height: 100%; left: 20px;bottom: -25px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="bodyForOneEl"
                     style='background: url("/wp-content/uploads/2021/11/09_12_2021_AKIMBO_45741.jpg") no-repeat;background-size: cover;background-position: center;'>
                    <a href="/shop/plate-p-2036-mav-vl/" target="_blank" data-tooltip="Платье П-2036 МАВ(ВЛ)"
                       style="height: 100%;bottom: 75px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
