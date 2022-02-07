<?php
class recurringAdmin extends recurring {
    function getSubscriptionList(){
        $url = BASE_URL_RECURRING_API.'subscription/list';
        $data = array(
            'Signature' => self::getSignature()
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getPlanList(){
        $url = BASE_URL_RECURRING_API.'plan/list';
        $data = array(
            'Signature' => self::getSignature(),
            "PlanStatus" => "All"
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getReportList(){
        $url = BASE_URL_RECURRING_API.'payment/list';
        $data = array(
            'Signature' => self::getSignature(),
            "PaymentStatus" => "All"
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }
}
?>