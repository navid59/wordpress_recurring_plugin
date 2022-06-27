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

   return $vars;
}

add_action('template_include', 'ntpRecurringNotifyValidation');
function ntpRecurringNotifyValidation($template) {
    global $wp_query;

    /**
     * To manage Requests
     * If the 'recurring_notify' query var isn't appended to the URL,
     * return default template
     */
    if(!isset($wp_query->query['name']) || $wp_query->query['name'] !== 'recurring_notify') {
        return $template;
    } else {
        status_header(200);
        getHeaderRequest();
        die();
    }
}

add_action('template_include', 'ntpRecurring3DSAuthorize');
function ntpRecurring3DSAuthorize($template) {
    global $wp_query;

    // If the 'recurring_3DSAuthorize' query var isn't appended to the URL,
    // don't do anything and return default
    if(!isset($wp_query->query['name']) || $wp_query->query['name'] !== 'recurring_3DSAuthorize') {
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

    $headers = apache_request_headers();

    file_put_contents($logFile, "[".$logDate."] ".$header." \n", FILE_APPEND);
    file_put_contents($logFile, "[".$logDate."] ----------------------------- \n", FILE_APPEND);
    
    
}

function getHeaderRequest() {
    global $wpdb;
    $obj = new recurring();
    /** Log Durring Implimentare*/
    (new DumpHTTPRequestToFile)->execute(WP_PLUGIN_DIR . '/netopia-recurring/log/notifyUrl.txt');
    
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
    $ntpIpn->publicKeyStr = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAy6pUDAFLVul4y499gz1P\ngGSvTSc82U3/ih3e5FDUs/F0Jvfzc4cew8TrBDrw7Y+AYZS37D2i+Xi5nYpzQpu7\nryS4W+qvgAA1SEjiU1Sk2a4+A1HeH+vfZo0gDrIYTh2NSAQnDSDxk5T475ukSSwX\nL9tYwO6CpdAv3BtpMT5YhyS3ipgPEnGIQKXjh8GMgLSmRFbgoCTRWlCvu7XOg94N\nfS8l4it2qrEldU8VEdfPDfFLlxl3lUoLEmCncCjmF1wRVtk4cNu+WtWQ4mBgxpt0\ntX2aJkqp4PV3o5kI4bqHq/MS7HVJ7yxtj/p8kawlVYipGsQj3ypgltQ3bnYV/LRq\n8QIDAQAB\n-----END PUBLIC KEY-----\n";


    $ipnResponse = $ntpIpn->verifyIPN();

    /**
    * IPN Output
    */
    echo json_encode($ipnResponse);   

    if(($ipnResponse['errorType'] == IPN::ERROR_TYPE_TEMPORARY) && ($ipnResponse['errorCode'] == IPN::RECURRING_ERROR_CODE_NEED_VERIFY)) {
        // Verify API KEY
        // Add Log - TMP
    file_put_contents($logFile, "[".$logDate."] - wc-netopiapayments-recurring-notify -- AFter -verifyIPN()-- => 512 & 1----- \n", FILE_APPEND);

        $headers = apache_request_headers();
        if(hasToken($headers)) {
            // Add Log & Add History
            file_put_contents($logFile, "[".$logDate."] - Has X-apikey and should add in History Table ---------------- \n", FILE_APPEND);
            
            $data = file_get_contents('php://input');
            $arrDate = json_decode($data, true);
            if($arrDate['NotifySubscription']['Action'] == "Unsubscribe") {
                $wpdb->insert( 
                    $wpdb->prefix . $obj->getDbSourceName('history'), 
                    array( 
                        'Subscription_Id'=> $arrDate['NotifySubscription']['SubscriptionID'],
                        'TransactionID'  => $arrDate['NotifyOrder']['orderID'],
                        'NotifyContent'  => $data,
                        'Comment'        => $arrDate['NotifyPayment']['Message'],
                        'Status'         => 2,
                        'CreatedAt'      => date("Y-m-d H:i:s")
                    )
                );
            } else {
                $insertResult = $wpdb->insert( 
                    $wpdb->prefix . $obj->getDbSourceName('history'), 
                    array( 
                        'Subscription_Id'=> $arrDate['NotifySubscription']['SubscriptionID'],
                        'TransactionID'  => $arrDate['NotifyOrder']['orderID'],
                        'NotifyContent'  => $data,
                        'Comment'        => $arrDate['NotifyPayment']['Message'],
                        'Status'         => $arrDate['NotifyPayment']['Status'],
                        'CreatedAt'      => date("Y-m-d H:i:s")
                    )
                ); 
            }
            

            /** Log IPN */
            file_put_contents($logFile, "[".$logDate."] IPN - Subscription ".$arrDate['NotifySubscription']['SubscriptionID']." added in History - DB -> TB \n", FILE_APPEND);
            file_put_contents($logFile, "[".$logDate."] - ---------------- STEP 1 is DONE ---------------- \n", FILE_APPEND);
        } else {
            /** Log IPN */
            file_put_contents($logFile, "[".$logDate."] IPN - Request is not a valid request \n", FILE_APPEND);
        }        
    } else {
        //-------------
        /** Log Temporar */
        file_put_contents($logFile, print_r($ipnResponse, true)."\n", FILE_APPEND);
        file_put_contents($logFile, "[".$logDate."] - - CHOOOOS----- \n", FILE_APPEND);
    }
}


function hasToken($header) {
    $ntpIpn = new IPN();
    if($ntpIpn->hasXapikeyHeader($header)) {
        $apikeyInHeader = getApiKeyFromHeader($header);
        if($apikeyInHeader) {
            if(isValidToken($apikeyInHeader)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }       
    } else {
        return false;
    }
}

function getApiKeyFromHeader($header) {
    file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', print_r('getApiKeyFromHeader Function', true)."\n", FILE_APPEND);
    file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', "----------------------------------------\n", FILE_APPEND);
    foreach($header as $key => $value) {
        if(strtolower($key) == strtolower('X-Apikey')) {
            file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', print_r($value, true)."\n", FILE_APPEND);
            return $value;
        }
    }
    return false;
}

function isValidToken($apiKey) {
    $obj = new recurring();

    file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', "********************************\n", FILE_APPEND);
    file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', print_r($obj->getApiKey(), true)."\n", FILE_APPEND);
    file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', print_r($apiKey, true)."\n", FILE_APPEND);


    if(ltrim($obj->getApiKey()) == ltrim($apiKey)) {
        file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', "** YES **\n", FILE_APPEND);
        return true;
    } else {
        file_put_contents(WP_PLUGIN_DIR . '/netopia-recurring/log/tmp.log', "** NO **\n", FILE_APPEND);
        return false;
    }
}


/********************************************
*            Just for debuging              *
*********************************************/
class DumpHTTPRequestToFile {
	public function execute($targetFile) {
		$data = sprintf(
			"%s %s %s\n\nHTTP headers:\n",
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI'],
			$_SERVER['SERVER_PROTOCOL']
		);

		foreach ($this->getHeaderList() as $name => $value) {
			$data .= $name . ': ' . $value . "\n";
		}

		$data .= "\nRequest body:\n";
        
		file_put_contents(
			$targetFile,
			$data . file_get_contents('php://input') . "\n", FILE_APPEND
		);
		
		file_put_contents(
			$targetFile,
			$data . print_r($_REQUEST,true) . "\n*************************************************************\n", FILE_APPEND
		);
	}

	private function getHeaderList() {
		$headerList = [];
		foreach ($_SERVER as $name => $value) {
			if (preg_match('/^HTTP_/',$name)) {
				// convert HTTP_HEADER_NAME to Header-Name
				$name = strtr(substr($name,5),'_',' ');
				$name = ucwords(strtolower($name));
				$name = strtr($name,' ','-');

				// add to list
				$headerList[$name] = $value;
			}
		}

		return $headerList;
	}
}


