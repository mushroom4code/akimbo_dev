<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Возвращает то, что может быть результатом add_blog_option, add_option
 * 
 * @since 3.0.0
 *
 * @param string $option_name (require)
 * @param string $value (require)
 * @param string $n (not require)
 * @param string $autoload (not require) (yes/no) (@since 3.3.15)
 * @param string $type (not require) (@since 3.5.5)
 * @param string $source_settings_name (not require) (@since 3.6.4)
 *
 * @return bool
 */
function yfym_optionADD( $option_name, $value = '', $n = '', $autoload = 'yes', $type = 'option', $source_settings_name = '' ) {
	if ( $option_name == '' ) {
		return false;
	}
	switch ( $type ) {
		case "set_arr":
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			$yfym_settings_arr[ $n ][ $option_name ] = $value;
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr );
			} else {
				return update_option( 'yfym_settings_arr', $yfym_settings_arr, $autoload );
			}
		case "custom_set_arr":
			if ( $source_settings_name === '' ) {
				return false;
			}
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( $source_settings_name );
			$yfym_settings_arr[ $n ][ $option_name ] = $value;
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), $source_settings_name, $yfym_settings_arr );
			} else {
				return update_option( $source_settings_name, $yfym_settings_arr, $autoload );
			}
		default:
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return add_blog_option( get_current_blog_id(), $option_name, $value );
			} else {
				return add_option( $option_name, $value, '', $autoload );
			}
	}
}
/**
 * @since 3.0.0
 *
 * @param string $option_name (require)
 * @param string $value (not require)
 * @param string $n (not require)
 * @param string $autoload (not require) (yes/no) (@since 3.3.15)
 * @param string $type (not require) (@since 3.5.5)
 * @param string $source_settings_name (not require) (@since 3.6.4)
 *
 * @return bool
 * Возвращает то, что может быть результатом update_blog_option, update_option
 */
function yfym_optionUPD( $option_name, $value = '', $n = '', $autoload = 'yes', $type = '', $source_settings_name = '' ) {
	if ( $option_name == '' ) {
		return false;
	}
	switch ( $type ) {
		case "set_arr":
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			$yfym_settings_arr[ $n ][ $option_name ] = $value;
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr );
			} else {
				return update_option( 'yfym_settings_arr', $yfym_settings_arr, $autoload );
			}
		case "custom_set_arr":
			if ( $source_settings_name === '' ) {
				return false;
			}
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( $source_settings_name );
			$yfym_settings_arr[ $n ][ $option_name ] = $value;
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), $source_settings_name, $yfym_settings_arr );
			} else {
				return update_option( $source_settings_name, $yfym_settings_arr, $autoload );
			}
		default:
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), $option_name, $value );
			} else {
				return update_option( $option_name, $value, $autoload );
			}
	}
}
/**
 * Возвращает то, что может быть результатом get_blog_option, get_option
 * 
 * @since 2.0.0
 *
 * @param string $option_name (require)
 * @param string $n (not require) (@since 3.0.0)
 * @param string $type (not require) (@since 3.5.5)
 * @param string $source_settings_name (not require) (@since 3.6.4)
 *
 * @return mixed|false
 */
