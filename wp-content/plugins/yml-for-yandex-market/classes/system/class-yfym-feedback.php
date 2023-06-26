<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Sends feedback about the plugin
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			1.6.0
 * 
 * @version			1.1.0 (23-12-2022)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param			
 *
 * @return			
 *
 * @depends			classes:	
 *					traits:		
 *					methods:	
 *					functions:	
 *					constants:	YFYM_PLUGIN_VERSION
 *					options:	
 *
 */

final class YFYM_Feedback {
	private $pref = 'yfym';

	public function __construct( $pref = null ) {
		if ( $pref ) {
			$this->pref = $pref;
		}

		$this->listen_submits_func();
	}

	public function get_form() { ?>
		<div class="postbox">
			<h2 class="hndle">
				<?php _e( 'Send data about the work of the plugin', 'yml-for-yandex-market' ); ?>
			</h2>
			<div class="inside">
				<p>
					<?php _e( 'Sending statistics you help make the plugin even better', 'yml-for-yandex-market' ); ?>!
					<?php _e( 'The following data will be transferred', 'yml-for-yandex-market' ); ?>:
				</p>
				<ul class="yfym_ul">
					<li>
						<?php _e( 'URL your feeds', 'yml-for-yandex-market' ); ?>
					</li>
					<li>
						<?php _e( 'Files generation status', 'yml-for-yandex-market' ); ?>
					</li>
					<li>
						<?php _e( 'PHP version information', 'yml-for-yandex-market' ); ?>
					</li>
					<li>
						<?php _e( 'Multisite mode status', 'yml-for-yandex-market' ); ?>
					</li>
					<li>
						<?php _e( 'Technical information and plugin logs', 'yml-for-yandex-market' ); ?> YML for Yandex Market
					</li>
				</ul>
				<p>
					<?php _e( 'Did my plugin help you upload your products to the', 'yml-for-yandex-market' ); ?> YML for Yandex
					Market?
				</p>
				<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post" enctype="multipart/form-data">
					<p>
						<input type="radio" name="<?php echo $this->get_radio_name(); ?>" value="yes"><?php _e( 'Yes', 'yml-for-yandex-market' ); ?><br />
						<input type="radio" name="<?php echo $this->get_radio_name(); ?>" value="no"><?php _e( 'No', 'yml-for-yandex-market' ); ?><br />
					</p>
					<p>
						<?php _e( "If you don't mind to be contacted in case of problems, please enter your email address", "yml-for-yandex-market" ); ?>.
					</p>
					<p><input type="email" name="<?php echo $this->get_input_name(); ?>"></p>
					<p>
						<?php _e( 'Your message', 'yml-for-yandex-market' ); ?>:
					</p>
					<p><textarea rows="6" cols="32" name="<?php echo $this->get_textarea_name(); ?>"
							placeholder="<?php _e( 'Enter your text to send me a message (You can write me in Russian or English). I check my email several times a day', 'yml-for-yandex-market' ); ?>"></textarea>
					</p>
					<?php wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_field() ); ?>
					<input class="button-primary" type="submit" name="<?php echo $this->get_submit_name(); ?>"
						value="<?php _e( 'Send data', 'yml-for-yandex-market' ); ?>" />
				</form>
			</div>
		</div>
		<?php
	}

	public function get_block_support_project() { ?>
		<div class="postbox">
			<h2 class="hndle">
				<?php _e( 'Please support the project', 'yml-for-yandex-market' ); ?>!
			</h2>
			<div class="inside">
				<p>
					<?php _e( 'Thank you for using the plugin', 'yml-for-yandex-market' ); ?> <strong>YML for Yandex
						Market</strong>
				</p>
				<p>
					<?php _e( 'Please help make the plugin better', 'yml-for-yandex-market' ); ?> <a
						href="https://docs.google.com/forms/d/e/1FAIpQLSdmEXYIQzW-_Hj2mwvVbzKT8UUKaScJWQjDwcgI7Y5D0Xmchw/viewform"
						target="_blank">
						<?php _e( 'answering 6 questions', 'yml-for-yandex-market' ); ?>!
					</a>
				</p>
				<p>
					<?php _e( 'If this plugin useful to you, please support the project one way', 'yml-for-yandex-market' ); ?>:
				</p>
				<ul class="yfym_ul">
					<li><a href="//wordpress.org/support/plugin/yml-for-yandex-market/reviews/" target="_blank">
							<?php _e( 'Leave a comment on the plugin page', 'yml-for-yandex-market' ); ?>
						</a>.</li>
					<li>
						<?php _e( 'Support the project financially', 'yml-for-yandex-market' ); ?>. <a
							href="//pay.cloudtips.ru/p/45d8ff3f" target="_blank">
							<?php _e( 'Donate now', 'yml-for-yandex-market' ); ?>
						</a>.
					</li>
					<li>
						<?php _e( 'Noticed a bug or have an idea how to improve the quality of the plugin', 'yml-for-yandex-market' ); ?>?
						<a href="mailto:support@icopydoc.ru">
							<?php _e( 'Let me know', 'yml-for-yandex-market' ); ?>
						</a>.
					</li>
				</ul>
				<p>
					<?php _e( 'The author of the plugin Maxim Glazunov', 'yml-for-yandex-market' ); ?>.
				</p>
				<p><span style="color: red;">
						<?php _e( 'Accept orders for individual revision of the plugin', 'yml-for-yandex-market' ); ?>
					</span>:<br /><a href="mailto:support@icopydoc.ru">
						<?php _e( 'Leave a request', 'yml-for-yandex-market' ); ?>
					</a>.</p>
			</div>
		</div>
		<?php
	}

	private function get_pref() {
		return $this->pref;
	}

	private function get_radio_name() {
		return $this->get_pref() . '_its_ok';
	}

	private function get_input_name() {
		return $this->get_pref() . '_email';
	}

	private function get_textarea_name() {
		return $this->get_pref() . '_message';
	}

	private function get_submit_name() {
		return $this->get_pref() . '_submit_send_stat';
	}

	private function get_nonce_action() {
		return $this->get_pref() . '_nonce_action_send_stat';
	}

	private function get_nonce_field() {
		return $this->get_pref() . '_nonce_field_send_stat';
	}

	private function listen_submits_func() {
		if ( isset( $_REQUEST[ $this->get_submit_name()] ) ) {
			$this->send_data();
			add_action( 'admin_notices', function () {
				$class = 'notice notice-success is-dismissible';
				$message = __( 'The data has been sent. Thank you', 'yml-for-yandex-market' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
			}, 9999 );
		}
	}

	private function send_data() {
		if ( ! empty( $_POST ) && check_admin_referer( $this->get_nonce_action(), $this->get_nonce_field() ) ) {
			if ( is_multisite() ) {
				$yfym_is_multisite = 'включен';
				$yfym_keeplogs = get_blog_option( get_current_blog_id(), 'yfym_keeplogs' );
			} else {
				$yfym_is_multisite = 'отключен';
				$yfym_keeplogs = get_option( 'yfym_keeplogs' );
			}
			$feed_id = '1'; // (string)
			$unixtime = (string) current_time( 'Y-m-d H:i' );
			$mail_content = '<h1>Заявка (#' . $unixtime . ')</h1>';
			$mail_content .= "Версия плагина: " . YFYM_PLUGIN_VERSION . "<br />";
			$mail_content .= "Версия WP: " . get_bloginfo( 'version' ) . "<br />";
			$woo_version = get_woo_version_number();
			$mail_content .= "Версия WC: " . $woo_version . "<br />";
			$mail_content .= "Версия PHP: " . phpversion() . "<br />";
			$mail_content .= "Режим мультисайта: " . $yfym_is_multisite . "<br />";
			$mail_content .= "Вести логи: " . $yfym_keeplogs . "<br />";
			$upload_dir = wp_get_upload_dir();
			$mail_content .= 'Расположение логов: <a href="' . $upload_dir['baseurl'] . '/yfym/plugins.log" target="_blank">' . $upload_dir['basedir'] . '/yfym/yfym.log</a><br />';
			$possible_problems_arr = YFYM_Debug_Page::get_possible_problems_list();
			if ( $possible_problems_arr[1] > 0 ) {
				$possible_problems_arr[3] = str_replace( '<br/>', PHP_EOL, $possible_problems_arr[3] );
				$mail_content .= "Самодиагностика: " . PHP_EOL . $possible_problems_arr[3];
			} else {
				$mail_content .= "Самодиагностика: Функции самодиагностики не выявили потенциальных проблем" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketAliexpress' ) ) {
				$mail_content .= "Aliexpress Export: не активна" . "<br />";
			} else {
				$order_id = yfym_optionGET( 'yfymae_order_id' );
				$order_email = yfym_optionGET( 'yfymae_order_email' );
				$mail_content .= "Aliexpress Export: активна (v " . yfymae_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketBookExport' ) ) {
				$mail_content .= "Book Export: не активна" . "<br />";
			} else {
				if ( ! defined( 'yfymbe_VER' ) ) {
					define( 'yfymbe_VER', 'н/д' );
				}
				$order_id = yfym_optionGET( 'yfymbe_order_id' );
				$order_email = yfym_optionGET( 'yfymbe_order_email' );
				$mail_content .= "Book Export: активна (v " . yfymbe_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketPro' ) ) {
				$mail_content .= "Pro: не активна" . "<br />";
			} else {
				if ( ! defined( 'yfymp_VER' ) ) {
					define( 'yfymp_VER', 'н/д' );
				}
				$order_id = yfym_optionGET( 'yfymp_order_id' );
				$order_email = yfym_optionGET( 'yfymp_order_email' );
				$mail_content .= "Pro: активна (v " . yfymp_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketProm' ) ) {
				$mail_content .= "Prom Export: не активна" . "<br />";
			} else {
				$order_id = yfym_optionGET( 'yfympr_order_id' );
				$order_email = yfym_optionGET( 'yfympr_order_email' );
				$mail_content .= "Prom Export: активна (v " . yfympr_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketPromosExport' ) ) {
				$mail_content .= "Promos Export: не активна" . "<br />";
			} else {
				if ( ! defined( 'yfympe_VER' ) ) {
					define( 'yfympe_VER', 'н/д' );
				}
				$order_id = yfym_optionGET( 'yfympe_order_id' );
				$order_email = yfym_optionGET( 'yfympe_order_email' );
				$mail_content .= "Promos Export: активна (v " . yfympe_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			if ( ! class_exists( 'YmlforYandexMarketRozetka' ) ) {
				$mail_content .= "Prom Export: не активна" . "<br />";
			} else {
				$order_id = yfym_optionGET( 'yfymre_order_id' );
				$order_email = yfym_optionGET( 'yfymre_order_email' );
				$mail_content .= "Rozetka Export: активна (v " . yfymre_VER . " (#" . $order_id . " / " . $order_email . "))" . "<br />";
			}
			$yandex_zen_rss = yfym_optionGET( 'yzen_yandex_zen_rss' );
			$mail_content .= "RSS for Yandex Zen: " . $yandex_zen_rss . "<br />";
			if ( isset( $_POST[ $this->get_radio_name()] ) ) {
				$mail_content .= PHP_EOL . "Помог ли плагин: " . sanitize_text_field( $_POST[ $this->get_radio_name()] );
			}
			if ( isset( $_POST[ $this->get_input_name()] ) ) {
				$mail_content .= '<br />Почта: <a href="mailto:' . sanitize_email( $_POST[ $this->get_input_name()] ) . '?subject=Ответ разработчика YML for Yandex Market (#' . $unixtime . ')" target="_blank" rel="nofollow noreferer" title="' . sanitize_email( $_POST['yfym_email'] ) . '">' . sanitize_email( $_POST['yfym_email'] ) . '</a>';
			}
			if ( isset( $_POST[ $this->get_textarea_name()] ) ) {
				$mail_content .= "<br />Сообщение: " . sanitize_text_field( $_POST[ $this->get_textarea_name()] );
			}
			/*$argsp = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1 );
					 $products = new WP_Query($argsp);
					 $vsegotovarov = $products->found_posts;*/
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			$yfym_settings_arr_keys_arr = array_keys( $yfym_settings_arr );
			for ( $i = 0; $i < count( $yfym_settings_arr_keys_arr ); $i++ ) {
				$feed_id = $yfym_settings_arr_keys_arr[ $i ];
				$status_sborki = (int) yfym_optionGET( 'yfym_status_sborki', $feed_id );
				$yfym_file_url = urldecode( yfym_optionGET( 'yfym_file_url', $feed_id, 'set_arr' ) );
				$yfym_file_file = urldecode( yfym_optionGET( 'yfym_file_file', $feed_id, 'set_arr' ) );
				$yfym_whot_export = yfym_optionGET( 'yfym_whot_export', $feed_id, 'set_arr' );
				$yfym_yml_rules = yfym_optionGET( 'yfym_yml_rules', $feed_id, 'set_arr' );
				$yfym_skip_missing_products = yfym_optionGET( 'yfym_skip_missing_products', $feed_id, 'set_arr' );
				$yfym_skip_backorders_products = yfym_optionGET( 'yfym_skip_backorders_products', $feed_id, 'set_arr' );
				$yfym_status_cron = yfym_optionGET( 'yfym_status_cron', $feed_id, 'set_arr' );
				$yfym_ufup = yfym_optionGET( 'yfym_ufup', $feed_id, 'set_arr' );
				$yfym_date_sborki = yfym_optionGET( 'yfym_date_sborki', $feed_id, 'set_arr' );
				$yfym_main_product = yfym_optionGET( 'yfym_main_product', $feed_id, 'set_arr' );
				$yfym_errors = yfym_optionGET( 'yfym_errors', $feed_id, 'set_arr' );

				$mail_content .= "<br />ФИД №: " . $i . "<br />";
				$mail_content .= "status_sborki: " . $status_sborki . "<br />";
				$mail_content .= "УРЛ: " . get_site_url() . "<br />";
				$mail_content .= "УРЛ YML-фида: " . $yfym_file_url . "<br />";
				$mail_content .= "Временный файл: " . $yfym_file_file . "<br />";
				$mail_content .= "Что экспортировать: " . $yfym_whot_export . "<br />";
				$mail_content .= "Придерживаться правил: " . $yfym_yml_rules . "<br />";
				$mail_content .= "Исключать товары которых нет в наличии (кроме предзаказа): " . $yfym_skip_missing_products . "<br />";
				$mail_content .= "Исключать из фида товары для предзаказа: " . $yfym_skip_backorders_products . "<br />";
				$mail_content .= "Автоматическое создание файла: " . $yfym_status_cron . "<br />";
				$mail_content .= "Обновить фид при обновлении карточки товара: " . $yfym_ufup . "<br />";
				$mail_content .= "Дата последней сборки XML: " . $yfym_date_sborki . "<br />";
				$mail_content .= "Что продаёт: " . $yfym_main_product . "<br />";
				$mail_content .= "Ошибки: " . $yfym_errors . "<br />";
			}

			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
			wp_mail( 'support@icopydoc.ru', 'Отчёт YML for WP', $mail_content );
			// Сбросим content-type, чтобы избежать возможного конфликта
			remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
		}
	}

	public static function set_html_content_type() {
		return 'text/html';
	}
}
?>