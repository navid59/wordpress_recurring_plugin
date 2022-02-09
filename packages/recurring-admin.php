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

    function setPlan($formData){
        $url = BASE_URL_RECURRING_API.'plan'; 
        $planData = array(
            "MerchantSignature" => self::getSignature(),
            "Title" => $formData['Title'],
            "RecurrenceType" => $formData['RecurrenceType'],
            "Frequency" => [
                "Type" => $formData['Frequency']['Type'],
                "Value" => $formData['Frequency']['Value']+0 // Is added to ZERO to have result as INT
            ],
            "Description"=> $formData['Description'],
            "GracePeriod"=> $formData['GracePeriod']+0, // Is added to ZERO to have result as INT
            "Amount"=> $formData['Amount']+0, // Is added to ZERO to have result as FLOAT
            "Currency"=> $formData['Currency'],
            "InitialPayment" => $formData['InitialPayment'],
            "Taxable"=> false 
        );

        $postData = json_encode($planData);
    
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

function recurring_addPlan() {
    $a = new recurringAdmin();
    $planData = array(
        "Title" => $_POST['planTitile'],
        "RecurrenceType" =>  $_POST['RecurrenceType'],
        "Frequency" => array (
            "Type" => $_POST['FrequencyType'],
            "Value" => $_POST['FrequencyValue']
        ),
        "Description" => $_POST['planDescription'],
        "GracePeriod" => $_POST['GracePeriod'],
        "Amount" => $_POST['Amount'] ,
        "Currency" => $_POST['Currency'],
        "InitialPayment" => $_POST['InitialPayment'] === 'true' ? true : false
    );

    $jsonResultData = $a->setPlan($planData);
    
    $mySimulatedResult = array(
            'status'=> $jsonResultData['code'] === "00" ? true : false,
            'msg'=> $jsonResultData['message'],
            );
    echo json_encode($mySimulatedResult);
    die();
}
add_action('wp_ajax_addPlan', 'recurring_addPlan');
?>