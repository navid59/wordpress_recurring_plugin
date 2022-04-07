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
    if($wp_query->query['pagename'] !== 'recurring_notify') {
        return $template;
    } else {
        // Step #1 - Make sure request is come from NETOPIA Recurring API
        // Step #2 - Make sure if request is for this Comerciant
        getHeaderRequest();
        die();
    }
}

function getHeaderRequest() {
    $headers = apache_request_headers();
    foreach ($headers as $header => $value) {
        echo "$header: $value <br />\n";
    }
    echo "<hr>";
    $data = file_get_contents('php://input');
    echo $data;
    echo "<hr>";
    $arrayDate = json_decode($data);
    echo "<pre>";
    var_dump($arrayDate);
    echo "</pre>";
    echo dirname(__FILE__);
    echo file_put_contents('/var/www/html/wordpress-ntp-recurring/wp-content/plugins/netopia-recurring/log/log_navid_'.date("j.n.Y").'.log', $data, FILE_APPEND);
}

function getBodyRequest() {

}

function sanitizeBody() {

}

function sendResponse() {

}