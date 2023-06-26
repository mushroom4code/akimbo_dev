<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The main class of the plugin YML for Yandex Market
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			3.9.0
 * 
 * @version			1.0.0 (31-01-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param		
 *
 * @return		
 *
 * @depends			classes:	YFYM_Data_Arr
 *								YFYM_Settings_Page
 *								YFYM_Debug_Page
 *								YFYM_Error_Log
 *								YFYM_Generation_XML
 *					traits:	
 *					methods:	
 *					functions:	common_option_get
 *								common_option_upd
 *					constants:	YFYM_PLUGIN_VERSION
 *								YFYM_PLUGIN_BASENAME
 *								YFYM_PLUGIN_DIR_URL
 *					options:	
 *
 */

final class YmlforYandexMarket {
	private $plugin_version = YFYM_PLUGIN_VERSION; // 1.0.0

	private $site_uploads_url = YFYM_SITE_UPLOADS_URL; // http://site.ru/wp-content/uploads
	private $site_uploads_dir_path = YFYM_SITE_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads	
	private $plugin_upload_dir_url = YFYM_PLUGIN_UPLOADS_DIR_URL; // http://site.ru/wp-content/uploads/yfym/
	private $plugin_upload_dir_path = YFYM_PLUGIN_UPLOADS_DIR_PATH; // /home/site.ru/public_html/wp-content/uploads/yfym/
	private $plugin_dir_url = YFYM_PLUGIN_DIR_URL; // http://site.ru/wp-content/plugins/yml-for-yandex-market/
	private $plugin_dir_path = YFYM_PLUGIN_DIR_PATH; // /home/p135/www/site.ru/wp-content/plugins/yml-for-yandex-market/
	private $plugin_main_file_path = YFYM_PLUGIN_MAIN_FILE_PATH; // /home/p135/www/site.ru/wp-content/plugins/yml-for-yandex-market/yml-for-yandex-market.php
	private $plugin_slug = YFYM_PLUGIN_SLUG; // yml-for-yandex-market - псевдоним плагина
	private $plugin_basename = YFYM_PLUGIN_BASENAME; // yml-for-yandex-market/yml-for-yandex-market.php - полный псевдоним плагина (папка плагина + имя главного файла)

	protected static $instance;
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Срабатывает при активации плагина (вызывается единожды)
	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$name_dir = YFYM_SITE_UPLOADS_DIR_PATH . '/yfym';
		if ( ! is_dir( $name_dir ) ) {
			if ( ! mkdir( $name_dir ) ) {
				error_log( 'ERROR: Ошибка создания папки ' . $name_dir . '; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__, 0 );
			}
		}
		$feed_id = '1'; // (string)
		$name_dir = YFYM_SITE_UPLOADS_DIR_PATH . '/yfym/feed' . $feed_id;
		if ( ! is_dir( $name_dir ) ) {
			if ( ! mkdir( $name_dir ) ) {
				error_log( 'ERROR: Ошибка создания папки ' . $name_dir . '; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__, 0 );
			}
		}

		yfym_optionADD( 'yfym_status_sborki', '-1', $feed_id ); // статус сборки файла

		$yfym_registered_feeds_arr = array(
			0 => array( 'last_id' => '1' ),
			1 => array( 'id' => '1' )
		);

		$def_plugin_date_arr = new YFYM_Data_Arr();
		$yfym_settings_arr = [];
		$yfym_settings_arr['1'] = $def_plugin_date_arr->get_opts_name_and_def_date( 'all' );

		if ( is_multisite() ) {
			add_blog_option( get_current_blog_id(), 'yfym_version', YFYM_PLUGIN_VERSION );
			add_blog_option( get_current_blog_id(), 'yfym_keeplogs', '' );
			add_blog_option( get_current_blog_id(), 'yfym_disable_notices', '' );
			add_blog_option( get_current_blog_id(), 'yfym_enable_five_min', '' );
			add_blog_option( get_current_blog_id(), 'yfym_feed_content', '' );

			add_blog_option( get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr );
			add_blog_option( get_current_blog_id(), 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr );
		} else {
			add_option( 'yfym_version', YFYM_PLUGIN_VERSION );
			add_option( 'yfym_keeplogs', '' );
			add_option( 'yfym_disable_notices', '' );
			add_option( 'yfym_enable_five_min', '' );
			add_option( 'yfym_feed_content', '' );

			add_option( 'yfym_settings_arr', $yfym_settings_arr );
			add_option( 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr );
		}
	}

	// Срабатывает при отключении плагина (вызывается единожды)
	public static function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$yfym_registered_feeds_arr = yfym_optionGET( 'yfym_registered_feeds_arr' );
		for ( $i = 1; $i < count( $yfym_registered_feeds_arr ); $i++ ) { // с единицы, т.к инфа по конкретным фидам там
			$feed_id = $yfym_registered_feeds_arr[ $i ]['id'];
			wp_clear_scheduled_hook( 'yfym_cron_period', [ $feed_id ] ); // отключаем крон
			wp_clear_scheduled_hook( 'yfym_cron_sborki', [ $feed_id ] ); // отключаем крон
		}

		deactivate_plugins( 'yml-for-yandex-market-aliexpress-export/yml-for-yandex-market-aliexpress-export.php' );
		deactivate_plugins( 'yml-for-yandex-market-book-export/yml-for-yandex-market-book-export.php' );
		deactivate_plugins( 'yml-for-yandex-market-pro/yml-for-yandex-market-pro.php' );
		deactivate_plugins( 'yml-for-yandex-market-prom-export/yml-for-yandex-market-prom-export.php' );
		deactivate_plugins( 'yml-for-yandex-market-promos-export/yml-for-yandex-market-promos-export.php' );
		deactivate_plugins( 'yml-for-yandex-market-rozetka-export/yml-for-yandex-market-rozetka-export.php' );
	}

	public function __construct() {
		$this->check_and_fix(); // если вдруг нет настроек плагина
		$this->check_options_upd(); // проверим, нужны ли обновления опций плагина
		$this->init_classes();
		$this->init_hooks(); // подключим хуки
	}

	public function check_and_fix() {
		$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
		if ( ! is_array( $yfym_settings_arr ) ) {
			self::on_activation();
		}
	}

	public function init_classes() {
		return;
	}

	public function check_options_upd() {
		if ( false == common_option_get( 'yfym_version' ) ) { // это первая установка
			if ( is_multisite() ) {
				update_blog_option( get_current_blog_id(), 'yfym_version', $this->plugin_version );
			} else {
				update_option( 'yfym_version', $this->plugin_version );
			}
		} else {
			$this->set_new_options();
		}
	}

	public function set_new_options() {
		// Если предыдущая версия плагина меньше текущей
		if ( version_compare( $this->get_plugin_version(), $this->plugin_version, '<' ) ) {

		} else { // обновления не требуются
			return;
		}

		// Если предыдущая версия плагина меньше 3.7.0
		if ( version_compare( $this->get_plugin_version(), '3.7.0', '<' ) ) {
			$yfym_registered_feeds_arr = array(
				0 => array( 'last_id' => '5' ),
				1 => array( 'id' => '1' ),
				2 => array( 'id' => '2' ),
				3 => array( 'id' => '3' ),
				4 => array( 'id' => '4' ),
				5 => array( 'id' => '5' )
			);
			if ( is_multisite() ) {
				add_blog_option( get_current_blog_id(), 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr );
			} else {
				add_option( 'yfym_registered_feeds_arr', $yfym_registered_feeds_arr );
			}
		}

		$yfym_data_arr_obj = new YFYM_Data_Arr();
		$opts_arr = $yfym_data_arr_obj->get_opts_name_and_def_date_obj( 'all' ); // список дефолтных настроек
		// проверим, заданы ли дефолтные настройки
		$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
		$yfym_settings_arr_keys_arr = array_keys( $yfym_settings_arr );
		for ( $i = 0; $i < count( $yfym_settings_arr_keys_arr ); $i++ ) {
			$feed_id = (string) $yfym_settings_arr_keys_arr[ $i ];
			for ( $n = 0; $n < count( $opts_arr ); $n++ ) {
				$name = $opts_arr[ $n ]->get_name();
				$value = $opts_arr[ $n ]->get_value();
				if ( ! isset( $yfym_settings_arr[ $feed_id ][ $name ] ) ) {
					yfym_optionUPD( $name, $value, $feed_id, 'yes', 'set_arr' );
				}
			}
		}

		if ( is_multisite() ) {
			update_blog_option( get_current_blog_id(), 'yfym_version', $this->plugin_version );
		} else {
			update_option( 'yfym_version', $this->plugin_version );
		}
	}

	public function get_plugin_version() {
		if ( is_multisite() ) {
			$v = get_blog_option( get_current_blog_id(), 'yfym_version' );
		} else {
			$v = get_option( 'yfym_version' );
		}
		return $v;
	}

	public function init_hooks() {
		add_action( 'admin_init', array( $this, 'listen_submits_func' ), 10 ); // ещё можно слушать чуть раньше на wp_loaded
		add_action( 'admin_menu', array( $this, 'add_admin_menu_func' ), 10, 1 );

		add_filter( 'upload_mimes', array( $this, 'add_mime_types_func' ), 99, 1 ); // чутка позже остальных
		add_filter( 'cron_schedules', array( $this, 'add_cron_intervals_func' ), 10, 1 );

		add_action( 'yfym_cron_sborki', array( $this, 'yfym_do_this_seventy_sec' ), 10, 1 );
		add_action( 'yfym_cron_period', array( $this, 'yfym_do_this_event' ), 10, 1 );

		// индивидуальные опции доставки товара
		add_action( 'save_post', array( $this, 'save_post_product_func' ), 50, 3 );
		// пришлось юзать save_post вместо save_post_product ибо wc блочит обновы
		// https://wpruse.ru/woocommerce/custom-fields-in-products/
		// https://wpruse.ru/woocommerce/custom-fields-in-variations/
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'yfym_added_wc_tabs' ), 10, 1 );
		add_filter( 'yfymp_request_string_filter', array( $this, 'yfym_for_сompatibility_with_yandex_zen_plug_func' ), 10, 3 );
		add_action( 'admin_footer', array( $this, 'yfym_art_added_tabs_icon' ), 10, 1 );
		add_action( 'woocommerce_product_data_panels', array( $this, 'yfym_art_added_tabs_panel' ), 10, 1 );
		/* ! */add_action( 'woocommerce_process_product_meta', array( $this, 'yfym_art_woo_custom_fields_save' ), 10, 1 );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'yfym_woocommerce_product_options_general_product_data' ), 10, 1 );

		add_action( 'admin_notices', array( $this, 'print_admin_notices_func' ), 10, 1 );

		/* Регаем стили только для страницы настроек плагина */
		add_action( 'admin_init', function () {
			wp_register_style( 'yfym-admin-css', YFYM_PLUGIN_DIR_URL . 'css/yfym_style.css' );
		}, 9999 );

		add_filter( 'plugin_action_links', array( $this, 'yfym_plugin_action_links' ), 10, 2 );

		add_filter( 'yfymae_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );
		add_filter( 'yfymbe_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );
		add_filter( 'yfymp_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );
		add_filter( 'yfympr_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );
		add_filter( 'yfympe_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );
		add_filter( 'yfymre_args_filter', array( $this, 'yfym_args_filter_func' ), 10, 3 );

		// фильтры для фида майтаргет yfym_after_variable_offer_stop_flag, yfym_variable_offer_id_yml_filter
	}

	public function listen_submits_func() {
		do_action( 'yfym_listen_submits' );

		if ( isset( $_REQUEST['yfym_submit_action'] ) ) {
			$message = __( 'Updated', 'yml-for-yandex-market' );
			$class = 'notice-success';
			if ( isset( $_POST['yfym_run_cron'] ) && sanitize_text_field( $_POST['yfym_run_cron'] ) !== 'off' ) {
				$message .= '. ' . __( 'Creating the feed is running. You can continue working with the website', 'yml-for-yandex-market' );
			}

			add_action( 'admin_notices', function () use ($message, $class) {
				$this->admin_notices_func( $message, $class );
			}, 10, 2 );
		}
	}

	public function yfym_plugin_action_links( $actions, $plugin_file ) {
		if ( false === strpos( $plugin_file, YFYM_PLUGIN_BASENAME ) ) { // проверка, что у нас текущий плагин
			return $actions;
		}
		$settings_link = '<a style="color: green; font-weight: 700;" href="/wp-admin/admin.php?page=yfymextensions">' . __( 'More features', 'yml-for-yandex-market' ) . '</a>';
		array_unshift( $actions, $settings_link );
		$settings_link = '<a href="/wp-admin/admin.php?page=yfymexport">' . __( 'Settings', 'yml-for-yandex-market' ) . '</a>';
		array_unshift( $actions, $settings_link );
		return $actions;
	}

	public function yfym_args_filter_func( $args, $order_id, $order_email ) {
		$args['basic_version'] = $this->get_plugin_version();
		return $args;
	}

	public function yfym_admin_css_func() {
		wp_enqueue_style( 'yfym-admin-css' ); /* Ставим css-файл в очередь на вывод */
	}

	// Добавляем пункты меню
	public function add_admin_menu_func() {
		$page_suffix = add_menu_page( null, __( 'Export Yandex Market', 'yml-for-yandex-market' ), 'manage_woocommerce', 'yfymexport', array( $this, 'get_export_page_func' ), 'dashicons-redo', 51 );
		add_action( 'admin_print_styles-' . $page_suffix, array( $this, 'yfym_admin_css_func' ) ); // создаём хук, чтобы стили выводились только на странице настроек

		$page_suffix = add_submenu_page( 'yfymexport', __( 'Debug', 'yml-for-yandex-market' ), __( 'Debug page', 'yml-for-yandex-market' ), 'manage_woocommerce', 'yfymdebug', array( $this, 'get_debug_page_func' ) );
		add_action( 'admin_print_styles-' . $page_suffix, array( $this, 'yfym_admin_css_func' ) );

		$page_subsuffix = add_submenu_page( 'yfymexport', __( 'Add Extensions', 'yml-for-yandex-market' ), __( 'Extensions', 'yml-for-yandex-market' ), 'manage_woocommerce', 'yfymextensions', 'yfym_extensions_page' );
		require_once YFYM_PLUGIN_DIR_PATH . '/extensions.php';
		add_action( 'admin_print_styles-' . $page_subsuffix, array( $this, 'yfym_admin_css_func' ) );
	}

	// вывод страницы настроек плагина
	public function get_export_page_func() {
		new YFYM_Settings_Page();
		return;
	}

	// вывод страницы настроек плагина
	public function get_debug_page_func() {
		new YFYM_Debug_Page();
		return;
	}

	// Разрешим загрузку xml и csv файлов
	public function add_mime_types_func( $mimes ) {
		$mimes['csv'] = 'text/csv';
		$mimes['xml'] = 'text/xml';
		$mimes['yml'] = 'text/xml';
		return $mimes;
	}

	/* добавляем интервалы крон в 70 секунд и 6 часов */
	public function add_cron_intervals_func( $schedules ) {
		$schedules['seventy_sec'] = array(
			'interval' => 70,
			'display' => '70 sec'
		);
		$schedules['five_min'] = array(
			'interval' => 300,
			'display' => '5 min'
		);
		$schedules['six_hours'] = array(
			'interval' => 21600,
			'display' => '6 hours'
		);
		$schedules['week'] = array(
			'interval' => 604800,
			'display' => '1 week'
		);
		return $schedules;
	}
	/* end добавляем интервалы крон в 70 секунд и 6 часов */

	// Сохраняем данные блока, когда пост сохраняется
	public function save_post_product_func( $post_id, $post, $update ) {
		new YFYM_Error_Log( 'Стартовала функция save_post_product_func. Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );

		if ( $post->post_type !== 'product' ) {
			return;
		} // если это не товар вукомерц
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		} // если это ревизия
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} // если это автосохранение ничего не делаем
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		} // проверяем права юзера

		$post_meta_arr = array(
			'yfym_individual_delivery',
			'yfym_cost',
			'yfym_days',
			'yfym_order_before',
			'yfym_individual_pickup',
			'yfym_pickup_cost',
			'yfym_pickup_days',
			'yfym_pickup_order_before',
			'yfym_bid',
			'yfym_individual_vat',
			// 'yfym_condition',
			'_yfym_condition',
			'yfym_reason',
			'_yfym_quality',
			'_yfym_market_sku',
			'_yfym_tn_ved_code',
			'yfym_credit_template',
			'_yfym_cargo_types',
			'_yfym_supplier',
			'_yfym_min_quantity',
			'_yfym_step_quantity',
			'_yfym_premium_price',
			'_yfym_price_rrp',
			'_yfym_min_price'
		);
		$this->save_post_meta( $post_meta_arr, $post_id );

		// Убедимся что поле установлено.
		if ( isset( $_POST['yfym_cost'] ) ) {
			$yfym_recommend_stock_data_arr = [];
			$yfym_recommend_stock_data_arr['availability'] = sanitize_text_field( $_POST['_yfym_availability'] );
			$yfym_recommend_stock_data_arr['transport_unit'] = sanitize_text_field( $_POST['_yfym_transport_unit'] );
			$yfym_recommend_stock_data_arr['min_delivery_pieces'] = sanitize_text_field( $_POST['_yfym_min_delivery_pieces'] );
			$yfym_recommend_stock_data_arr['quantum'] = sanitize_text_field( $_POST['_yfym_quantum'] );
			$yfym_recommend_stock_data_arr['leadtime'] = sanitize_text_field( $_POST['_yfym_leadtime'] );
			$yfym_recommend_stock_data_arr['box_count'] = sanitize_text_field( $_POST['_yfym_box_count'] );
			if ( isset( $_POST['_delivery_weekday_arr'] ) && ! empty( $_POST['_delivery_weekday_arr'] ) ) {
				$yfym_recommend_stock_data_arr['delivery_weekday_arr'] = $_POST['_delivery_weekday_arr'];
			} else {
				$yfym_recommend_stock_data_arr['delivery_weekday_arr'] = [];
			}
			// Обновляем данные в базе данных
			update_post_meta( $post_id, '_yfym_recommend_stock_data_arr', $yfym_recommend_stock_data_arr );
		}

		// нужно ли запускать обновление фида при перезаписи файла
		$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
		$yfym_settings_arr_keys_arr = array_keys( $yfym_settings_arr );
		for ( $i = 0; $i < count( $yfym_settings_arr_keys_arr ); $i++ ) {
			$feed_id = $yfym_settings_arr_keys_arr[ $i ];

			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Шаг $i = ' . $i . ' цикла по формированию кэша файлов; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );

			$result_get_unit_obj = new YFYM_Get_Unit( $post_id, $feed_id ); // формируем фид товара
			$result_xml = $result_get_unit_obj->get_result();
			$ids_in_xml = $result_get_unit_obj->get_ids_in_xml();

			yfym_wf( $result_xml, $post_id, $feed_id, $ids_in_xml ); // записываем кэш-файл

			// нужно ли запускать обновление фида при перезаписи файла
			$yfym_ufup = yfym_optionGET( 'yfym_ufup', $feed_id, 'set_arr' );
			if ( $yfym_ufup !== 'on' ) {
				new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Шаг $yfym_ufup = ' . $yfym_ufup . '. Пересборка фида не требуется; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
				continue;
			} else {
				new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Шаг $yfym_ufup = ' . $yfym_ufup . '. Пересборка требуется; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
			}
			$status_sborki = (int) yfym_optionGET( 'yfym_status_sborki', $feed_id );
			if ( $status_sborki > -1 ) {
				continue;
			} // если идет сборка фида - пропуск

			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Пересборка запускается; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );

			$yfym_date_save_set = yfym_optionGET( 'yfym_date_save_set', $feed_id, 'set_arr' );
			$yfym_date_sborki = yfym_optionGET( 'yfym_date_sborki', $feed_id, 'set_arr' );

			// ! т.к у нас работа с array_keys, то в $feed_id может быть int, а не string значит двойное равенство лучше
			if ( $feed_id == '1' ) {
				$prefFeed = '';
			} else {
				$prefFeed = $feed_id;
			}
			$yfym_file_extension = yfym_optionGET( 'yfym_file_extension', $feed_id, 'set_arr' );
			if ( $yfym_file_extension == '' ) {
				$yfym_file_extension = 'xml';
			}
			if ( is_multisite() ) {
				$filenamefeed = YFYM_SITE_UPLOADS_DIR_PATH . "/" . $prefFeed . "feed-yml-" . get_current_blog_id() . "." . $yfym_file_extension;
			} else {
				$filenamefeed = YFYM_SITE_UPLOADS_DIR_PATH . "/" . $prefFeed . "feed-yml-0." . $yfym_file_extension;
			}
			if ( ! file_exists( $filenamefeed ) ) {
				new YFYM_Error_Log( 'FEED № ' . $feed_id . '; WARNING: Файла filenamefeed = ' . $filenamefeed . ' не существует! Пропускаем быструю сборку; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
				continue;
			} // файла с фидом нет

			clearstatcache(); // очищаем кэш дат файлов
			$last_upd_file = filemtime( $filenamefeed );
			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; $yfym_date_save_set=' . $yfym_date_save_set . ';$filenamefeed=' . $filenamefeed, 0 );
			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Начинаем сравнивать даты! Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
			if ( $yfym_date_save_set > $last_upd_file ) {
				// настройки фида сохранялись позже, чем создан фид		
				// нужно полностью пересобрать фид
				new YFYM_Error_Log( 'FEED № ' . $feed_id . '; NOTICE: Настройки фида сохранялись позже, чем создан фид; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
				$arr_maybe = array( 'off', 'five_min', 'hourly', 'six_hours', 'twicedaily', 'daily', 'week' );
				$yfym_run_cron = yfym_optionGET( 'yfym_status_cron', $feed_id, 'set_arr' );
				if ( in_array( $yfym_run_cron, $arr_maybe ) ) {
					if ( $yfym_run_cron === 'off' ) {
					} else {
						$feedid = (string) $feed_id; // для правильности работы важен тип string!
						$recurrence = $yfym_run_cron;
						wp_clear_scheduled_hook( 'yfym_cron_period', array( $feedid ) );
						wp_schedule_event( time(), $recurrence, 'yfym_cron_period', array( $feedid ) );
						new YFYM_Error_Log( 'FEED № ' . $feedid . '; Для полной пересборки после быстрого сохранения yfym_cron_period внесен в список заданий; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
					}
				} else {
					new YFYM_Error_Log( 'FEED № ' . $feed_id . '; ERROR: Крон ' . $yfym_run_cron . ' не зарегистрирован. Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
				}
			} else { // нужно лишь обновить цены
				$feed_id = (string) $feed_id;
				new YFYM_Error_Log( 'FEED № ' . $feed_id . '; NOTICE: Настройки фида сохранялись раньше, чем создан фид. Нужно лишь обновить цены; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
				$generation = new YFYM_Generation_XML( $feed_id );
				$generation->clear_file_ids_in_xml( $feed_id );
				$generation->onlygluing();
			}
		}
		return;
	}

	public static function yfym_added_wc_tabs( $tabs ) {
		$tabs['yfym_special_panel'] = [ 
			'label' => __( 'YML for Yandex Market', 'yml-for-yandex-market' ), // название вкладки
			'target' => 'yfym_added_wc_tabs', // идентификатор вкладки
			'class' => [ 'hide_if_grouped' ], // классы управления видимостью вкладки в зависимости от типа товара
			'priority' => 70, // приоритет вывода
		];
		return $tabs;
	}

	public static function yfym_art_added_tabs_icon() {
		// https://rawgit.com/woothemes/woocommerce-icons/master/demo.html 
		?>
		<style>
			#woocommerce-coupon-data ul.wc-tabs li.yfym_special_panel_options a::before,
			#woocommerce-product-data ul.wc-tabs li.yfym_special_panel_options a::before,
			.woocommerce ul.wc-tabs li.yfym_special_panel_options a::before {
				content: "\f172";
			}
		</style>
		<?php
	}
	public static function yfym_art_added_tabs_panel() {
		global $post; ?>
		<div id="yfym_added_wc_tabs" class="panel woocommerce_options_panel">
			<?php do_action( 'yfym_prepend_options_panel', $post ); ?>
			<div class="options_group">
				<h2><strong>
						<?php _e( 'Individual product settings for YML-feed', 'yml-for-yandex-market' ); ?>
					</strong></h2>
				<h2>
					<?php _e( 'Here you can set up individual options terms for this product', 'yml-for-yandex-market' ); ?>. <a
						target="_blank" href="//yandex.ru/support/partnermarket/elements/delivery-options.html#structure">
						<?php _e( 'Read more on Yandex', 'yml-for-yandex-market' ); ?>
					</a>
				</h2>
				<?php do_action( 'yfym_prepend_options_group_1', $post ); ?>
				<?php
				woocommerce_wp_select( [ 
					'id' => 'yfym_individual_delivery',
					'label' => __( 'Delivery', 'yml-for-yandex-market' ),
					'options' => [ 
						'off' => __( 'Disabled', 'yml-for-yandex-market' ),
						'false' => 'False',
						'true' => 'True'
					],
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>delivery</strong>'
				] );

				// цифровое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_cost',
					'label' => __( 'Delivery cost', 'yml-for-yandex-market' ),
					//	'placeholder' => '1',
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>cost</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>delivery-option</strong>',
					'type' => 'number',
					'custom_attributes' => [ 
						'step' => 'any',
						'min' => '0'
					]
				] );

				// текстовое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_days',
					'label' => __( 'Delivery days', 'yml-for-yandex-market' ),
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>days</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>delivery-option</strong>',
					'type' => 'text'
				] );

				// текстовое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_order_before',
					'label' => __( 'The time', 'yml-for-yandex-market' ),
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>order-before</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>delivery-option</strong>. ' . __( 'The time in which you need to place an order to get it at this time', 'yml-for-yandex-market' ),
					//	'desc_tip' => 'true',
					'type' => 'text'
				] );

				?>
				<?php do_action( 'yfym_append_options_group_1', $post ); ?>
			</div>
			<?php do_action( 'yfym_append_options_between_group_1_2', $post ); ?>
			<div class="options_group">
				<h2>
					<?php _e( 'Here you can configure the pickup conditions for this product', 'yml-for-yandex-market' ); ?>
				</h2>
				<?php do_action( 'yfym_prepend_options_group_2', $post ); ?>
				<?php
				woocommerce_wp_select( [ 
					'id' => 'yfym_individual_pickup',
					'label' => __( 'Pickup', 'yml-for-yandex-market' ),
					'options' => [ 
						'off' => __( 'Disabled', 'yml-for-yandex-market' ),
						'false' => 'False',
						'true' => 'True'
					],
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>pickup</strong>'
				] );

				// цифровое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_pickup_cost',
					'label' => __( 'Pickup cost', 'yml-for-yandex-market' ),
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>cost</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>pickup-options</strong>',
					'type' => 'number',
					'custom_attributes' => [ 
						'step' => 'any',
						'min' => '0'
					],
				] );

				// текстовое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_pickup_days',
					'label' => __( 'Pickup days', 'yml-for-yandex-market' ),
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>days</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>pickup-options</strong>',
					'type' => 'text'
				] );

				// текстовое поле
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_pickup_order_before',
					'label' => __( 'The time', 'yml-for-yandex-market' ),
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>order-before</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>pickup-options</strong>. ' . __( 'The time in which you need to place an order to get it at this time', 'yml-for-yandex-market' ),
					'type' => 'text'
				] );

				?>
				<?php do_action( 'yfym_append_options_group_2', $post ); ?>
			</div>
			<?php do_action( 'yfym_append_options_between_group_2_3', $post ); ?>
			<div class="options_group">
				<h2>
					<?php _e( 'Bid values', 'yml-for-yandex-market' ); ?> &
					<?php _e( 'Сondition', 'yml-for-yandex-market' ); ?>
				</h2>
				<?php do_action( 'yfym_prepend_options_group_3', $post ); ?>
				<?php
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_bid',
					'label' => __( 'Bid values', 'yml-for-yandex-market' ),
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>bid</strong>. ' . __( 'Bid values in your price list. Specify the bid amount in Yandex cents: for example, the value 80 corresponds to the bid of 0.8 Yandex units. The values must be positive integers', 'yml-for-yandex-market' ) . ' <a target="_blank" href="//yandex.ru/support/partnermarket/elements/bid-cbid.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );

				woocommerce_wp_select( [ 
					'id' => 'yfym_individual_vat',
					'label' => __( 'VAT rate', 'yml-for-yandex-market' ),
					'options' => [ 
						'global' => __( 'Use global settings', 'yml-for-yandex-market' ),
						'NO_VAT' => __( 'No VAT', 'yml-for-yandex-market' ) . ' (NO_VAT)',
						'VAT_0' => '0% (VAT_0)',
						'VAT_10' => '10% (VAT_10)',
						'VAT_10_110' => '10/110 (VAT_10_110)',
						'VAT_18' => '18% (VAT_18)',
						'VAT_18_118' => '18/118 (VAT_18_118)',
						'VAT_20' => '20% (VAT_20)',
						'VAT_20_120' => '20/120 VAT_20_120)'
					],
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>vat</strong> ' . __( 'This element is used when creating an YML feed for Yandex.Delivery', 'yml-for-yandex-market' ) . ' <a target="_blank" href="//yandex.ru/support/delivery/settings/vat.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>'
				] );

				/* woocommerce_wp_select([
											'id' => 'yfym_condition',
											'label' => __('Сondition', 'yml-for-yandex-market'),
											'options' => [
												'off' => __('None', 'yml-for-yandex-market'),
												'likenew' => __('Like New', 'yml-for-yandex-market'),
												'used' => __('Used', 'yml-for-yandex-market'),	
											],
											'description' => __('Optional element', 'yml-for-yandex-market').' <strong>condition</strong>'
										]); */

				woocommerce_wp_select( [ 
					'id' => '_yfym_condition',
					'label' => __( 'Сondition', 'yml-for-yandex-market' ),
					'options' => [ 
						'default' => __( 'Default', 'yml-for-yandex-market' ),
						'disabled' => __( 'Disabled', 'yml-for-yandex-market' ),
						'showcasesample' => __( 'Showcase sample', 'yml-for-yandex-market' ) . ' (showcasesample)',
						'reduction' => __( 'Reduction', 'yml-for-yandex-market' ) . ' (reduction)',
						'fashionpreowned' => __( 'Fashionpreowned', 'yml-for-yandex-market' ) . ' (fashionpreowned)',
						'preowned' => __( 'Fashionpreowned', 'yml-for-yandex-market' ) . ' (preowned)',
						'likenew' => __( 'Like New', 'yml-for-yandex-market' ) . ' (likenew)'
					],
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>condition</strong>'
				] );

				woocommerce_wp_select( [ 
					'id' => '_yfym_quality',
					'label' => __( 'Quality', 'yml-for-yandex-market' ),
					'options' => [ 
						'default' => __( 'Default', 'yml-for-yandex-market' ),
						'perfect' => __( 'Perfect', 'yml-for-yandex-market' ),
						'excellent' => __( 'Excellent', 'yml-for-yandex-market' ),
						'good' => __( 'Good', 'yml-for-yandex-market' ),
					],
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>quality</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>condition</strong>',
					'type' => 'text'
				] );

				woocommerce_wp_text_input( [ 
					'id' => 'yfym_reason',
					'label' => __( 'Reason', 'yml-for-yandex-market' ),
					'placeholder' => '',
					'description' => __( 'Required element', 'yml-for-yandex-market' ) . ' <strong>reason</strong> ' . __( 'of attribute', 'yml-for-yandex-market' ) . ' <strong>condition</strong>',
					'type' => 'text'
				] );
				?>
				<?php do_action( 'yfym_append_options_group_3', $post ); ?>
			</div>
			<div class="options_group">
				<h2>Маркетплейс Яндекс.Маркета</h2>
				<p>
					<?php _e( 'This data is used only when creating a feed for', 'yml-for-yandex-market' ); ?> Маркетплейс
					Яндекс.Маркета
				</p>
				<?php do_action( 'yfym_prepend_options_group_other', $post ); ?>
				<?php
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_market_sku',
					'label' => __( 'Product ID on Yandex', 'yml-for-yandex-market' ),
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>market-sku</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') ' . __( 'Product ID on Yandex. You can get it after downloading the file in your personal account', 'yml-for-yandex-market' ) . '. <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_tn_ved_code',
					'label' => __( 'Code ТН ВЭД', 'yml-for-yandex-market' ),
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>tn-ved-code</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );

				if ( get_post_meta( $post->ID, '_yfym_recommend_stock_data_arr', true ) == '' ) {
					$yfym_recommend_stock_data_arr = [];
				} else {
					$yfym_recommend_stock_data_arr = get_post_meta( $post->ID, '_yfym_recommend_stock_data_arr', true );
				}
				$availability = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'availability', 'disabled' );
				$transport_unit = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'transport_unit' );
				$min_delivery_pieces = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'min_delivery_pieces' );
				$quantum = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'quantum' );
				$leadtime = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'leadtime' );
				$box_count = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'box_count' );
				$delivery_weekday_arr = yfym_data_from_arr( $yfym_recommend_stock_data_arr, 'delivery_weekday_arr', [] );

				woocommerce_wp_select( [ 
					'id' => '_yfym_availability',
					'label' => __( 'Supply plans', 'yml-for-yandex-market' ),
					'value' => $availability,
					'options' => array(
						'disabled' => __( 'Disabled', 'yml-for-yandex-market' ),
						'ACTIVE' => __( 'Supplies will', 'yml-for-yandex-market' ),
						'INACTIVE' => __( 'There will be no supplies', 'yml-for-yandex-market' ),
						'DELISTED' => __( 'Product in the archive', 'yml-for-yandex-market' ),
					),
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>availability</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_transport_unit',
					'label' => __( 'The number of products in the package (multiplicity of the box)', 'yml-for-yandex-market' ),
					'value' => $transport_unit,
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>transport-unit</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_min_delivery_pieces',
					'label' => __( 'Minimum delivery pieces', 'yml-for-yandex-market' ),
					'value' => $min_delivery_pieces,
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>min-delivery-pieces</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_quantum',
					'label' => __( 'Additional batch (quantum of delivery)', 'yml-for-yandex-market' ),
					'value' => $quantum,
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>quantum</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_leadtime',
					'label' => __( 'Lead time', 'yml-for-yandex-market' ),
					'value' => $leadtime,
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>leadtime</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_box_count',
					'label' => __( 'Box count', 'yml-for-yandex-market' ),
					'value' => $box_count,
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>box-count</strong> (' . __( 'Forbidden in Yandex Market', 'yml-for-yandex-market' ) . ') <a target="_blank" href="//yandex.ru/support/marketplace/catalog/yml-simple.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				yfym_woocommerce_wp_select_multiple( [ 
					'id' => '_delivery_weekday_arr',
					//	'wrapper_class' => 'show_if_simple', 
					'label' => __( 'Days of the week when you are ready to deliver the goods to the warehouse of the marketplace', 'yml-for-yandex-market' ),
					'value' => $delivery_weekday_arr,
					'options' => [ 
						'MONDAY' => __( 'Monday', 'yml-for-yandex-market' ),
						'TUESDAY' => __( 'Tuesday', 'yml-for-yandex-market' ),
						'WEDNESDAY' => __( 'Wednesday', 'yml-for-yandex-market' ),
						'THURSDAYy' => __( 'Thursday', 'yml-for-yandex-market' ),
						'FRIDAY' => __( 'Friday', 'yml-for-yandex-market' ),
						'SATURDAY' => __( 'Saturday', 'yml-for-yandex-market' ),
						'SUNDAY' => __( 'Sunday', 'yml-for-yandex-market' )
					]
				] );
				?>
				<?php do_action( 'yfym_append_options_group_4', $post ); ?>
			</div>
			<div class="options_group">
				<h2>
					<?php _e( 'Other', 'yml-for-yandex-market' ); ?>
				</h2>
				<?php do_action( 'yfym_prepend_options_group_other', $post ); ?>
				<?php
				woocommerce_wp_text_input( [ 
					'id' => 'yfym_credit_template',
					'label' => __( 'Credit program identifier', 'yml-for-yandex-market' ),
					'placeholder' => '',
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>credit-template</strong> <a target="_blank" href="//yandex.ru/support/partnermarket/efficiency/credit.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_select( [ 
					'id' => '_yfym_cargo_types',
					'label' => 'Cargo types',
					'options' => [ 
						'default' => __( 'Default', 'yml-for-yandex-market' ),
						'disabled' => __( 'Disabled', 'yml-for-yandex-market' ),
						'yes' => 'CIS_REQUIRED'
					],
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>cargo-types</strong> <a target="_blank" href="//yandex.ru/support/partnermarket-dsbs/orders/cis.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_supplier',
					'label' => 'ОГРН/ОГРНИП ' . __( 'of a third-party seller', 'yml-for-yandex-market' ),
					'description' => __( 'Optional element', 'yml-for-yandex-market' ) . ' <strong>supplier</strong>. <a target="_blank" href="//yandex.ru/support/partnermarket/registration/marketplace.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_min_quantity',
					'label' => __( 'Minimum number of products per order', 'yml-for-yandex-market' ),
					'description' => __( 'For these categories only', 'yml-for-yandex-market' ) . ': "Автошины", "Грузовые шины", "Мотошины", "Диски" <strong>min-quantity</strong>. <a target="_blank" href="//yandex.ru/support/partnermarket/elements/min-quantity.html">' . __( 'Read more on Yandex', 'yml-for-yandex-market' ) . '</a>',
					'type' => 'text'
				] );
				woocommerce_wp_text_input( [ 
					'id' => '_yfym_step_quantity',
					'label' => 'step-quantity',
					'description' => __( 'For these categories only', 'yml-for-yandex-market' ) . ': "Автошины", "Грузовые шины", "Мотошины", "Диски" <strong>step-quantity</strong>',
					'type' => 'text'
				] );
				?>
				<?php do_action( 'yfym_append_options_group_5', $post ); ?>
			</div>
			<?php do_action( 'yfym_append_options_panel', $post ); ?>
		</div>
		<?php
	}

	// совместимость с палгином RSS for Yandex Zen
	public function yfym_for_сompatibility_with_yandex_zen_plug_func( $dwl_link, $order_id, $order_email ) {
		if ( yfymp_license_status() == 'ok' ) {
			if ( empty( $order_id ) || empty( $order_email ) ) {
				yfym_optionUPD( 'yzen_yandex_zen_rss', 'enabled' );
			} else {
				yfym_optionUPD( 'yzen_yandex_zen_rss', 'disabled' );
			}
		}
		return $dwl_link;
	}

	public static function yfym_art_woo_custom_fields_save( $post_id ) {
		// Сохранение текстового поля
		//if (isset($_POST['_yfym_condition'])) {update_post_meta($post_id, '_yfym_condition', esc_attr($_POST['_yfym_condition']));}
	}

	public static function yfym_woocommerce_product_options_general_product_data() {
		global $product, $post;
		echo '<div class="options_group">'; // Группировка полей 
		woocommerce_wp_text_input( [ 
			'id' => '_yfym_premium_price',
			'label' => 'premium_price',
			'placeholder' => '0',
			'description' => __( 'Price for Ozon Premium customers. Used only in the OZONE feed', 'yml-for-yandex-market' ),
			'type' => 'number',
			'custom_attributes' => [ 
				'step' => '0.01',
				'min' => '0'
			]
		] );
		woocommerce_wp_text_input( [ 
			'id' => '_yfym_price_rrp',
			'label' => 'price_rrp',
			'placeholder' => '0',
			'description' => __( 'Recommended retail price, type of price for suppliers', 'yml-for-yandex-market' ),
			'type' => 'number',
			'custom_attributes' => [ 
				'step' => '0.01',
				'min' => '0'
			]
		] );
		woocommerce_wp_text_input( [ 
			'id' => '_yfym_min_price',
			'label' => 'min_price',
			'placeholder' => '0',
			'description' => __( 'Minimum price', 'yml-for-yandex-market' ),
			'type' => 'number',
			'custom_attributes' => [ 
				'step' => '0.01',
				'min' => '0'
			]
		] );
		echo '</div>';
	}

	/* функции крона */
	public function yfym_do_this_seventy_sec( $feed_id ) {
		// условие исправляет возможные ошибки и повторное создание удаленного фида
		if ( $feed_id == '' || $feed_id === 1 ) {
			yfym_optionUPD( 'yfym_status_sborki', '-1', $feed_id );
			wp_clear_scheduled_hook( 'yfym_cron_sborki', array( $feed_id ) );
			wp_clear_scheduled_hook( 'yfym_cron_period', array( $feed_id ) );

			// $yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			// unset($yfym_settings_arr[$feed_id]);
			// yfym_optionUPD('yfym_settings_arr', $yfym_settings_arr);
			return;
		}

		new YFYM_Error_Log( 'Cтартовала крон-задача do_this_seventy_sec' );
		$generation = new YFYM_Generation_XML( $feed_id ); // делаем что-либо каждые 70 сек
		$generation->run();
	}
	public function yfym_do_this_event( $feed_id = '' ) {
		// условие исправляет возможные ошибки и повторное создание удаленного фида
		if ( $feed_id == '' || $feed_id === 1 ) {
			yfym_optionUPD( 'yfym_status_sborki', '-1', $feed_id );
			wp_clear_scheduled_hook( 'yfym_cron_sborki', array( $feed_id ) );
			wp_clear_scheduled_hook( 'yfym_cron_period', array( $feed_id ) );

			// $yfym_settings_arr = yfym_optionGET('yfym_settings_arr');
			// unset($yfym_settings_arr[$feed_id]);
			// yfym_optionUPD('yfym_settings_arr', $yfym_settings_arr);
			return;
		}

		new YFYM_Error_Log( 'FEED № ' . $feed_id . '; Крон yfym_do_this_event включен. Делаем что-то каждый час; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
		$step_export = (int) yfym_optionGET( 'yfym_step_export', $feed_id, 'set_arr' );
		if ( $step_export === 0 ) {
			$step_export = 500;
		}
		yfym_optionUPD( 'yfym_status_sborki', 1, $feed_id );

		wp_clear_scheduled_hook( 'yfym_cron_sborki', array( $feed_id ) );

		// Возвращает nul/false. null когда планирование завершено. false в случае неудачи.
		$res = wp_schedule_event( time(), 'seventy_sec', 'yfym_cron_sborki', array( $feed_id ) );
		if ( $res === false ) {
			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; ERROR: Не удалось запланировань CRON seventy_sec; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
		} else {
			new YFYM_Error_Log( 'FEED № ' . $feed_id . '; CRON seventy_sec успешно запланирован; Файл: yml-for-yandex-market.php; Строка: ' . __LINE__ );
		}
	}
	/* end функции крона */

	// Вывод различных notices
	public function print_admin_notices_func() {
		if ( is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			// global $pagenow; https://wpincode.com/kak-dobavit-sobstvennye-uvedomleniya-v-adminke-wordpress/
			if ( isset( $_GET['page'] ) ) {
				if ( $_GET['page'] === 'yfymdebug' || $_GET['page'] === 'yfymexport' ) {
					print '<div class="notice notice-warning"><p><span class="yfym_bold">W3 Total Cache</span> ' . __( 'plugin is active', 'yml-for-yandex-market' ) . '. ' . __( 'If no YML feed is being generated, please', 'yml-for-yandex-market' ) . ' <a href="https://icopydoc.ru/w3tc-page-cache-meshaet-sozdaniyu-fida-reshenie/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=notice&utm_term=w3-total-cache" target="_blank">' . __( 'read this guide', 'yml-for-yandex-market' ) . '</a>.</p></div>';
				}
			}
		}

		$yfym_disable_notices = yfym_optionGET( 'yfym_disable_notices' );
		if ( $yfym_disable_notices !== 'on' ) {
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			$yfym_settings_arr_keys_arr = array_keys( $yfym_settings_arr );
			for ( $i = 0; $i < count( $yfym_settings_arr_keys_arr ); $i++ ) {
				$feed_id = $yfym_settings_arr_keys_arr[ $i ];
				$status_sborki = yfym_optionGET( 'yfym_status_sborki', $feed_id );
				if ( $status_sborki == false ) {
					continue;
				} else {
					$status_sborki = (int) $status_sborki;
				}
				if ( $status_sborki !== -1 ) {
					$count_posts = wp_count_posts( 'product' );
					$vsegotovarov = $count_posts->publish;
					$step_export = (int) yfym_optionGET( 'yfym_step_export', $feed_id, 'set_arr' );
					if ( $step_export === 0 ) {
						$step_export = 500;
					}
					//					$vobrabotke = $status_sborki-$step_export;

					$vobrabotke = ( ( $status_sborki - 1 ) * $step_export ) - $step_export;

					if ( $vsegotovarov > $vobrabotke ) {
						if ( $status_sborki == 1 ) {
							$vyvod = '<br />FEED № ' . $feed_id . ' ' . __( 'Category list import', 'yml-for-yandex-market' ) . '.<br />' . __( 'If the progress indicators have not changed within 20 minutes, try reducing the "Step of export" in the plugin settings', 'yml-for-yandex-market' ) . '. ' . __( 'Also make sure that there are no problems with the CRON on your site', 'yml-for-yandex-market' ) . ' (<a href="https://icopydoc.ru/minimalnye-trebovaniya-dlya-raboty-yml-for-yandex-market/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=notice&utm_term=check_problems_cron" target="_blank">' . __( 'read this guide', 'yml-for-yandex-market' ) . '</a>)';
						}
						if ( $status_sborki == 2 ) {
							$vyvod = '<br />FEED № ' . $feed_id . ' ' . __( 'Counting the number of products', 'yml-for-yandex-market' );
						}
						if ( $status_sborki > 2 ) {
							$vyvod = '<br />FEED № ' . $feed_id . ' ' . __( 'Progress', 'yml-for-yandex-market' ) . ': ' . $vobrabotke . ' ' . __( 'from', 'yml-for-yandex-market' ) . ' ' . $vsegotovarov . ' ' . __( 'products', 'yml-for-yandex-market' ) . '.<br />' . __( 'If the progress indicators have not changed within 20 minutes, try reducing the "Step of export" in the plugin settings', 'yml-for-yandex-market' ) . '. ' . __( 'Also make sure that there are no problems with the CRON on your site', 'yml-for-yandex-market' ) . ' (<a href="https://icopydoc.ru/minimalnye-trebovaniya-dlya-raboty-yml-for-yandex-market/?utm_source=yml-for-yandex-market&utm_medium=organic&utm_campaign=in-plugin-yml-for-yandex-market&utm_content=notice&utm_term=check_problems_cron" target="_blank">' . __( 'read this guide', 'yml-for-yandex-market' ) . '</a>)';
						}
					} else {
						$vyvod = '<br />FEED № ' . $feed_id . ' ' . __( 'Prior to the completion of less than 70 seconds', 'yml-for-yandex-market' );
					}
					print '<div class="updated notice notice-success is-dismissible"><p><span class="yfym_bold">Y4YM:</span> ' . __( 'We are working on automatic file creation. XML will be developed soon', 'yml-for-yandex-market' ) . $vyvod . '.</p></div>';
				}
			}
		}
	}

	private function admin_notices_func( $message, $class ) {
		$yfym_disable_notices = yfym_optionGET( 'yfym_disable_notices' );
		if ( $yfym_disable_notices === 'on' ) {
			return;
		} else {
			printf( '<div class="notice %1$s"><p>%2$s</p></div>', $class, $message );
			return;
		}
	}

	private function save_post_meta( $post_meta_arr, $post_id ) {
		for ( $i = 0; $i < count( $post_meta_arr ); $i++ ) {
			$meta_name = $post_meta_arr[ $i ];
			if ( isset( $_POST[ $meta_name ] ) ) {
				update_post_meta( $post_id, $meta_name, sanitize_text_field( $_POST[ $meta_name ] ) );
			}
		}
	}
} /* end class YmlforYandexMarket */