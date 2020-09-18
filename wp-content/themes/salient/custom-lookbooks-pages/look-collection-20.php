<?php /* Template Name: look-collections-20  */
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
            <h1>Страна контрастов</h1>
            <p class="intro-text">Дизайнеры делают акцент на глубоком
синем цвете и комбинируют цветочные
принты, свойственные разным культурам.
Платье футляр и бутоны распустившейся
сакуры на нем придают нежное весеннее
настроение. А брюки прямого кроя и
струящаяся блуза пудрового цвета помогут
создать сдержанный образ для деловой
встречи. Разное настроение – каждый раз
новая вы 
            </p> 
        </div>
    
        <div style="display: flex;justify-content: center;align-items: center">
            <div class="forCollection">
                <!--элемент 1-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/02/img293399.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-943-zhar-v20/" target="_blank" data-tooltip="Платье П-943 ЖАР(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i> 
                        </a>
                </div>
                <!--элемент 2-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/02/img293386.jpg") no-repeat;background-size: cover;    background-position: center;'>
                    <a href="/shop/bluzka-d-802-chr-v20/" target="_blank" data-tooltip="Блузка Д-802 ЧР(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a> 
                </div>  
                <!--элемент 3-->
                <div class="bodyForOneEl" style=' background: url("/wp-content/uploads/2020/03/img293353.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-937-ine-v20/" target="_blank" data-tooltip="Платье П-937 ИНЕ(В20)" style="height: 100%;right: 35px; bottom: 60px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>   
                <!--элемент 4--> 
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/03/img293373.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bryuki-b-802-un-v20" target="_blank" data-tooltip="Брюки Б-802 УН(В20)" style="height: 100%;    bottom: -30px; "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                    
                </div>  
                <!--элемент 5-->
                <div class="bodyForOneEl viveEl" style='background: url("/wp-content/uploads/2020/03/img293362.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/plate-p-58-yas-v20/" target="_blank" data-tooltip="Платье П-58 ЯС(В20)" style="height: 100%; bottom: 60px; "
                           data-position="right" class="right custPoint">
                            <i class="fa fa-bullseye" aria-hidden="true"></i>
                        </a>
                </div>
                <!--элемент 6-->
                <div class="bodyForOneEl " style='background: url("/wp-content/uploads/2020/03/img293375.jpg") no-repeat;background-size: cover;    background-position: center;'>
                <a href="/shop/bluzka-d-909-or-v20/" target="_blank" data-tooltip="Блузка Д-909 ОР(В20)" style="height: 100%; bottom: 70px "
                       data-position="right" class="right custPoint">
                        <i class="fa fa-bullseye" aria-hidden="true"></i>
                    </a>
                </div>
            
              
        </div>


        </div>
    </div><!--/row-->
</div><!--/container-->
<?php get_footer(); ?>
