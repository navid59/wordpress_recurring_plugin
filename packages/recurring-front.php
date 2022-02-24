<?php
    class recurringFront extends recurring {
        function getPlan($planId){
            $url = BASE_URL_RECURRING_API.'plan/status';
            $data = array(
                'Signature' => self::getSignature(),
                "PlanId" => $planId+0
            );
        
            $postData = json_encode($data);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }

        function setSubscription($formData){
            $url = BASE_URL_RECURRING_API.'subscription'; 
            $postData = json_encode($formData);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }

        function setUnsubscription($formData){
            $url = BASE_URL_RECURRING_API.'subscription/cancel'; 
            $postData = json_encode($formData);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }
    }
?>