<?php
    class recurring {
        protected $slug = 'netopia_recurring';

        
        /**
        *  get Public Key
        */
        function getPublicKey() {
            return get_option($this->slug.'_general_public_key_file_name', array());
        }

        function getSignature() {
            return get_option($this->slug.'_signature', array());
        }

        function getApiKey() {
            if($this->isLive()) {
                return get_option($this->slug.'_api_key', array());
            } else {
                return get_option($this->slug.'_api_key_sandbox', array());
            }
            
        }

        function isLive() {
            $mood = get_option($this->slug.'_mood', array());
            if(count($mood)){
                return $mood[0] == 'live' ? true : false;
            } else {
                /* Do nothing 
                *  Is just for handel PHP Error Notify on Merchant Server
                */
                return;
            }  
        }

        function getNTPID() {
            if ( ! session_id() ) {
                session_start();
            }
            // $ntpRpNtpID = isset($_COOKIE['ntpRp-cookies-NtpID']) ? $_COOKIE['ntpRp-cookies-NtpID'] : "";
            $ntpRpNtpID = isset($_SESSION['ntpRp-session-NtpID']) ? $_SESSION['ntpRp-session-NtpID'] : "";
            return $ntpRpNtpID;
        }

        function getAuthenticationToken() {
            if ( ! session_id() ) {
                session_start();
            }
            // $ntpRpAuthenticationToken = isset($_COOKIE['ntpRp-cookies-AuthenticationToken']) ? $_COOKIE['ntpRp-cookies-AuthenticationToken'] : "";
            $ntpRpAuthenticationToken = isset($_SESSION['ntpRp-session-AuthenticationToken']) ? $_SESSION['ntpRp-session-AuthenticationToken'] : "";
            return $ntpRpAuthenticationToken;
        }

        function getSubscriptionData() {
            if ( ! session_id() ) {
                session_start();
            }
            // $ntpRpSubscriptionData = $_COOKIE['ntpRp-cookies-json'];
            $ntpRpSubscriptionData = $_SESSION['ntpRp-session-json'];
            return (stripslashes($ntpRpSubscriptionData));
        }

        function getApiUrl($action){
            if($this->isLive()) {
                $Url = BASE_URL_RECURRING_API_LIVE.$action;
            } else {
                $Url = BASE_URL_RECURRING_API_SANDBOX.$action;
            }
            return $Url;
        }

        function getNotifyUrl() {
            // return get_site_url()."/".get_option($this->slug.'_notify_url', array()); 
            return get_site_url()."/recurring_notify"; 
        }

        function getBackUrl($planId) {
            $parts = parse_url( home_url() );
            $current_uri = "{$parts['scheme']}://{$parts['host']}" . add_query_arg( 'planId', $planId );
            // $current_uri = basename($_SERVER['REQUEST_URI']) . add_query_arg( 'planId', $planId );
            $backUrl = $current_uri;
            return $backUrl;
        }

        function getSuccessMessagePayment() {
            $msg = get_option($this->slug.'_subscription_reg_msg');
            return $msg; 
        }

        function getFailedMessagePayment() {
            $msg = get_option($this->slug.'_subscription_reg_failed_msg');
            return $msg; 
        }

        function getUnsuccessMessage() {
            $msg = get_option($this->slug.'_unsubscription_msg');
            return $msg; 
        }

        function getAccountPageSetting() {
            $accountPageSubtitle = get_option($this->slug.'_account_subtitle', array());
            $accountPageFirstParagraph = get_option($this->slug.'_account_paragraph_first', array());
            $accountPageSecoundParagraph = get_option($this->slug.'_account_paragraph_secound', array());
            return array(
                "subtitle" => !empty($accountPageSubtitle) ? $accountPageSubtitle : __('My subscription account','ntpRp') ,
                "firstParagraph" => !empty($accountPageFirstParagraph) ? $accountPageFirstParagraph : __('Welcome to recurring account page','ntpRp'),
                "secoundParagraph" => !empty($accountPageSecoundParagraph) ? $accountPageSecoundParagraph : __('To get more information about your recurring situation, use the menu','ntpRp')
            );
        }

        function getLoginUrl() {
            global $wpdb;
            $loginUrl =  get_option($this->slug.'_login_url', array());
            $baseURL = get_site_url();
            return $loginUrl !== "" ? $baseURL.'/'.$loginUrl : $baseURL.'/login';
        }

        function getData($url, $requestData) {
            $authenticationToken = $this->getApiKey();

            $ch = curl_init($url); 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','token: '.$authenticationToken));
            $result = curl_exec($ch);
            $jsonDate = $result;
            $arrayData = json_decode($jsonDate, true);

            return($arrayData);
        }


        function getStatusStr($section, $statusCode) {
            switch ($section) {
                case 'subscription':
                    switch ($statusCode) {
                        case '0':
                            $statusStr = 'New';
                            break;
                        case '1':
                            $statusStr = 'Active';
                            break;
                        case '2':
                            $statusStr = 'Unsubscribed';
                            break;
                        default:
                            $statusStr = $statusCode;
                    }
                break;
                case 'plan':
                    switch ($statusCode) {
                        case '1':
                            $statusStr = __('Active Plan','ntpRp');
                            break;
                        case '2':
                            $statusStr = __('Inactive Plan','ntpRp');
                            break;
                        default:
                            $statusStr = $statusCode;
                    }
                break;
                case 'report':
                    switch ($statusCode) {
                        case '00':
                            $statusStr = 'Confirmed';
                            break;
                        case '2':
                            $statusStr = 'Unsubscribed';
                            break;
                        case '3':
                            $statusStr = 'Paid';
                            break;
                        case '12':
                            $statusStr = 'Not paid';
                            break;
                        case '15':
                            $statusStr = 'Authorized';
                            break;
                        default:
                            $statusStr = $statusCode;
                    }
                break;
            }
            return $statusStr;
         }

        public function encrypt($x509FilePath)
            {
             $this->_prepare();
             
             $publicKey = openssl_pkey_get_public("file://{$x509FilePath}");
             if($publicKey === false)
             {
                 $this->outEncData	= null;
                 $this->outEnvKey	= null;
                 $errorMessage = "Error while loading X509 public key certificate! Reason:";
                 while(($errorString = openssl_error_string()))
                 {
                     $errorMessage .= $errorString . "\n";
                 }
                 throw new \Exception($errorMessage, self::ERROR_LOAD_X509_CERTIFICATE);
             }
             $srcData = $this->_xmlDoc->saveXML();
             $publicKeys	= array($publicKey);
             $encData 	= null;
             $envKeys 	= null;
             $cipher_algo = 'RC4';
             $result 	= openssl_seal($srcData, $encData, $envKeys, $publicKeys, $cipher_algo);
             if($result === false)
             {
                 $this->outEncData	= null;
                 $this->outEnvKey	= null;
                 $errorMessage = "Error while encrypting data! Reason:";
                 while(($errorString = openssl_error_string()))
                 {
                     $errorMessage .= $errorString . "\n";
                 }
                 throw new \Exception($errorMessage, self::ERROR_ENCRYPT_DATA);
             }
             
             $this->outEncData 	= base64_encode($encData);
             $this->outEnvKey 	= base64_encode($envKeys[0]);
            }
     
        public function getEnvKey()
         {
             return $this->outEnvKey;
         }
     
        public function getEncData()
         {
             return $this->outEncData;
         }

        public function informMember($subject, $message) {
            if (empty($subject) || empty($message))
                return false;

            $current_user = wp_get_current_user();
            $to = $current_user->user_email;
            $mailResult = false;
            $mailResult = wp_mail( $to, $subject, $message );
            
        } 

        public function getDbSourceName($section) {
            $mod = $this->isLive() ? "live" : "sandbox";
            $DbSrc = '';
            switch ($mod) {
                case "live":
                    switch ($section) {
                        case "plan":
                            $DbSrc = "ntp_plans";
                        break;
                        case "subscription":
                            $DbSrc = "ntp_subscriptions";
                        break;
                        case "history":
                            $DbSrc = "ntp_history";
                        break;
                        default:
                            throw new \Exception('NTP recurring -> '.$section.' Connection problem!');
                    }
                break;
                case "sandbox":
                    switch ($section) {
                        case "plan":
                            $DbSrc = "ntp_plans";
                        break;
                        case "subscription":
                            $DbSrc = "ntp_subscriptions_sandbox";
                        break;
                        case "history":
                            $DbSrc = "ntp_history_sandbox";
                        break;
                        default:
                            throw new \Exception('NTP recurring -> '.$section.' Connection problem!');
                    }
                break;
                default:
                    throw new \Exception('NTP recurring Connection problem!');
            }
            return $DbSrc;
        }
    }    
?>