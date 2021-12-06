<?php

/*
 * SendPulse REST API Usage Example
 *
 * Documentation
 * https://login.sendpulse.com/manual/rest-api/
 * https://sendpulse.com/api
 *
 * Settings
 * https://login.sendpulse.com/settings/#api
 */
require_once WP_CONTENT_DIR . '/themes/salient/Sendpulse/ApiClient.php';
require_once WP_CONTENT_DIR . '/themes/salient/Sendpulse/ApiInterface.php';


use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

define('API_USER_ID', '23d6aaad230fefe62b21369f899b815e');
define('API_SECRET', 'ecbac068d7f4212483c04a4353bba4dd');
define('PATH_TO_ATTACH_FILE', __FILE__);



function ee_add_email_addresses( $book_id, $emails, $data =[] ){
    $SPApiClient = new ApiClient(API_USER_ID, API_SECRET, new FileStorage());
    $SPApiClient->addEmails( $book_id, $emails,$data);
}
