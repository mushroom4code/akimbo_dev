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
 * @version			1.0.1 (22-05-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html 
 *					https://wp-kama.ru/function/wp_list_table
 * 
 * @param		
 *
 * @return		
 *
 * @depends			classes:	WP_List_Table
 *					traits:	
 *					methods:	
 *					functions:	common_option_get 
 *								yfym_optionGET
 *					constants:	
 *					options:	
 *
 */

class YFYM_Settings_Feed_WP_List_Table extends WP_List_Table {
	private $feed_id;
	private $rules;

	function __construct( $feed_id ) {
		$this->feed_id = (string) $feed_id;
		$this->rules = yfym_optionGET( 'yfym_yml_rules', $this->feed_id, 'set_arr' );

		global $status, $page;
		parent::__construct( [ 
			'plural' => '', // По умолчанию: '' ($this->screen->base);
			// Название для множественного числа, используется во всяких 
			// заголовках, например в css классах, в заметках, например 'posts', тогда 'posts' будет добавлен в 
			// класс table.

			'singular' => '', // По умолчанию: ''; 
			// Название для единственного числа, например 'post'.

			'ajax' => false, // По умолчанию: false; 
			// Должна ли поддерживать таблица AJAX. Если true, класс будет вызывать метод 
			// _js_vars() в подвале, чтобы передать нужные переменные любому скрипту обрабатывающему AJAX события.

			'screen' => null, // По умолчанию: null; 
			// Строка содержащая название хука, нужного для определения текущей страницы. 
			// Если null, то будет установлен текущий экран. 
		] );
	}

	/**
	 * 	Метод get_columns() необходим для маркировки столбцов внизу и вверху таблицы. 
	 *	Ключи в массиве должны быть теми же, что и в массиве данных, 
	 *	иначе соответствующие столбцы не будут отображены.
	 */
	function get_columns() {
		$columns = [ 
			'yfym_attr_name' => __( 'Attribute', 'yml-for-yandex-market' ),
			'yfym_attr_desc' => __( 'Attribute description', 'yml-for-yandex-market' ),
			'yfym_attr_val' => __( 'Value', 'yml-for-yandex-market' ),
			'yfym_def_val' => __( 'Default value', 'yml-for-yandex-market' ),
		];
		return $columns;
	}

	private function attr_name_mask( $desc, $tag, $rules_arr = [] ) {
		if ( in_array( $this->rules, $rules_arr ) ) {
			$color = 'black';
		} else {
			$color = 'red';
		}
		if ( ! empty( $tag ) ) {
			$tag = '[' . $tag . ']';
		}
		return sprintf( '<span class="yfym_bold" style="color: %3$s;">%1$s</span><br/>%2$s',
			$desc,
			$tag,
			$color
		);
	}

	/**
	 *	Метод вытаскивает из БД данные, которые будут лежать в таблице
	 *	$this->table_data();
	 */
	private function table_data() {
		$result_arr = [];

		$feed_id = $this->get_feed_id();

		$attr_arr = [ 
			[ 
				'opt_name' => 'yfym_amount',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Quantity of products', 'yml-for-yandex-market' ) . ' (СДЭК)',
					'desc' => __(
						'To make it work you must enable "Manage stock" and indicate "Stock quantity"',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'amount'
				]
			],
			[ 
				'opt_name' => 'yfym_shop_sku',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => 'Shop sku',
					'desc' => 'Shop sku',
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'products_id', 'text' => __( 'Add from products ID', 'yml-for-yandex-market' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'shop-sku'
				]
			],
			[ 
				'opt_name' => 'yfym_count',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Quantity of products', 'yml-for-yandex-market' ),
					'desc' => __(
						'To make it work you must enable "Manage stock" and indicate "Stock quantity"',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'count'
				]
			],
			[ 
				'opt_name' => 'yfym_auto_disabled',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Automatically remove products from sale', 'yml-for-yandex-market' ),
					'desc' => __( 'Automatically remove products from sale', 'yml-for-yandex-market' ),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'yes', 'text' => __( 'Yes', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'disabled'
				]
			],
			[ 
				'opt_name' => 'yfym_market_sku_status',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Add market-sku to feed', 'yml-for-yandex-market' ),
					'desc' => __(
						'Optional when creating a catalog. A must for price recommendations',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'market-sku'
				]
			],
			[ 
				'opt_name' => 'yfym_manufacturer',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Manufacturer company', 'yml-for-yandex-market' ),
					'desc' => __( 'Manufacturer company', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'manufacturer'
				]
			],
			[ 
				'opt_name' => 'yfym_vendor',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Vendor', 'yml-for-yandex-market' ),
					'desc' => __( 'Vendor', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => true,
					'brands' => true,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'post_meta', 'text' => __( 'Substitute from post meta', 'yml-for-yandex-market' ) ],
						[ 
							'value' => 'default_value',
							'text' => sprintf( '%s "%s"',
								__( 'Default value from field', 'yml-for-yandex-market' ),
								__( 'Default value', 'yml-for-yandex-market' )
							)
						]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'vendor'
				]
			],
			[ 
				'opt_name' => 'yfym_vendor_post_meta',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '',
					'desc' => '',
					'placeholder' => sprintf( '%s / %s',
						__( 'Value', 'yml-for-yandex-market' ),
						__( 'Name post_meta', 'yml-for-yandex-market' )
					)
				]
			],
			[ 
				'opt_name' => 'yfym_country_of_origin',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Country of origin', 'yml-for-yandex-market' ),
					'desc' => __(
						'This element indicates the country where the product was manufactured',
						'yml-for-yandex-market'
					),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'country_of_origin'
				]
			],
			[ 
				'opt_name' => 'yfym_source_id',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Source ID of the product', 'yml-for-yandex-market' ),
					'desc' => '',
					'woo_attr' => true,
					'default_value' => true,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ],
						[ 'value' => 'post_meta', 'text' => __( 'Substitute from post meta', 'yml-for-yandex-market' ) ],
						[ 
							'value' => 'germanized',
							'text' => __( 'Substitute from', 'yml-for-yandex-market' ) . 'WooCommerce Germanized'
						]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => ''
				]
			],
			[ 
				'opt_name' => 'yfym_source_id_post_meta',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '',
					'desc' => '',
					'placeholder' => __( 'Name post_meta', 'yml-for-yandex-market' )
				]
			],
			[ 
				'opt_name' => 'yfym_on_demand',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Mark products under the order', 'yml-for-yandex-market' ),
					'desc' => __( 'Product under the order', 'yml-for-yandex-market' ),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'type="on.demand"'
				]
			],
			[ 
				'opt_name' => 'yfym_pickup',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Pickup', 'yml-for-yandex-market' ),
					'desc' => __( 'Option to get order from pickup point', 'yml-for-yandex-market' ),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'true', 'text' => __( 'True', 'yml-for-yandex-market' ) ],
						[ 'value' => 'false', 'text' => __( 'False', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'pickup'
				]
			],
			[ 
				'opt_name' => 'yfym_price_from',
				'def_val' => 'no',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Price from', 'yml-for-yandex-market' ),
					'desc' => __( 'Apply the setting Price from', 'yml-for-yandex-market' ) . ' <strong>from="true"</strong> ' . __( 'attribute of', 'yml-for-yandex-market' ) . ' <strong>price</strong><br /><strong>' . __( 'Example', 'yml-for-yandex-market' ) . '>:</strong><br /><code>&lt;price from=&quot;true&quot;&gt;2000&lt;/price&gt;</code>',
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'true', 'text' => __( 'Yes', 'yml-for-yandex-market' ) ],
						[ 'value' => 'false', 'text' => __( 'No', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => '...from="true"...'
				]
			],
			[ 
				'opt_name' => 'yfym_oldprice',
				'def_val' => 'no',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Old price', 'yml-for-yandex-market' ),
					'desc' => __(
						'In oldprice indicates the old price of the goods, which must necessarily be higher than the new price (price)',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'yes', 'text' => __( 'Yes', 'yml-for-yandex-market' ) ],
						[ 'value' => 'no', 'text' => __( 'No', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'oldprice'
				]
			],
			[ 
				'opt_name' => 'yfym_delivery',
				'def_val' => 'no',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Delivery', 'yml-for-yandex-market' ),
					'desc' => __(
						'The delivery item must be set to false if the item is prohibited to sell remotely (jewelry, medicines)',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'true', 'text' => __( 'True', 'yml-for-yandex-market' ) ],
						[ 'value' => 'false', 'text' => __( 'False', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'delivery'
				]
			],
			[ 
				'opt_name' => 'yfym_vat',
				'def_val' => 'no',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'VAT rate', 'yml-for-yandex-market' ),
					'desc' => __(
						'This element is used when creating an YML feed for Yandex.Delivery',
						'yml-for-yandex-market'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enable. No default value', 'yml-for-yandex-market' ) ],
						[ 'value' => 'NO_VAT', 'text' => __( 'No VAT', 'yml-for-yandex-market' ) . ' (NO_VAT)' ],
						[ 'value' => 'VAT_0', 'text' => '0% (VAT_0)' ],
						[ 'value' => 'VAT_10', 'text' => '10% (VAT_10)' ],
						[ 'value' => 'VAT_10_110', 'text' => 'VAT_10_110' ],
						[ 'value' => 'VAT_18', 'text' => '18% (VAT_18)' ],
						[ 'value' => 'VAT_18_118', 'text' => '18/118 (VAT_18_118)' ],
						[ 'value' => 'VAT_20', 'text' => '20% (VAT_20)' ],
						[ 'value' => 'VAT_20_120', 'text' => '20/120 (VAT_20_120)' ],
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'vat'
				]
			],
			[ 
				'opt_name' => 'yfym_barcode',
				'def_val' => 'no',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Barcode', 'yml-for-yandex-market' ),
					'desc' => '',
					'woo_attr' => true,
					'default_value' => true,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ],
						[ 'value' => 'post_meta', 'text' => __( 'Substitute from post meta', 'yml-for-yandex-market' ) ],
						[ 
							'value' => 'ean-for-woocommerce',
							'text' => __( 'Substitute from', 'yml-for-yandex-market' ) . ' EAN for WooCommerce'
						],
						[ 
							'value' => 'germanized',
							'text' => __( 'Substitute from', 'yml-for-yandex-market' ) . ' WooCommerce Germanized'
						]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'barcode'
				]
			],
			[ 
				'opt_name' => 'yfym_barcode_post_meta',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '',
					'desc' => '',
					'placeholder' => __( 'Name post_meta', 'yml-for-yandex-market' )
				]
			],
			[ 
				'opt_name' => 'yfym_vendorcode',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Vendor Code', 'yml-for-yandex-market' ),
					'desc' => __( 'Vendor Code', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'vendorcode'
				]
			],
			[ 
				'opt_name' => 'yfym_cargo_types',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '«Честный ЗНАК»',
					'desc' => '',
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'enabled', 'text' => __( 'Enabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'cargo-types'
				]
			],
			[ 
				'opt_name' => 'yfym_expiry',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Shelf life / service life', 'yml-for-yandex-market' ),
					'desc' => __( 'Shelf life / service life. expiry date / service life', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'expiry'
				]
			],
			[ 
				'opt_name' => 'yfym_period_of_validity_days',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Shelf life', 'yml-for-yandex-market' ),
					'desc' => __( 'Shelf life', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'period-of-validity-days'
				]
			],
			[ 
				'opt_name' => 'yfym_downloadable',
				'def_val' => 'off',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Mark downloadable products', 'yml-for-yandex-market' ),
					'desc' => '',
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'off', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'on', 'text' => __( 'On', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'downloadable'
				]
			],
			[ 
				'opt_name' => 'yfym_age',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Age', 'yml-for-yandex-market' ),
					'desc' => __( 'Age', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'age'
				]
			],
			[ 
				'opt_name' => 'yfym_model',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Model', 'yml-for-yandex-market' ),
					'desc' => __( 'Model', 'yml-for-yandex-market' ),
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'model'
				]
			],
			[ 
				'opt_name' => 'yfym_manufacturer_warranty',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Manufacturer warrant', 'yml-for-yandex-market' ),
					'desc' => __( "This element is used for products that have an official manufacturer's warranty", 'yfym' ) . '.<ul><li>false — ' . __( 'Product does not have an official warranty', 'yml-for-yandex-market' ) . '</li><li>true — ' . __( 'Product has an official warranty', 'yml-for-yandex-market' ) . '</li></ul>',
					'woo_attr' => true,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 'value' => 'alltrue', 'text' => __( 'Add to all', 'yml-for-yandex-market' ) . ' true' ],
						[ 'value' => 'allfalse', 'text' => __( 'Add to all', 'yml-for-yandex-market' ) . ' false' ],
						[ 'value' => 'sku', 'text' => __( 'Substitute from SKU', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'manufacturer_warranty'
				]
			],
			[ 
				'opt_name' => 'yfym_sales_notes_cat',
				'def_val' => 'disabled',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Sales notes', 'yml-for-yandex-market' ),
					'desc' => __(
						'The text may be up to 50 characters in length. Also in the item is forbidden to specify the terms of delivery and price reduction (discount on merchandise)',
						'yml-for-yandex-market'
					),
					'woo_attr' => true,
					'default_value' => true,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 
							'value' => 'default_value',
							'text' => sprintf( '%s "%s"',
								__( 'Default value from field', 'yml-for-yandex-market' ),
								__( 'Default value', 'yml-for-yandex-market' )
							)
						]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'sales_notes'
				]
			],
			[ 
				'opt_name' => 'yfym_sales_notes',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '',
					'desc' => '',
					'placeholder' => __( 'Default value', 'yml-for-yandex-market' )
				]
			],
			[ 
				'opt_name' => 'yfym_store',
				'def_val' => 'true',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Store', 'yml-for-yandex-market' ),
					'desc' => sprintf( '<ul><li>%s — %s</li><li>%s — %s</li></ul>',
						__( 'true', 'yml-for-yandex-market' ),
						__( 'The product can be purchased in retail stores', 'yml-for-yandex-market' ),
						__( 'false', 'yml-for-yandex-market' ),
						__( 'the product cannot be purchased in retail stores', 'yml-for-yandex-market' )
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'true', 'text' => __( 'True', 'yml-for-yandex-market' ) ],
						[ 'value' => 'false', 'text' => __( 'False', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'store'
				]
			],
			[ 
				'opt_name' => 'yfym_condition',
				'def_val' => 'true',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => __( 'Condition', 'yml-for-yandex-market' ),
					'desc' => sprintf( '%s %s:<br/>%s',
						__( 'Default value', 'yml-for-yandex-market' ),
						__( 'for', 'yml-for-yandex-market' ),
						'(...condition type="X"...)'
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'disabled', 'text' => __( 'Disabled', 'yml-for-yandex-market' ) ],
						[ 
							'value' => 'showcasesample',
							'text' => __( 'Showcase sample', 'yml-for-yandex-market' ) . ' (showcasesample)'
						],
						[ 
							'value' => 'reduction',
							'text' => __( 'Reduction', 'yml-for-yandex-market' ) . ' (reduction)'
						],
						[ 
							'value' => 'fashionpreowned',
							'text' => __( 'Fashionpreowned', 'yml-for-yandex-market' ) . ' (fashionpreowned)'
						],
						[ 
							'value' => 'preowned',
							'text' => __( 'Fashionpreowned', 'yml-for-yandex-market' ) . ' (preowned)'
						],
						[ 
							'value' => 'likenew',
							'text' => __( 'Like New', 'yml-for-yandex-market' ) . ' (likenew)'
						]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => 'condition'
				]
			],
			[ 
				'opt_name' => 'yfym_reason',
				'def_val' => '',
				'mark' => 'public',
				'required' => true,
				'type' => 'text',
				'tab' => 'wp_list_table',
				'data' => [ 
					'default_value' => false,
					'label' => '',
					'desc' => sprintf( '%s %s:<br/>%s [reason]',
						__( 'Default value', 'yml-for-yandex-market' ),
						__( 'for', 'yml-for-yandex-market' ),
						__( 'Reason', 'yml-for-yandex-market' )
					),
					'placeholder' => __( 'Default value', 'yml-for-yandex-market' ),
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => ''
				]
			],
			[ 
				'opt_name' => 'yfym_quality',
				'def_val' => 'true',
				'mark' => 'public',
				'required' => false,
				'type' => 'select',
				'tab' => 'wp_list_table',
				'data' => [ 
					'label' => '',
					'desc' => sprintf( '%s %s:<br/>%s [quality]',
						__( 'Default value', 'yml-for-yandex-market' ),
						__( 'for', 'yml-for-yandex-market' ),
						__( 'Quality', 'yml-for-yandex-market' )
					),
					'woo_attr' => false,
					'default_value' => false,
					'key_value_arr' => [ 
						[ 'value' => 'perfect', 'text' => __( 'Perfect', 'yml-for-yandex-market' ) ],
						[ 'value' => 'excellent', 'text' => __( 'Excellent', 'yml-for-yandex-market' ) ],
						[ 'value' => 'good', 'text' => __( 'Good', 'yml-for-yandex-market' ) ]
					],
					'rules' => [ 
						'yandex_market', 'dbs', 'single_catalog', 'sales_terms', 'sbermegamarket', 'beru',
						'products_and_offers', 'yandex_webmaster', 'all_elements', 'ozon', 'vk','group_price'
					],
					'tag_name' => ''
				]
			]
		];

		for ( $i = 0; $i < count( $attr_arr ); $i++ ) {
			if ( $attr_arr[ $i ]['tab'] === 'wp_list_table' ) {
				$r_arr = [];
				$r_arr['yfym_attr_name'] = $this->attr_name_mask(
					$attr_arr[ $i ]['data']['label'],
					$attr_arr[ $i ]['data']['tag_name'],
					$attr_arr[ $i ]['data']['rules']
				);
				$r_arr['yfym_attr_desc'] = $attr_arr[ $i ]['data']['desc'];

				if ( $attr_arr[ $i ]['type'] === 'select' ) {
					$attr_val = $this->get_view_html_field_select( $attr_arr[ $i ] );
				} else if ( $attr_arr[ $i ]['type'] === 'text' ) {
					$attr_val = $this->get_view_html_field_input( $attr_arr[ $i ] );
				}
				$r_arr['yfym_attr_val'] = $attr_val;

				if ( true === $attr_arr[ $i ]['data']['default_value'] ) {
					$i++;
					if ( $attr_arr[ $i ]['type'] === 'text' ) {
						$r_arr['yfym_def_val'] = $this->get_view_html_field_input( $attr_arr[ $i ] );
					}
				} else {
					$r_arr['yfym_def_val'] = __( 'There are no default settings', 'yml-for-yandex-market' );
				}

				$result_arr[] = $r_arr;
				unset( $r_arr );
			}
		}

		return $result_arr;
	}

	private function get_view_html_field_input( $data_arr ) {
		return sprintf( '<input 
					type="text" 
					name="%1$s" 
					id="%1$s" 
					value="%2$s"
					placeholder="%3$s" /><br />',
			esc_attr( $data_arr['opt_name'] ),
			esc_attr( common_option_get( $data_arr['opt_name'], false, $this->get_feed_id(), 'yfym' ) ),
			esc_html( $data_arr['data']['placeholder'] )
		);
	}

	private function get_view_html_field_select( $data_arr ) {
		if ( isset( $data_arr['data']['key_value_arr'] ) ) {
			$key_value_arr = $data_arr['data']['key_value_arr'];
		} else {
			$key_value_arr = [];
		}

		return sprintf( '<select name="%1$s" id="%1$s" />%2$s</select>',
			esc_attr( $data_arr['opt_name'] ),
			$this->print_view_html_option_for_select(
				common_option_get(
					$data_arr['opt_name'],
					false,
					$this->get_feed_id(),
					'yfym'
				),
				false,
				[ 
					'woo_attr' => $data_arr['data']['woo_attr'],
					'key_value_arr' => $key_value_arr
				]
			)
		);
	}

	private function print_view_html_option_for_select( $opt_value, $opt_name = false, $params_arr = [], $res = '' ) {
		if ( ! empty( $params_arr['key_value_arr'] ) ) {
			for ( $i = 0; $i < count( $params_arr['key_value_arr'] ); $i++ ) {
				$res .= sprintf( '<option value="%1$s" %2$s>%3$s</option>' . PHP_EOL,
					esc_attr( $params_arr['key_value_arr'][ $i ]['value'] ),
					esc_attr( selected( $opt_value, $params_arr['key_value_arr'][ $i ]['value'], false ) ),
					esc_attr( $params_arr['key_value_arr'][ $i ]['text'] )
				);
			}
		}

		if ( isset( $otions_arr[ $i ]['brands'] ) ) {
			if ( is_plugin_active( 'perfect-woocommerce-brands/perfect-woocommerce-brands.php' )
				|| is_plugin_active( 'perfect-woocommerce-brands/main.php' )
				|| class_exists( 'Perfect_Woocommerce_Brands' ) ) {
				$res .= sprintf( '<option value="sfpwb" %s>%s Perfect Woocommerce Brands</option>',
					selected( $opt_value, 'sfpwb', false ),
					__( 'Substitute from', 'yml-for-yandex-market' )
				);
			}
			if ( is_plugin_active( 'premmerce-woocommerce-brands/premmerce-brands.php' ) ) {
				$res .= sprintf( '<option value="premmercebrandsplugin" %s>%s %s</option>',
					selected( $opt_value, 'premmercebrandsplugin', false ),
					__( 'Substitute from', 'yml-for-yandex-market' ),
					'Premmerce Brands for WooCommerce'
				);
			}
			if ( is_plugin_active( 'woocommerce-brands/woocommerce-brands.php' ) ) {
				$res .= sprintf( '<option value="woocommerce_brands" %s>%s %s</option>',
					selected( $opt_value, 'woocommerce_brands', false ),
					__( 'Substitute from', 'yml-for-yandex-market' ),
					'WooCommerce Brands'
				);
			}
			if ( class_exists( 'woo_brands' ) ) {
				$res .= sprintf( '<option value="woo_brands" %s>%s %s</option>',
					selected( $opt_value, 'woo_brands', false ),
					__( 'Substitute from', 'yml-for-yandex-market' ),
					'Woocomerce Brands Pro'
				);
			}
			if ( is_plugin_active( 'yith-woocommerce-brands-add-on/init.php' )
				|| is_plugin_active( 'perfect-woocommerce-brands/main.php' )
				|| class_exists( 'Perfect_Woocommerce_Brands' ) ) {
				$res .= sprintf( '<option value="yith_woocommerce_brands_add_on" %s>%s %s</option>',
					selected( $opt_value, 'yith_woocommerce_brands_add_on', false ),
					__( 'Substitute from', 'yml-for-yandex-market' ),
					'YITH WooCommerce Brands Add-On'
				);
			}
		}

		if ( ! empty( $params_arr['woo_attr'] ) ) {
			$woo_attributes_arr = get_woo_attributes();
			for ( $i = 0; $i < count( $woo_attributes_arr ); $i++ ) {
				$res .= sprintf( '<option value="%1$s" %2$s>%3$s</option>' . PHP_EOL,
					esc_attr( $woo_attributes_arr[ $i ]['id'] ),
					esc_attr( selected( $opt_value, $woo_attributes_arr[ $i ]['id'], false ) ),
					esc_attr( $woo_attributes_arr[ $i ]['name'] )
				);
			}
			unset( $woo_attributes_arr );
		}
		return $res;
	}

	/**
	 *	prepare_items определяет два массива, управляющие работой таблицы:
	 *	$hidden - определяет скрытые столбцы 
	 *			(https://2web-master.ru/wp_list_table-%E2%80%93-poshagovoe-rukovodstvo.html#screen-options)
	 *	$sortable - определяет, может ли таблица быть отсортирована по этому столбцу
	 */
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = [];
		$sortable = $this->get_sortable_columns(); // вызов сортировки
		$this->_column_headers = [ $columns, $hidden, $sortable ];
		// блок пагинации пропущен
		$this->items = $this->table_data();
	}

	/** 
	 * 	Данные таблицы.
	 *	Наконец, метод назначает данные из примера на переменную представления данных класса — items.
	 *	Прежде чем отобразить каждый столбец, WordPress ищет методы типа column_{key_name}, например,
	 *	function column_yfym_url_xml_file. 
	 *	Такой метод должен быть указан для каждого столбца. Но чтобы не создавать эти методы для всех столбцов
	 *	в отдельности, можно использовать column_default. Эта функция обработает все столбцы, для которых не определён
	 *	специальный метод.
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'yfym_attr_name':
			case 'yfym_attr_desc':
			case 'yfym_attr_val':
			case 'yfym_def_val':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Мы отображаем целый массив во избежание проблем
		}
	}

	private function get_feed_id() {
		return $this->feed_id;
	}
}