<?php /* Template Name: look-collections-21  */
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
            <h1>Авангард</h1>
            <p class="intro-text">Наши дизайнеры вдохновляются искусством. В капсуле ярко выраженная асимметрия, глубокие цвета и эксцентричное настроение. 
            Актуальный костюм-двойка, состоящий из жилета и брюк прямого кроя, предлагаем комбинировать с яркой блузой лаймового цвета. А платья выбирать в зависимости от мероприятия.
            </p> 
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/02/img293668.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/zhaket-v-86-rad-v20/" target="_blank" data-tooltip="Жакет В-86 РАД(В20)" style="height: 100%; bottom: 35px; right: 35px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i> 
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/02/img293693.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/plate-p-2022-ya-v20/" target="_blank" data-tooltip="Платье П-2022 Я(В20)" style="height: 100%; bottom: 60px; right: 35px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a> 
                </div>  
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/03/img293638.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-2016-gli-v20/" target="_blank" data-tooltip="Платье П-2016 ГЛИ(В20)" style="height: 100%;right: 35px; bottom: 60px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>   
                <!--элемент 4--> 
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/03/img293348.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-937-ine-v20/" target="_blank" data-tooltip="Платье П-937 ИНЕ(В20)" style="height: 100%;  bottom: 60px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    
                </div>  
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/02/img293676.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bryuki-b-117-rad-v20/" target="_blank" data-tooltip="Брюки Б-117 РАД(В20)" style="height: 100%; bottom: -30px;left: 20px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/03/img293660.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-934-ep-v20/" target="_blank" data-tooltip="Блузка Д-934 ЭП(В20)" style="height: 100%; bottom: 70px "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            
              
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
