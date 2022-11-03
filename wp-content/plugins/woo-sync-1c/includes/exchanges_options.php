<?php

class wc1c_options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    private $tab_products;
    private $tab_orders;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->tab_products = 'wc1c-products-options';
        $this->tab_orders = 'wc1c-orders-options';

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));

        //1C GUID
        add_action('woocommerce_product_options_advanced', array($this, 'wc1c_woo_guid_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_wc1c_field'));
    }

    /**
     * Add a select Field at the bottom
     */
    function wc1c_woo_guid_fields()
    {
        $select_field = array(
            'id' => '_wc1c_guid',
            'label' => __('1C guid'),
            'desc_tip' => true,
            'description' => __('GUID product for sync with 1C.'),
        );
        woocommerce_wp_text_input($select_field);


        $select_dates = array(
            'id' => 'planned_date',
            'label' => __('Плановая дата поставки'),
            'desc_tip' => true,
            'description' => __('Planned date'),
        );

        new_wp_text_input($select_dates);



        $select_date = array(
            'id' => 'first_date',
            'label' => __('Дата первого поступления'),
            'desc_tip' => true,
            'description' => __('first date'),
        );
        new_wp_text_input($select_date);


        $chech_field = array(
            'id' => '_wc1c_standatr_module',
            'label' => __('Standart module'),
            'description' => __('Add gift label', 'woocommerce'),
        );

        woocommerce_wp_checkbox($chech_field);
    }

    /**
     * @param $post_id
     */
    function save_wc1c_field($post_id)
    {
        $wc1c_field_value = isset($_POST['_wc1c_guid']) ? $_POST['_wc1c_guid'] : '';

        $product = wc_get_product($post_id);
        $product->update_meta_data('_wc1c_guid', $wc1c_field_value);
        $product->save();
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Настройка Woo Exchange 1C',
            'Настройки обмена с 1С',
            'manage_options',
            'wc1c-setting-admin',
            array($this, 'wc1c_setting_page')
        );
    }

    /**
     * Options page callback
     */
    public function wc1c_setting_page()
    {
        // Set class property
        $this->options = get_option('wc1c_options');
        ?>
        <div class="wrap">
            <?php
            $active_tab = $this->tab_products;
            if (isset($_GET["tab"])) {
                if ($_GET["tab"] == $this->tab_orders) {
                    $active_tab = $this->tab_orders;
                } else {
                    $active_tab = $this->tab_products;
                }
            }

            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=wc1c-setting-admin&tab=<?php echo $this->tab_products; ?>"
                   class="nav-tab <?php if ($active_tab == $this->tab_products) {
                       echo 'nav-tab-active';
                   } ?> "><?php _e('Products', 'woo-sync-1c'); ?></a>
                <a href="?page=wc1c-setting-admin&tab=<?php echo($this->tab_orders); ?>"
                   class="nav-tab <?php if ($active_tab == $this->tab_orders) {
                       echo 'nav-tab-active';
                   } ?>"><?php _e('Orders', 'woo-sync-1c'); ?></a>
            </h2>

            <form method="post" action="options.php">
                <?php

                settings_fields('wc-option-group');
                do_settings_sections('wc1c-setting-page');

                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'wc-option-group',
            'wc1c_options',
            array($this, 'wc1c_save_settings')
        );

        if ($_GET['tab'] === $this->tab_orders) {
            add_settings_section(
                'wc1c-section-orders',
                'Обмен заказами',
                null,
                'wc1c-setting-page'
            );
            add_settings_field(
                'wc1c_last_order_date_commited',
                'Дата последней загрузки заказов',
                array($this, 'wc1c_last_order_date_commited_callback'),
                'wc1c-setting-page',
                'wc1c-section-orders'
            );
            add_settings_field(
                'wc1c_load_orders',
                'Обновлять данные заказов из 1С',
                array($this, 'wc1c_load_orders_callback'),
                'wc1c-setting-page',
                'wc1c-section-orders'
            );
        } else {
            add_settings_section(
                'wc1c-section-common',
                'Общие настройки',
                null,
                'wc1c-setting-page'
            );
            add_settings_field(
                'wc1c_standart_module',
                'Стандартный модуль',
                array($this, 'wc1c_standart_module_callback'),
                'wc1c-setting-page',
                'wc1c-section-common'
            );

            add_settings_section(
                'wc1c-section-products',
                'Загрузка товаров',
                null,
                'wc1c-setting-page'
            );
            add_settings_field(
                'wc1c_new_post_status',
                'Статус нового товара',
                array($this, 'wc1c_new_post_status_callback'),
                'wc1c-setting-page',
                'wc1c-section-products'
            );
            // #000018198
            add_settings_field(
                'wc1c_product_base_price',
                'Вид базовой цены',
                array($this, 'wc1c_product_base_price_callback'),
                'wc1c-setting-page',
                'wc1c-section-products'
            );

            add_settings_field(
                'wc1c_product_regular_price',
                'Вид цены',
                array($this, 'wc1c_product_regular_price_callback'),
                'wc1c-setting-page',
                'wc1c-section-products'
            );

            add_settings_field(
                'wc1c_product_sale_price',
                'Вид цены со скидкой',
                array($this, 'wc1c_product_sale_price_callback'),
                'wc1c-setting-page',
                'wc1c-section-products'
            );

            add_settings_field(
                'wc1c_action_after_full_load',
                'Что делать с товарами не попавшими  в обмен',
                array($this, 'wc1c_action_after_full_load_callback'),
                'wc1c-setting-page',
                'wc1c-section-products'
            );
        }
    }

    /**
     * @param $input
     * @return array
     */
    public function wc1c_save_settings($input)
    {
        $new_input = get_option('wc1c_options');

        if (isset($input['wc1c_new_post_status'])) {
            $new_input['wc1c_new_post_status'] = $input['wc1c_new_post_status'];
        }

        if (isset($input['wc1c_action_after_full_load'])) {
            $new_input['wc1c_action_after_full_load'] = $input['wc1c_action_after_full_load'];
        }

        if (isset($input['wc1c_product_base_price']))
            $new_input['wc1c_product_base_price'] = $input['wc1c_product_base_price'];

        if (isset($input['wc1c_product_regular_price']))
            $new_input['wc1c_product_regular_price'] = $input['wc1c_product_regular_price'];

        if (isset($input['wc1c_product_sale_price']))
            $new_input['wc1c_product_sale_price'] = $input['wc1c_product_sale_price'];

        if (isset($input['wc1c_last_order_date_commited'])) {
            $date_commited = $input['wc1c_last_order_date_commited'];
            $date_commited_h = $input['wc1c_last_order_date_commited_h'];
            $date_commited_m = $input['wc1c_last_order_date_commited_m'];
            $date_commited_s = $input['wc1c_last_order_date_commited_s'];

            $new_input['wc1c_last_order_date_commited'] = "$date_commited $date_commited_h:$date_commited_m:$date_commited_s";
        }

        if (isset($input['wc1c_standart_module']))
            $new_input['wc1c_standart_module']  = ($input['wc1c_standart_module'] === 'on') ? true : false;

        if (isset($input['wc1c_load_orders']))
            $new_input['wc1c_load_orders'] = (input['wc1c_load_orders'] === 'on') ? true : false;

        return $new_input;
    }

    public function wc1c_new_post_status_callback()
    {
        $post_status = isset($this->options['wc1c_new_post_status']) ? $this->options['wc1c_new_post_status'] : 'publish';
        $arStatus = array(
            'publish' => 'Опубликовано',
            'pending' => 'На утверждении',
            'draft' => 'Черновик',
        );

        ?> <select id="wc1c_new_post_status" name="wc1c_options[wc1c_new_post_status]"> <?php
        foreach ($arStatus as $statusKey => $statusValue) {
            $attr_sel = ($statusKey === $post_status) ? 'selected="selected"' : '';
            echo("<option $attr_sel  value='$statusKey'>$statusValue</option>");
        }
        ?></select><?php
    }

    public function wc1c_action_after_full_load_callback()
    {
        $action_product = isset($this->options['wc1c_action_after_full_load']) ? $this->options['wc1c_action_after_full_load'] : 0;
        $arAction = array('nothing' => "Ничего", 'deactivate' => "Деактивировать", 'remove' => "Удалять");

        ?> <select id="wc1c_action_after_full_load" name="wc1c_options[wc1c_action_after_full_load]"> <?php
        foreach ($arAction as $statusKey => $statusValue) {
            $attr_sel = ($statusKey === $action_product) ? 'selected="selected"' : '';
            echo("<option $attr_sel  value='$statusKey'>$statusValue</option>");
        }
        ?></select><?php
    }

    public function wc1c_standart_module_callback()
    {
        $standart_module = isset($this->options['wc1c_standart_module']) ? $this->options['wc1c_standart_module'] : false;

        ?>
        <input type="checkbox" id="wc1c_standart_module" name="wc1c_options[wc1c_standart_module]"
        <?php if ($standart_module) echo "checked" ?>
        <?php
    }

    public function wc1c_load_orders_callback()
    {
        $standart_module = isset($this->options['wc1c_load_orders']) ? $this->options['wc1c_load_orders'] : false;

        ?>
        <input type="checkbox" id="wc1c_load_orders" name="wc1c_options[wc1c_load_orders]"
        <?php if ($standart_module) echo "checked" ?>
        <?php
    }

    public function wc1c_last_order_date_commited_callback()
    {
        if (isset($this->options['wc1c_last_order_date_commited'])) {
            $last_order_date = $this->options['wc1c_last_order_date_commited'];
            list($date_commite, $time_commite) = explode(" ", $last_order_date, 2);
            list($time_h, $time_m, $time_s) = explode(":", $time_commite, 3);
        } else {
            $date_commite = "";
            $time_h = "";
            $time_m = "";
            $time_s = "";
        }

        echo "<input type='date' id='datepicker' name='wc1c_options[wc1c_last_order_date_commited]' value=$date_commite class='example-datepicker' />";

        echo "<input type='number' class='hour' placeholder='ч' name='wc1c_options[wc1c_last_order_date_commited_h]' min='0' max='23' step='1' value=$time_h pattern='([01]?[0-9]{1}|2[0-3]{1})'>";
        echo "<input type='number' class='minute' placeholder='м' name='wc1c_options[wc1c_last_order_date_commited_m]' min='0' max='59' step='1' value=$time_m pattern='[0-5]{1}[0-9]{1}'>";
        echo "<input type='number' class='secondary' placeholder='c' name='wc1c_options[wc1c_last_order_date_commited_s]' min='0' max='59' step='1' value=$time_s pattern='[0-5]{1}[0-9]{1}'>";
    }

    public function wc1c_product_base_price_callback(){
        $option_name = 'wc1c_product_base_price';
        $option_val= isset($this->options[$option_name]) ? $this->options[$option_name] : '';

        $product_prices = get_option('wc1c_prices',array());
        ?><select id="wc1c_product_regular_price" name="wc1c_options[wc1c_product_base_price]" ><?php
        foreach ($product_prices as $price_key=>$price_val) {
            $sel_option = ($option_val===$price_key) ? 'selected="selected"' : '';
            ?> <option <?php echo $sel_option ?> label="<?php echo $price_val['Наименование'] ?>" value="<?php echo $price_key ?>"><?php echo $price_val['Наименование'] ?></option> <?php
        }
        ?></select><?php
    }

    public function wc1c_product_regular_price_callback(){
        $option_name = 'wc1c_product_regular_price';
        $option_val= isset($this->options[$option_name]) ? $this->options[$option_name] : '';

        $product_prices = get_option('wc1c_prices',array());
        ?><select id="wc1c_product_regular_price" name="wc1c_options[wc1c_product_regular_price]" ><?php
        foreach ($product_prices as $price_key=>$price_val) {
            $sel_option = ($option_val===$price_key) ? 'selected="selected"' : '';
            ?> <option <?php echo $sel_option ?> label="<?php echo $price_val['Наименование'] ?>" value="<?php echo $price_key ?>"><?php echo $price_val['Наименование'] ?></option> <?php
        }
        ?></select><?php
    }

    public function wc1c_product_sale_price_callback(){
        $option_name = 'wc1c_product_sale_price';
        $option_val= isset($this->options[$option_name]) ? $this->options[$option_name] : '';

        $product_prices = get_option('wc1c_prices',array());
        ?><select id="wc1c_product_sale_price" name="wc1c_options[<?php echo $option_name ?>]" ><?php
        foreach ($product_prices as $price_key=>$price_val) {
            $sel_option = ($option_val===$price_key) ? 'selected="selected"' : '';
            ?> <option <?php echo $sel_option ?> label="<?php echo $price_val['Наименование'] ?>" value="<?php echo $price_key ?>"><?php echo $price_val['Наименование'] ?></option> <?php
        }
        ?></select><?php
    }

    /**
     * Save default options
     */
    public static function wc1c_generate_options()
    {
        update_option("wc1c_options", array(
            'wc1c_new_post_status' => 'publish',
            'wc1c_action_after_full_load' => 'nothing',
            'wc1c_standart_module' => false,
            'wc1c_load_orders' => false,
            'wc1c_product_base_price' => '',
            'wc1c_product_regular_price' => '',
            'wc1c_product_sale_price' => '',
            'wc1c_last_order_date_commited' => "0001-01-01",
        ));
    }

    /**
     * @return array|mixed|void
     */
    public static function wc1c_get_options()
    {
        $wc1c_ar_options = get_option('wc1c_options');
        if (empty($wc1c_ar_options)) {
            self::wc1c_generate_options();
        }
        $wc1c_ar_options = get_option('wc1c_options');
        return $wc1c_ar_options;
    }

    /**
     * @param array $options
     */
    public static function wc1c_save_options($options)
    {
        update_option('wc1c_options',$options);
    }

}