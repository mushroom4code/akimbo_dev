<?php /* Template Name: look-collections-2  */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
nectar_page_header( $post->ID );
$nectar_fp_options = nectar_get_full_page_options();

?>
<div class="container-wrap">
    <a href='/lookbook' class=back-url>К списку коллекций</a>
    <div class="row" style="padding-bottom: 200px">
        <div style='display: flex; margin-left: 11%; flex-direction: column; width: 80%;'>
            <h1>Весна в городе</h1>
            <p class="intro-text">Все оттенки голубого цвета как весеннее небо пробуждающейся природы. Капсула передает настроение
                 свежести в ярком принте и лаконичных силуэтах, которые подчеркнут все достоинства фигуры: удлиненная блузка, платье с удлиненным подолом и уточенный костюм лазурного цвета.
            </p>
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/11/img284955.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-836-ki-v20/" target="_blank" data-tooltip="Платье П-836 КИ(В20)" style="height: 100%; bottom: 60px; right: 25px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/09/img284181.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-910-br3-o9/ " target="_blank" data-tooltip="Блузка Д-910 БР3(О9)" style="height: 50%; top:30px"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-805-neo-v20/" target="_blank" data-tooltip="Брюки Б-805 НЕО(В20)" style="height: 50%; bottom: 75px; left: 10px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/11/img284207.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-810-pl-v20/" target="_blank" data-tooltip="Блузка Д-810 ПЛ(В20)" style="height: 30%; top: 30px; right: 14px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/zhaket-v-810-neo-v20/" target="_blank" data-tooltip="Жакет В-810 НЕО(В20)" style="height: 30%; bottom: 60px;    left: 17px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-805-neo-v20/" target="_blank" data-tooltip="Брюки Б-805 НЕО(В20)" style="height: 30%;     bottom: 100px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/12/img284346.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-933-pl-v20/" target="_blank" data-tooltip="Блузка Д-933 ПЛ(В20)" style="height: 30%; top: 25px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/zhaket-v-86-klo-v20/" target="_blank" data-tooltip="Жакет В-86 КЛО(В20)" style="height: 30%; bottom: 60px; right: 16px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-802-klo-v20/" target="_blank" data-tooltip="Брюки Б-802 КЛО(В20)" style="height: 30%; bottom: 100px; left: 30px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2019/12/img284976.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-876-dzhu-v20/ " target="_blank" data-tooltip="Платье П-876 ДЖУ(В20)" style="height: 100%; bottom: 60px; left:25px"
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->

<?php get_footer(); ?>

<script>
    jQuery(document).ready(function($) {
        setTimeout(function() {
            $("div#hiddenForOptiaze").removeClass("hiddenenCustom");
        }, 1300); // <-- time in milliseconds
    });
</script>
