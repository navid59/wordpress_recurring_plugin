<?php
/**
*   Add the 'recurring_notify' query variable so WordPress
*   won't remove it.
*/
add_filter( 'query_vars', 'ntp_add_query_vars');
function ntp_add_query_vars($vars){
   $vars[] = "recurring_notify";
   return $vars;
}

add_action('template_include', 'ntpRecurringNotifyValidation');

function ntpRecurringNotifyValidation($template) {
    global $wp_query;

    // If the 'recurring_notify' query var isn't appended to the URL,
    // don't do anything and return default
    if(!isset($wp_query->query['pagename']) || $wp_query->query['pagename'] !== 'recurring_notify') {
        return $template;
    } else {
        // Step #1 - Make sure request is come from NETOPIA Recurring API
        // Step #2 - Make sure if request is for this Comerciant
        getHeaderRequest();
        die();
    }
}

function getHeaderRequest() {
    // get Header
    // Get data
    // Add Payment History for subscription
    // Add Log 
    // Update Subscription Data
    // Add Log
    //
    global $wpdb;
    $logFile = '/var/www/html/wordpress-ntp-recurring/wp-content/plugins/netopia-recurring/log/log_navid_'.date("j.n.Y").'.log';

    $headers = apache_request_headers();
    foreach ($headers as $header => $value) {
        /** Log Temporar */
        file_put_contents($logFile, $header.' : '.$value."\n", FILE_APPEND);
    }
    
    $data = file_get_contents('php://input');

    /** Log Temporar */
    file_put_contents($logFile, "-------------------------\n", FILE_APPEND);
    file_put_contents($logFile, $data."\n", FILE_APPEND);

    ////////////////
    // $wpdb->insert( 
    //     $wpdb->prefix . "ntp_history", 
    //     array( 
    //         'Subscription_Id' => $XX['Subscription_Id'],
    //         'TransactionID'   => $XX['TransactionID'],
    //         'PaymentComment'  => $XX['PaymentComment'],
    //         'Label'           => $XX['Label'],
    //         'Status'          => $XX['Status'],
    //         'CreatedAt'       => date("Y-m-d")
    //     )
    // );
    ////////////////

    $arrDate = json_decode($data, true);
    
    /** Log Temporar */
    file_put_contents($logFile, "-------------------------\n", FILE_APPEND);
    file_put_contents($logFile, print_r($arrDate, true)."\n", FILE_APPEND);



}

function getBodyRequest() {

}

function sanitizeBody() {

}

function sendResponse() {

}