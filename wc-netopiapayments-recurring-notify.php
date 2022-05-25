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
   $vars[] = "recurring_3DSAuthorize";

//    echo "<pre>";
//    var_dump($vars);
//    echo "</pre>";
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
add_action('template_include', 'ntpRecurring3DSAuthorize');
function ntpRecurring3DSAuthorize($template) {
    global $wp_query;

    // If the 'recurring_3DSAuthorize' query var isn't appended to the URL,
    // don't do anything and return default
    if(!isset($wp_query->query['pagename']) || $wp_query->query['pagename'] !== 'recurring_3DSAuthorize') {
        return $template;
    } else {
        // Step #1 - Make sure request is come from NETOPIA Recurring API
        // Step #2 - Make sure if request is for this Comerciant
        get3DSAuthorizeRedirect();
        die();
    }
}

function get3DSAuthorizeRedirect() {
    /** Log Time & Path*/
    $logDate = new DateTime();
    $logDate = $logDate->format("y:m:d h:i:s");
    $logFile = WP_PLUGIN_DIR . '/netopia-recurring/log/3DSAuth_'.date("j.n.Y").'.log';
    /** 3DSAuth log */
    file_put_contents($logFile, "[".$logDate."] 3DSAuth Hint \n", FILE_APPEND);

    echo "<pre>";
    var_dump($_REQUEST);
    echo "</pre>";
    // echo "You will be redirect to bank!!";
    echo $strHtmlForm = stripslashes($_POST['html']);
    file_put_contents($logFile, "[".$logDate."] ".print_r($_POST['html'], true)." \n", FILE_APPEND);
    file_put_contents($logFile, "[".$logDate."] --------------------------------- \n", FILE_APPEND);
    ?>
    <form name="3DSAuthorizeForm" id="3DSAuthorizeForm" target="" action="https://secure.sandbox.netopia-payments.com/sandbox/authorize" method="POST">
        <input type="text" name="paReq" value="oEcADE3fRoK8BCPBx90In7YGw7DfsKubchD0PyHmy7L42m5YfnKdnPYAAvk8B_OyKmMKdHo4KvxExTygaSkuMTQ=">
        <input type="text" name="backUrl" value="https://navid.ro">
        <button type="submit" class="btn btn-primary">Submit AAAAA</button>
    </form>

    <script type="text/javascript">
        window.onload=function(){
            var auto = setTimeout(function(){ autoRefresh(); }, 100);

            function submitform(){
            alert('Temporar to see the Form value for TEST');
            document.forms["3DSAuthorizeForm"].submit();
            }

            function autoRefresh(){
            clearTimeout(auto);
            auto = setTimeout(function(){ submitform(); autoRefresh(); }, 1000);
            }
        }
    </script>
   <?php
}

function getHeaderRequest() {
    global $wpdb;
    $obj = new recurring();
    /** Log Time & Path*/
    $logDate = new DateTime();
    $logDate = $logDate->format("y:m:d h:i:s");
    $logFile = WP_PLUGIN_DIR . '/netopia-recurring/log/log_'.date("j.n.Y").'.log';

    $ntpIpn = new IPN();
    $ntpIpn->logFile           = $logFile;
    $ntpIpn->activeKey         = $obj->getSignature(); // activeKey or posSignature
    $ntpIpn->posSignatureSet[] = $ntpIpn->activeKey; // Another posSignature, base on DemoV2. Idea Alex - if exist!!!
    
    $ntpIpn->hashMethod        = 'SHA512';
    $ntpIpn->alg               = 'RS512';


    $ipnResponse = $ntpIpn->verifyIPN();
    if(($ipnResponse['errorType'] == IPN::ERROR_TYPE_TEMPORARY) && ($ipnResponse['errorCode'] == IPN::RECURRING_ERROR_CODE_NEED_VERIFY)) {
        // Verify API KEY
        $headers = apache_request_headers();
        if(hasToken($headers)) {
            // Add Log & Add History
            $data = file_get_contents('php://input');
            $arrDate = json_decode($data, true);
            $wpdb->insert( 
                $wpdb->prefix . $obj->getDbSourceName('history'), 
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
            file_put_contents($logFile, "[".$logDate."] IPN - Subscription ".$arrDate['NotifySubscription']['SubscriptionID']." added in DB \n", FILE_APPEND);
            file_put_contents($logFile, "[".$logDate."] - ---------------- STEP 1 is DONE ---------------- \n", FILE_APPEND);
        } else {
            /** Log IPN */
            file_put_contents($logFile, "[".$logDate."] IPN - Request is not a valid request \n", FILE_APPEND);
        }        
    } else {
        //-------------
        /** Log Temporar */
        file_put_contents($logFile, '-------------- STEP 2 is DONE  ----------------'."\n", FILE_APPEND);
        file_put_contents($logFile, print_r($ipnResponse, true)."\n", FILE_APPEND);


        /**
         * IPN Output
         */
        echo json_encode($ipnResponse);
        //-------------
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