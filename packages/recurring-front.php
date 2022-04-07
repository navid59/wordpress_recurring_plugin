<?php
    class recurringFront extends recurring {
        function getPlan($planId){
            $url = self::getApiUrl('plan/status');
            $data = array(
                'Signature' => self::getSignature(),
                "PlanId" => $planId+0
            );
        
            $postData = json_encode($data);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }

        function setSubscription($formData){
            $url = self::getApiUrl('subscription'); 
            $postData = json_encode($formData);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }

        function setUnsubscription($formData){
            $url = self::getApiUrl('subscription/cancel'); 
            $postData = json_encode($formData);
        
            $resultData = self::getData($url, $postData);
            return $resultData;
        }
    }
?>