<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Plugin Form Activate
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			3.10.0
 * 
 * @version			1.0.2 (29-05-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				[ 202, 402, 412, 418, 520 ]
 * 
 * @param	array	$args
 *
 * @return			
 *
 * @depends			classes:	
 *					traits:	
 *					methods:	
 *					functions:	
 *					constants:	
 *					options:	
 *
 */

final class Y4YM_Plugin_Form_Activate {
	const INSTRUCTION_URL = 'https://icopydoc.ru/kak-aktivirovat-pro-versiyu-instruktsiya/';
	private $list_plugin_names = [ 
		'yfymp' => [ 'name' => 'PRO', 'code' => 'renewlicense20yp' ],
		'yfymbe' => [ 'name' => 'Book Export', 'code' => 'renewlicense20book' ],
		'yfymae' => [ 'name' => 'Aliexpress Export', 'code' => 'renewlicense20ali' ],
		'yfympr' => [ 'name' => 'PROM Export', 'code' => 'renewlicense20prom' ],
		'yfympe' => [ 'name' => 'PROMos Export', 'code' => 'renewlicense20promos' ],
		'yfymre' => [ 'name' => 'Rozetka Export', 'code' => 'renewlicense20re' ],
		'y4yms' => [ 'name' => 'SETS', 'code' => 'renewlicense23sets' ]
	];
	private $pref = 'yfymp';
	private $slug;
	private $submit_name;
	private $opt_name_order_id;
	private $opt_name_order_email;

	public function __construct( $pref = 'yfymp', $slug = '' ) {
		$this->pref = $pref;
		$this->slug = $slug;
		$this->submit_name = $this->get_pref() . '_submit_license_pro';
		$this->opt_name_order_id = $this->get_pref() . '_order_id';
		$this->opt_name_order_email = $this->get_pref() . '_order_email';

		$this->save_form();
		$this->the_form();
	}

	public function the_form() {
		if ( is_multisite() ) {
			$order_id = get_blog_option( get_current_blog_id(), $this->get_opt_name_order_id() );
			$order_email = get_blog_option( get_current_blog_id(), $this->get_opt_name_order_email() );
		} else {
			$order_id = get_option( $this->get_opt_name_order_id() );
			$order_email = get_option( $this->get_opt_name_order_email() );
		}
		?>
		<style>
			input.pw {
				-webkit-text-security: disc;
			}
		</style>
		<div class="postbox">
			<h2 class="hndle">
				<?php
				printf( '%s %s',
					__( 'License data', 'yml-for-yandex-market' ),
					esc_html( $this->list_plugin_names[ $this->get_pref()]['name'] )
				); ?>
			</h2>
			<div class="inside">
				<form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post" enctype="multipart/form-data">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<?php _e( 'Order ID', 'yml-for-yandex-market' ); ?>
								</th>
								<td class="overalldesc">
									<input class="pw" type="text"
										name="<?php echo esc_attr( $this->get_opt_name_order_id() ); ?>"
										value="<?php echo esc_attr( $order_id ); ?>" /><br />
									<span class="description">
										<a target="_blank" href="<?php
										printf( '%1$s?utm_source=%2$s&utm_medium=organic&utm_campaign=%2$s%3$s',
											esc_attr( self::INSTRUCTION_URL ),
											esc_attr( $this->slug ),
											'&utm_content=settings&utm_term=how-to-activate-order-id'
										); ?>"><?php _e( 'Read more', 'yml-for-yandex-market' ); ?></a>
									</span>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php _e( 'Order Email', 'yml-for-yandex-market' ); ?>
								</th>
								<td class="overalldesc">
									<input type="text" name="<?php echo esc_attr( $this->get_opt_name_order_email() ); ?>"
										value="<?php echo esc_attr( $order_email ); ?>" /><br />
									<span class="description">
										<a target="_blank" href="<?php
										printf( '%1$s?utm_source=%2$s&utm_medium=organic&utm_campaign=%2$s%3$s',
											esc_attr( self::INSTRUCTION_URL ),
											esc_attr( $this->slug ),
											'&utm_content=settings&utm_term=how-to-activate-order-email'
										); ?>"><?php _e( 'Read more', 'yml-for-yandex-market' ); ?></a></span>
								</td>
							</tr>
						</tbody>
					</table>
					<input class="button-primary" type="submit" name="<?php echo esc_attr( $this->get_submit_name() ); ?>"
						value="<?php _e( 'Update License Data', 'yml-for-yandex-market' ); ?>" />
				</form>
			</div>
		</div>
		<?php
	}

	private function get_pref() {
		return $this->pref;
	}

	private function get_submit_name() {
		return $this->submit_name;
	}

	private function get_opt_name_order_id() {
		return $this->opt_name_order_id;
	}

	private function get_opt_name_order_email() {
		return $this->opt_name_order_email;
	}

	private function save_form() {
		if ( isset( $_REQUEST[ $this->get_submit_name()] ) ) {
			if ( is_multisite() ) {
				if ( isset( $_POST[ $this->get_opt_name_order_id()] ) ) {
					update_blog_option(
						get_current_blog_id(),
						$this->get_opt_name_order_id(),
						sanitize_text_field( $_POST[ $this->get_opt_name_order_id()] )
					);
				}
				if ( isset( $_POST[ $this->get_opt_name_order_email()] ) ) {
					update_blog_option(
						get_current_blog_id(),
						$this->get_opt_name_order_email(),
						sanitize_text_field( $_POST[ $this->get_opt_name_order_email()] )
					);
				}
			} else {
				if ( isset( $_POST[ $this->get_opt_name_order_id()] ) ) {
					update_option(
						$this->get_opt_name_order_id(),
						sanitize_text_field( $_POST[ $this->get_opt_name_order_id()] )
					);
				}
				if ( isset( $_POST[ $this->get_opt_name_order_email()] ) ) {
					update_option(
						$this->get_opt_name_order_email(),
						sanitize_text_field( $_POST[ $this->get_opt_name_order_email()] )
					);
				}
			}
			wp_clean_plugins_cache();
			wp_clean_update_cache();
			add_filter( 'pre_site_transient_update_plugins', '__return_null' );
			wp_update_plugins();
			remove_filter( 'pre_site_transient_update_plugins', '__return_null' );
			printf( '<div class="notice notice-success is-dismissible"><p>%1$s <a 
														href="javascript:location.reload(true)">%2$s</a></p></div>',
				__( 'License data has been updated', 'yml-for-yandex-market' ),
				__( 'Refresh this page', 'yml-for-yandex-market' )
			);
			wp_update_plugins();
		}
	}
}