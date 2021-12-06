<?php      

    $new_table_shipping =  '
     <style>
    .cust_div{
    height: 45px;
    margin-bottom: 5px;
    }
    .left{
    font-size: 15px;
    padding-left: 15px;
    font-weight: bold;
    }
    .right{
     padding-right: 15px;
    }
    .new_body{
    border: 2px solid #e4e4e4;
    margin-bottom: 30px;
    
    }
    </style>
          <h2> Данные по доставке</h2>
         <div class="new_body">
        ';
    if(!empty($order->get_meta('_shipping__transport-company'))){
        if(!empty($order->get_meta('_shipping__transport-company'))){
            $new_table_shipping .= '
            <div class="cust_div" style= "margin-top: 30px;">
                <span class="left" > Транспортная компания : </span>
                <span class="right"> '.$order->get_meta('_shipping__transport-company').'</span>
            </div>
            ';
        }

        if(!empty($order->get_meta('_shipping__vid-transporta'))){

            $new_table_shipping .= '
              <div class="cust_div">
                <span class="left" >Вид транспорта :</span>
                <span class="right"> '.$order->get_meta('_shipping__vid-transporta').'</span>
            </div>
            ';
        }

        if(!empty($order->get_meta('_shipping__fio-gruzopoluchatelya'))){
            $new_table_shipping .= '
             <div class="cust_div">
                <span class="left" > ФИО грузополучателя :</span>
                <span class="right"> '.$order->get_meta('_shipping__fio-gruzopoluchatelya').'</span>
            </div>
            ';
        }
        if(!empty($order->get_meta('_shipping__phone-gruzopoluchatelya'))){
            $new_table_shipping .= '
              <div class="cust_div">
                <span class="left" > Контактный телефон получателя : </span>
                <span class="right"> '.$order->get_meta('_shipping__phone-gruzopoluchatelya').'</span>
            </div>
            ';
        }
        $full_address = get_full_address($order->get_id());
        if(!empty($full_address)){
            $new_table_shipping .= '
         <div class="cust_div">
                <span class="left" >Адрес доставки :</span>
                <span class="right"> '.$full_address.'</span>
            </div>
            ';
        }

    }else{
        $new_table_shipping .= 'Данные по доставке не указанны';

    }
    $new_table_shipping .= '</div>';
    echo  $new_table_shipping;

    function get_full_address($id){

        global $wpdb;
        $address = $wpdb->get_results( "SELECT meta_value FROM `wp_postmeta` WHERE `post_id` = '".$id."' AND `meta_key` = '_shipping_address_index'" );
        return $address[0] ->meta_value;
    }