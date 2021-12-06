<?php /* Template Name: look-collections-8  */
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
            <h1>Элеганте</h1>
            <p class="intro-text">Ключевые модели капсулы - одежда из экокожи, которая подчеркивает силуэт и придаёт роскоши в образ.
                 Преимущество капсулы, что все модели самостоятельные и их не требуется усложнять лишними атрибутами. Элегантность в простоте и умении презентовать себя через нужную форму. 
                 С «Элеганте» получится всегда выглядеть стильно.
            </p>
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection"> 
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/12/img284742.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-940-kf-v20/" target="_blank" data-tooltip="Платье П-940 КФ(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/11/img284920.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-917-mah-v20/" target="_blank" data-tooltip="Блузка Д-917 МАХ(В20)" style="height: 50%; top:30px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/yubka-a-805-mah-v20/" target="_blank" data-tooltip="Юбка А-805 МАХ(В20)" style="height: 50%; bottom: 75px;     left: 10px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/12/img285044.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-960-kf-v20/" target="_blank" data-tooltip="Платье П-960 КФ(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                <!--элемент 4-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2019/12/img284305.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-933-pl-v20/" target="_blank" data-tooltip="Блузка Д-933 ПЛ(В20)" style="height: 30%; top: 35px;    left: 10px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a> 
                    <a href="/shop/zhaket-v-880-ek1-v20/ " target="_blank" data-tooltip="Жакет В-88 ЭК1(В20)" style="height: 30%; bottom: 46px;    left: 25px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    <a href="/shop/yubka-a-905-ek-v20/ " target="_blank" data-tooltip="Юбка А-805 ЭК1(В20)" style="height: 30%;     bottom: 70px;    left: 30px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
               <!--элемент 5-->
               <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/12/img284756.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-928-pss-v20/ " target="_blank" data-tooltip="Платье П-928 ПСС(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                  <!--элемент 6-->
               <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2019/12/img284389.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-931-ek3-v20/" target="_blank" data-tooltip="Платье П-931 ЭК3(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->

<?php get_footer(); ?>
