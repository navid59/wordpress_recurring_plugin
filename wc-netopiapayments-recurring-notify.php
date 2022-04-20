<?php
include_once( 'packages/recurring.php' );
include_once( 'packages/ipn.php' );

/**
* Add the 'recurring_notify' query variable to WP
* so WordPress won't remove it.
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
    global $wpdb;
    /** Log Time & Path*/
    $logDate = new DateTime();
    $logDate = $logDate->format("y:m:d h:i:s");
    $logFile = WP_PLUGIN_DIR . '/netopia-recurring/log/log_'.date("j.n.Y").'.log';

    $ntpIpn = new IPN();
    $ntpIpn->activeKey         = '1PD2-FYKC-R27B-55BW-NVGN'; // activeKey or posSignature
    $ntpIpn->posSignatureSet[] = '1PD2-FYKC-R27B-55BW-NVGN'; // Another posSignature, base on DemoV2. Idea Alex
    
    $ntpIpn->hashMethod        = 'SHA512';
    $ntpIpn->alg               = 'RS512';
    $ntpIpn->publicKeyStr      = '-----BEGIN CERTIFICATE-----
    MIIDKjCCApOgAwIBAgIBADANBgkqhkiG9w0BAQQFADCBsTELMAkGA1UEBhMCUk8x
    EjAQBgNVBAgMCUJ1Y2hhcmVzdDESMBAGA1UEBwwJQnVjaGFyZXN0MRcwFQYDVQQK
    DA5OIEUgVCBPIFAgSSBBIDEnMCUGA1UECwweTiBFIFQgTyBQIEkgQSBEZXZlbG9w
    bWVudCBUZWFtMRQwEgYDVQQDDAttb2JpbHBheS5ybzEiMCAGCSqGSIb3DQEJARYT
    c3VwcG9ydEBtb2JpbHBheS5ybzAeFw0yMjA0MTIwNzM3NTFaFw0yMzA0MTIwNzM3
    NTFaMIGxMQswCQYDVQQGEwJSTzESMBAGA1UECAwJQnVjaGFyZXN0MRIwEAYDVQQH
    DAlCdWNoYXJlc3QxFzAVBgNVBAoMDk4gRSBUIE8gUCBJIEEgMScwJQYDVQQLDB5O
    IEUgVCBPIFAgSSBBIERldmVsb3BtZW50IFRlYW0xFDASBgNVBAMMC21vYmlscGF5
    LnJvMSIwIAYJKoZIhvcNAQkBFhNzdXBwb3J0QG1vYmlscGF5LnJvMIGfMA0GCSqG
    SIb3DQEBAQUAA4GNADCBiQKBgQC8IdPzYRKWRbir4IWfTe+Ql22tOTFjQoeNtpHH
    xSm6j+WFYglAYNzHOWWHdXtF4vVItUCNmf4773Iaw2RkMI2qwKa90vW6MBxJGR/N
    WaJTqDxwWW2KQNvASMh2EXGk14y7YgRr46cLs5Y5l3gaFS4pyGhNCFKTHp/TC1ht
    nxjHXQIDAQABo1AwTjAdBgNVHQ4EFgQUPclwoTBsc1M0H5ZpF09aMiAaHrUwHwYD
    VR0jBBgwFoAUPclwoTBsc1M0H5ZpF09aMiAaHrUwDAYDVR0TBAUwAwEB/zANBgkq
    hkiG9w0BAQQFAAOBgQBSuiaKTfSvKpITyynumjGWtibSn4tk735l07TKKhk7ow6Q
    104673t2Eht3A3tYZarlKe4+OumS0gxjhEYp5gcsEFW8naEz6NO4TXKTzz/Sgakk
    81SmQnaqcQ/DCtxDwV71qRgvojIDR6CIPutdWEk5H5rJTaljT2ZBWd97SsDd0g==
    -----END CERTIFICATE-----';


    $ipnResponse = $ntpIpn->verifyIPN();

    file_put_contents($this->logFile, "[".$logDate."] IPN - TEST \n", FILE_APPEND);
    file_put_contents($this->logFile, print_r($ipnResponse, true)." \n", FILE_APPEND);

    if($ipnResponse['errorType'] == $ntpIpn->ERROR_TYPE_TEMPORARY && $ipnResponse['errorType'] == $ntpIpn->RECURRING_ERROR_CODE_NEED_VERIFY) {
        // Verify API KEY
        $headers = apache_request_headers();
        if(hasToken($headers)) {
            // Add Log & Add History
            $data = file_get_contents('php://input');
            $arrDate = json_decode($data, true);
            $wpdb->insert( 
                $wpdb->prefix . "ntp_history", 
                array( 
                    'Subscription_Id'=> $arrDate['NotifySubscription']['SubscriptionID'],
                    'TransactionID'  => $arrDate['NotifyOrder']['orderID'],
                    'NotifyContent'  => $data,
                    'Comment'        => $arrDate['NotifyPayment']['Message'],
                    'Status'         => $arrDate['NotifyPayment']['PaymetCode'],
                    'CreatedAt'      => date("Y-m-d")
                )
            );

            /** Log IPN */
            file_put_contents($this->logFile, "[".$logDate."] IPN - Subscription added in DB \n", FILE_APPEND);
        } else {
            /** Log IPN */
            file_put_contents($this->logFile, "[".$logDate."] IPN - Request is not a valid request \n", FILE_APPEND);
        }        
    } else {
        //-------------
        /** Log Temporar */
    file_put_contents($logFile, '-------------- New IPN - AFTER Verify JWT ----------------'."\n", FILE_APPEND);
    file_put_contents($logFile, $ipnResponse."\n", FILE_APPEND);


    /**
     * IPN Output
     */
    echo json_encode($ipnResponse);
        //-------------
    }
}

