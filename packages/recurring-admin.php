<?php
class recurringAdmin extends recurring {
    function getSubscriptionList(){
        $url = BASE_URL_RECURRING_API.'subscription/list';
        $data = array(
            'Signature' => self::getSignature()
        );
    
        $postdata = json_encode($data);
    
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','token: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJBQUFBLUFBQUEtQUFBQS1BQUFBLUFBQUEiLCJhdXRob3JpemVkIjp0cnVlLCJjbGllbnQiOiJOYXZpZCBUb3JhYmF6YXJpIiwiZXhwIjoxNjQ0MDE2OTYxfQ.Lmlztx3BZHvAynv4cSrGXlSTitjJlYVDZTCQokDObZA'));
        $result = curl_exec($ch);
        $jsonDate = $result;
        $arrayData = json_decode($jsonDate, true);
    
        return $arrayData;
    }
}

?>