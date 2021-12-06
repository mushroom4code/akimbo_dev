<?php /* Template Name: look-collections-7  */
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
            <h1>Лунная греза</h1>
            <p class="intro-text">Капсула, которая позволяет летать. Модели свободного кроя, дают возможность динамичного передвижения по городу. 
                 Одежда разработана дизайнерами в спокойных оттенках. 
                  Собраны разные элементы: костюм в двух форматах, платья , блузы. Капсула станет отличной базой в вашем гардеробе.
            </p>
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/12/img285350.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-810-pl-v20/" target="_blank" data-tooltip="Блузка Д-810 ПЛ(В20)" style="height: 30%; top: 30px; right: 14px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a> 
                    <a href="/shop/zhaket-v-906-gar-v20/" target="_blank" data-tooltip="Жакет В-906 ГАР(В20)" style="height: 30%; bottom: 60px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-802-gar-v20/" target="_blank" data-tooltip="Брюки Б-802 ГАР(В20)" style="height: 30%; left: 25px; bottom: 100px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/11/img284020.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-916-mah-v20/" target="_blank" data-tooltip="Платье П-916 МАХ(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/11/img284039.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-874-mah-v20/" target="_blank" data-tooltip="Платье П-874 МАХ2(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/11/img284706.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-944-gr-v20/" target="_blank" data-tooltip="Платье П-944 ГР(В20)" style="height: 100%; bottom: 60px;right: 25px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/01/Risunok3.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-810-pl-v20/" target="_blank" data-tooltip="Блузка Д-810 ПЛ(В20)" style="height: 50%; top:30px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-904-gar-v20/" target="_blank" data-tooltip="Брюки Б-904 ГАР(В20)" style="height: 50%; bottom: 75px;     left: 10px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/Risunok4.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-810-pl-v20/" target="_blank" data-tooltip="Блузка Д-810 ПЛ(В20)" style="height: 30%; top: 11px;    left: 10px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a> 
                    <a href="/shop/zhaket-v-906-gar-v20/" target="_blank" data-tooltip="Жакет В-906 ГАР(В20)" style="height: 30%; bottom: 60px;    left: 48px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/bryuki-b-904-gar-v20/" target="_blank" data-tooltip="Брюки Б-904 ГАР(В20)" style="height: 30%;     bottom: 100px;    left: 30px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->

<?php get_footer(); ?>
