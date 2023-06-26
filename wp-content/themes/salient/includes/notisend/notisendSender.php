<?php

use Automattic\WooCommerce\Admin\API\Reports\Customers\DataStore as CustomersDataStore;

require_once "NotisendSettings.php";

/** Create recipient for notisend in clients group
 * @param array $data
 *
 * @return bool return true if recipient was created or email exist
 */
function createRecipients($data, $method): bool {

	$notisendSettings = NotisendSettings::getSettings();

	$ch = curl_init($notisendSettings->address . $method);
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = [
		'Content-Type: application/json',
		"Authorization: Bearer $notisendSettings->apiToken"
	];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);

	switch (curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 201:
			$result = true;
			break;
		case 422:
			$data = json_decode($response);
			if (isset($data->errors) && count($data->errors) > 0
			    && $data->errors[0]->detail==='Email has already been taken') {

				$result = true;
			} else {
				$result = false;
			}
			break;
		default:
			$result = false;
	}
	curl_close($ch);
	return $result;
}

add_action( 'wp_ajax_notisend_export_clients', 'notisend_action_export_clients' );

/** export recipients in notisend from table of customer woocommerce
 * @return void
 */
function notisend_action_export_clients() {
	global $wpdb;
	$notisendSettings = NotisendSettings::getSettings();

	$customers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_customer_lookup WHERE 
                                        date_registered > '$notisendSettings->start_date_registered'");
	$records = 0;
	$requestData = [];
	foreach ( $customers as $customer ) {
		$value = [];
		if (!empty($notisendSettings->param_city) && !empty($customer->city)) {
			$value[] =  [
				'parameter_id'=>$notisendSettings->param_city,
				'value'=>$customer->city
			];
		}
		if (!empty($notisendSettings->param_date_registered) && !empty($customer->date_registered)) {
			$date_registered = DateTime::createFromFormat('Y-m-d H:i:s', $customer->date_registered);
			if ($date_registered!==false) {
				$value[] = [
					'parameter_id' => $notisendSettings->param_date_registered,
					'value'        => $date_registered->format('d.m.Y H:i')
				];
			}
		}
		if ( !empty( $notisendSettings->param_phone ) ) {
			$phone = get_user_meta( $customer->user_id, 'billing_phone', true );
			if ( !empty( $phone ) ) {
				$value[] = [
					'parameter_id' => $notisendSettings->param_phone,
					'value'        => $phone
				];
			}
		}
		if (!empty($customer->email)) {
			$requestData['recipients'][] = [
				'email'=> $customer->email,
				'values' => $value
			];
			$records++;
		}

		if ($records===1000) {
//			createRecipients( $requestData, "/email/lists/$notisendSettings->group/recipients/imports");
			$records = 0;
			$requestData=[];
		}
	}
	if ($records>0) {
//		createRecipients( $requestData, "/email/lists/$notisendSettings->group/recipients/imports");
	}
}