function yfym_optionGET( $option_name, $n = '', $type = '', $source_settings_name = '' ) {
	if ( $option_name == 'yfym_status_sborki' && $n == '1' ) {
		if ( is_multisite() ) {
			return get_blog_option( get_current_blog_id(), 'yfym_status_sborki' );
		} else {
			return get_option( 'yfym_status_sborki' );
		}
	}

	if ( defined( 'yfymp_VER' ) ) {
		$pro_ver_number = yfymp_VER;
	} else {
		$pro_ver_number = '4.2.7';
	}
	if ( version_compare( $pro_ver_number, '4.3.0', '<' ) ) { // если версия PRO ниже 4.3.0
		if ( $option_name === 'yfymp_compare_value' ) {
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return get_blog_option( get_current_blog_id(), $option_name );
			} else {
				return get_option( $option_name );
			}
		}
		if ( $option_name === 'yfymp_compare' ) {
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return get_blog_option( get_current_blog_id(), $option_name );
			} else {
				return get_option( $option_name );
			}
		}
	}

	if ( $option_name == '' ) {
		return false;
	}
	switch ( $type ) {
		case "set_arr":
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			if ( isset( $yfym_settings_arr[ $n ][ $option_name ] ) ) {
				return $yfym_settings_arr[ $n ][ $option_name ];
			} else {
				return false;
			}
		case "custom_set_arr":
			if ( $source_settings_name === '' ) {
				return false;
			}
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( $source_settings_name );
			if ( isset( $yfym_settings_arr[ $n ][ $option_name ] ) ) {
				return $yfym_settings_arr[ $n ][ $option_name ];
			} else {
				return false;
			}
		case "for_update_option":
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return get_blog_option( get_current_blog_id(), $option_name );
			} else {
				return get_option( $option_name );
			}
		default:
			/* for old premium versions */
			if ( $option_name === 'yfym_desc' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			if ( $option_name === 'yfym_no_default_png_products' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			if ( $option_name === 'yfym_whot_export' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			if ( $option_name === 'yfym_file_extension' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			if ( $option_name === 'yfym_feed_assignment' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}

			if ( $option_name === 'yfym_file_ids_in_yml' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			if ( $option_name === 'yfym_wooc_currencies' ) {
				return yfym_optionGET( $option_name, $n, 'set_arr' );
			}
			/* for old premium versions */
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return get_blog_option( get_current_blog_id(), $option_name );
			} else {
				return get_option( $option_name );
			}
	}
}
/**
 * Возвращает то, что может быть результатом delete_blog_option, delete_option
 * 
 * @since 3.0.0
 *
 * @param string $option_name (require)
 * @param string $n (not require)
 * @param string $type (not require) (@since 3.5.5)
 * @param string $source_settings_name (not require) (@since 3.6.4)
 *
 * @return bool
 */
function yfym_optionDEL( $option_name, $n = '', $type = '', $source_settings_name = '' ) {
	if ( $option_name == '' ) {
		return false;
	}
	switch ( $type ) {
		case "set_arr":
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
			unset( $yfym_settings_arr[ $n ][ $option_name ] );
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), 'yfym_settings_arr', $yfym_settings_arr );
			} else {
				return update_option( 'yfym_settings_arr', $yfym_settings_arr );
			}
		case "custom_set_arr":
			if ( $source_settings_name === '' ) {
				return false;
			}
			if ( $n === '' ) {
				$n = '1';
			}
			$yfym_settings_arr = yfym_optionGET( $source_settings_name );
			unset( $yfym_settings_arr[ $n ][ $option_name ] );
			if ( is_multisite() ) {
				return update_blog_option( get_current_blog_id(), $source_settings_name, $yfym_settings_arr );
			} else {
				return update_option( $source_settings_name, $yfym_settings_arr );
			}
		default:
			if ( $n === '1' ) {
				$n = '';
			}
			$option_name = $option_name . $n;
			if ( is_multisite() ) {
				return delete_blog_option( get_current_blog_id(), $option_name );
			} else {
				return delete_option( $option_name );
			}
	}
}
/**
 * Создает tmp файл-кэш товара
 * 
 * @since 2.0.0
 * @since 3.0.0 добавлена поддержка нескольких фидов
 * @since 3.0.2 исправлена критическая ошибка
 * @since 3.1.0 добавлен параметр ids_in_yml
 * 
 * @return void
 */
function yfym_wf( $result_yml, $post_id, $feed_id = '1', $ids_in_yml = '' ) {
	if ( ! is_dir( YFYM_PLUGIN_UPLOADS_DIR_PATH ) ) {
		error_log( 'WARNING: Папки ' . YFYM_PLUGIN_UPLOADS_DIR_PATH . ' нет; Файл: functions.php; Строка: ' . __LINE__, 0 );
		if ( ! mkdir( YFYM_PLUGIN_UPLOADS_DIR_PATH ) ) {
			error_log( 'ERROR: Создать папку ' . YFYM_PLUGIN_UPLOADS_DIR_PATH . ' не вышло; Файл: functions.php; Строка: ' . __LINE__, 0 );
		} else {
			if ( yfym_optionGET( 'yzen_yandex_zen_rss' ) == 'enabled' ) {
				$result_yml = yfym_optionGET( 'yfym_feed_content' );
			}
		}
	} else {
		if ( yfym_optionGET( 'yzen_yandex_zen_rss' ) == 'enabled' ) {
			$result_yml = yfym_optionGET( 'yfym_feed_content' );
		}
	}

	$name_dir = YFYM_PLUGIN_UPLOADS_DIR_PATH . '/feed' . $feed_id;
	if ( ! is_dir( $name_dir ) ) {
		error_log( 'WARNING: Папки $name_dir =' . $name_dir . ' нет; Файл: functions.php; Строка: ' . __LINE__, 0 );
		if ( ! mkdir( $name_dir ) ) {
			error_log(
				'ERROR: Создать папку $name_dir =' . $name_dir . ' не вышло; Файл: functions.php; Строка: ' . __LINE__, 0 );
		}
	}
	if ( is_dir( $name_dir ) ) {
		$filename = $name_dir . '/' . $post_id . '.tmp';
		$fp = fopen( $filename, "w" );
		fwrite( $fp, $result_yml ); // записываем в файл текст
		fclose( $fp ); // закрываем

		/* C версии 3.1.0 */
		$filename = $name_dir . '/' . $post_id . '-in.tmp';
		$fp = fopen( $filename, "w" );
		fwrite( $fp, $ids_in_yml );
		fclose( $fp );
		/* end с версии 3.1.0 */
	} else {
		error_log( 'ERROR: Нет папки yfym! $name_dir =' . $name_dir . '; Файл: functions.php; Строка: ' . __LINE__, 0 );
	}
}
/**
 * @since 3.3.0
 *
 * @return string
 */
