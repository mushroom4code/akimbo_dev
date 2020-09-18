<?php /* Template Name: look-collections-18  */
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
            <h1>Флоренция</h1>
            <p class="intro-text">Капсула излучает эстетику солнечного
города Италии. Как Флоренция, она
содержит разные элементы, которые по
своему привлекательны, а вместе
формируют целостную историю. В
капсулу включены следующие модели:
платье длины миди, платье
трансформер, блуза с защипом на
плечевой линии, брюки с поясом paper
bag.
            </p>
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003015.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-2001-kar-v20/" target="_blank" data-tooltip="Блузка Д-2001 КАР(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i> 
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/01/A002999.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-952-kro-v20/" target="_blank" data-tooltip="Платье П-952 КРО(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a> 
                </div>  
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/01/A003026.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-928-kap-v20/" target="_blank" data-tooltip="Платье П-928 КАП(В20)" style="height: 100%; bottom: 90px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>  
                <!--элемент 4--> 
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003031.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-907-dub-v20" target="_blank" data-tooltip="Платье П-907 ДУБ(В20)" style="height: 100%;    top: -100px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    
                </div>  
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/01/A003022.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bryuki-b-901-kap-v20/" target="_blank" data-tooltip="Брюки Б-901 КАП(В20)" style="height: 100%; bottom: -20px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/01/A003011.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-898-gal-v20/" target="_blank" data-tooltip="Платье П-898 ГАЛ(В20)" style="height: 100%; bottom: 70px "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            
              
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
