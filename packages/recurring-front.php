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
    }
?>