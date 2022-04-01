<?php
    class recurring {
        protected $slug = 'netopia_recurring';

        function getSignature() {
            return get_option($this->slug.'_signature', array());
        }

        function getApiKey() {
            return get_option($this->slug.'_api_key', array());
        }

        function getNotifyUrl() {
            return get_site_url()."/".get_option($this->slug.'_notify_url', array());
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
                            $statusStr = __('Subscribed','ntpRp');
                            break;
                        case '2':
                            $statusStr = __('Unsubscribed','ntpRp');
                            break;
                        default:
                            $statusStr = $statusCode;
                    }
                break;
                case 'report':
                    switch ($statusCode) {
                        case '3':
                            $statusStr = 'Paid';
                            break;
                        case '12':
                            $statusStr = 'Not paid';
                            break;
                        case '15':
                            $statusStr = 'Authorizing';
                            break;
                        default:
                            $statusStr = $statusCode;
                    }
                break;
            }
            return $statusStr;
         }
    }    
?>