function yfym_formatSize( $bytes ) {
	if ( $bytes >= 1073741824 ) {
		$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ( $bytes >= 1048576 ) {
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} elseif ( $bytes > 1 ) {
		$bytes = $bytes . ' B';
	} elseif ( $bytes == 1 ) {
		$bytes = $bytes . ' B';
	} else {
		$bytes = '0 KB';
	}
	return $bytes;
}

/**
 * @since 3.3.16
 *
 * @return string
 */
function yfym_replace_decode( $string, $feed_id = '1' ) {
	$string = str_replace( "+", 'yfym', $string );
	$string = urldecode( $string );
	$string = str_replace( "yfym", '+', $string );
	$string = apply_filters( 'yfym_replace_decode_filter', $string, $feed_id );
	return $string;
}
/**
 * @since 3.4.0
 *
 * @param string $array (require)
 * @param string/int $key (require)
 * @param string/int $default_data (not require)
 *
 * @return mixed
 */
function yfym_data_from_arr( $array, $key, $default_data = null ) {
	if ( isset( $array[ $key ] ) ) {
		return $array[ $key ];
	} else {
		return $default_data;
	}
}
/**
 * @since 3.4.0
 *
 * @param array $field (require)
 *
 * Function based woocommerce_wp_select
 * https://stackoverflow.com/questions/23287358/woocommerce-multi-select-for-single-product-field
 */
function yfym_woocommerce_wp_select_multiple( $field, $blog_option = false ) {
	if ( $blog_option === false ) {
		global $thepostid, $post, $woocommerce;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['value'] = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );
	} else { // если у нас глобальные настройки, а не метаполя, то данные тащим через yfym_optionGET
		global $woocommerce;
		$field['value'] = isset( $field['value'] ) ? $field['value'] : ( yfym_optionGET( $field['id'] ) ? yfym_optionGET( $field['id'] ) : array() );
	}

	$field['class'] = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['name'] = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['label'] = isset( $field['label'] ) ? $field['label'] : '';

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '[]" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';

	foreach ( $field['options'] as $key => $value ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) ) {
		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}
	}

	echo '</p>';
}

/**
 * Возвращает количетсво всех фидов
 * 
 * @since 3.5.0
 *
 * @return int
 */
function yfym_number_all_feeds() {
	$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
	if ( false === $yfym_settings_arr ) {
		return -1;
	} else {
		return count( $yfym_settings_arr );
	}
}
/**
 * Получает первый фид. Используется на случай если get-параметр numFeed не указан
 * 
 * @since 3.7.0
 *
 * @return (string) feed ID or (string)''
 */
function yfym_get_first_feed_id() {
	$yfym_settings_arr = yfym_optionGET( 'yfym_settings_arr' );
	if ( ! empty( $yfym_settings_arr ) ) {
		return (string) array_key_first( $yfym_settings_arr );
	} else {
		return '';
	}
}
/**
 * The function replaces the domain in the URL
 * 
 * @since 3.7.5
 *
 * @param string $url (require)
 * @param string $feed_id (require)
 *
 * @return string
 */
function yfym_replace_domain( $url, $feed_id ) {
	$new_url = yfym_optionGET( 'yfym_replace_domain', $feed_id, 'set_arr' );
	if ( ! empty( $new_url ) ) {
		$domain = home_url(); // parse_url($url, PHP_URL_HOST);
		$new_url = (string) $new_url;
		$url = str_replace( $domain, $new_url, $url );
	}
	return $url;
}