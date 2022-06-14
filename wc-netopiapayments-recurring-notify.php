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


    $ipnResponse = $ntpIpn->verifyIPN();
    if(($ipnResponse['errorType'] == IPN::ERROR_TYPE_TEMPORARY) && ($ipnResponse['errorCode'] == IPN::RECURRING_ERROR_CODE_NEED_VERIFY)) {
        // Verify API KEY
        $headers = apache_request_headers();
        if(hasToken($headers)) {
            // Add Log & Add History
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
                $wpdb->insert( 
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
		

        
		echo("Done!\n\n");
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