function getHeaderRequest_OK_OLD() {
    // get Header
    // Get data
    // Add Payment History for subscription
    // Add Log 
    // Update Subscription Data
    // Add Log
    //
    global $wpdb;
    $logFile = WP_PLUGIN_DIR . '/netopia-recurring/log/log_navid_'.date("j.n.Y").'.log';
    
    $headers = apache_request_headers();
    if(hasToken($headers)) {
        // Add Log & Add History

        $data = file_get_contents('php://input');
         /** Log Temporar */
        file_put_contents($logFile, "-------------------------\n", FILE_APPEND);
        file_put_contents($logFile, $data."\n", FILE_APPEND);

        $arrDate = json_decode($data, true);

        /** Log Temporar */
        file_put_contents($logFile, "-------------------------\n", FILE_APPEND);
        file_put_contents($logFile, print_r($arrDate, true)."\n", FILE_APPEND);

        $wpdb->insert( 
            $wpdb->prefix . "ntp_history", 
            array( 
                'Subscription_Id'=> $arrDate['NotifySubscription']['SubscriptionID'],
                'TransactionID'  => $arrDate['NotifyOrder']['orderID'],
                'NotifyContent'  => $data,
                'Comment'        => $arrDate['NotifyPayment']['Message'],
                'Status'         => $arrDate['NotifyPayment']['PaymetCode'],
                'CreatedAt'      => date("Y-m-d")
            )
        );

        /** Test Email - Log Temporar */
        $mailResult = false;
        $mailResult = wp_mail( 'test@navid.ro', 'Mail works', 'Mail from Notify URL' );
        
        file_put_contents($logFile, "---------- EMAIL ---------------\n", FILE_APPEND);
        file_put_contents($logFile, $mailResult."\n", FILE_APPEND);

    } else {
        // Log may by IP Token is not found in Header
    }
}

function hasToken($header) {
    if (array_key_exists('Apikey', $header)) {
        if(isValidToken($header['Apikey'])) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function isValidToken($apiKey) {
    $obj = new recurring();
    if($obj->getApiKey() === $apiKey) {
        return true;
    } else {
        return false;
    }
}

function getBodyRequest() {

}

function sanitizeBody() {

}

function sendResponse() {

}