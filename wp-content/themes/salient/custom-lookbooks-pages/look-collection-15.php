<?php /* Template Name: look-collections-15  */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
nectar_page_header( $post->ID );

$nectar_fp_options = nectar_get_full_page_options();?>
<div class="container-wrap">
    <a href='/lookbook' class=back-url>К списку коллекций</a>
    <div class="row" style="padding-bottom: 200px">
        <div style='display: flex; margin-left: 11%; flex-direction: column; width: 80%;'>
            <h1>Сияющая орхидея</h1>
            <p class="intro-text">Наша капсула напоминает букет
распустившихся орхидей. Нежные
пастельные оттенки. Плавные линии и
формы у элементов одежды.
Романтичное настроение, детали
(воланы, манжеты). Идеальный набор
для встречи весенней поры! 
            </p>
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003060.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-935-va-v20/" target="_blank" data-tooltip="Платье П-935 ВА(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i> 
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/01/A003037.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-941-roz-v20/" target="_blank" data-tooltip="Платье П-941 РОЗ(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/01/A003044.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/plate-p-2004-kar-v20/" target="_blank" data-tooltip="Платье П-2004 КАР(В20)" style="height: 100%; bottom: 60px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div> 
                <!--элемент 4--> 
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003055.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bryuki-b-01-msh-v20/" target="_blank" data-tooltip="Брюки Б-01 МШ(В20)" style="height: 100%; left: 10px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    
                </div>
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/01/A003049.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/zhaket-v-907-lil-v20/" target="_blank" data-tooltip="Жакет В-907 ЛИЛ(В20)" style="height: 100%; bottom: 75px; left:10px"
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003057.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-810-pl-v20" target="_blank" data-tooltip="Блузка Д-810 ПЛ(В20)" style="height: 100%; bottom: 115px "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
                   
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
