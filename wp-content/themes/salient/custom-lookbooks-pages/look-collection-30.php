<?php /* Template Name: look-collections-30  */
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
            <p class="intro-text">Город, которым вдохновлена капсула, славится произведениями искусства и архитектуры эпохи возрождения.
             Здесь для дизайнеров были очень важны детали: широкие рукава с манжетами на платье, блуза ассиметричной формы с поясом, акцентирующая талию,
              ткань с несколькими вариациями узоров. Платья в капсуле подобраны разной длины, вы сможете выбрать образ в зависимости от вашей цели на день. 
              Вдохновляйтесь и чувствуйте себя героиней, прогуливавшейся по Флоренции. 
            </p>  
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/07/30_06_2020_AKIMBO81742.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-2026-pb-o/" target="_blank" data-tooltip="Платье П-2026 ПБ(О)" style="height: 100%; bottom: 90px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i> 
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/07/30_06_2020_AKIMBO81642.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-627-mel-o/" target="_blank" data-tooltip="Платье П-627 МЕЛ(О)" style="height: 100%; bottom: 90px; left: 20px "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a> 
                </div>  
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/03/img293201.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-2013-sham-v20/" target="_blank" data-tooltip="Платье П-2013 ШАМ(В20)" style="height: 100%;bottom: 60px;    right: 20px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>   
                <!--элемент 4--> 
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/07/30_06_2020_AKIMBO81724.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bryuki-b-01-pb-o/" target="_blank" data-tooltip="Брюки Б-01 ПБ(О)" style="height: 100%; left: 15px;"
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    
                </div>  
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/07/30_06_2020_AKIMBO81685.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-2007-pb-o/" target="_blank" data-tooltip="Блузка Д-2007 ПБ(О)" style="height: 100%; bottom: 125px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/07/30_06_2020_AKIMBO81125.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-2005-chs-o/" target="_blank" data-tooltip="Блузка Д-2005 ЧС(О)" style="height: 100%; bottom: 70px "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
        
        </div>
       
        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
