<?php
class recurringAdmin extends recurring {
    function getSubscriptionListLive(){
        $url = BASE_URL_RECURRING_API.'subscription/list';
        $data = array(
            'Signature' => self::getSignature()
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getSubscriptionList(){
        global $wpdb;
        /**
         * Just Template 
         * SELECT s.id, s.Subscription_Id, s.First_Name, s.Last_Name, s.Email, s.Tel, s.UserID, p.Title, p.Amount, s.StartDate, s.status FROM wp_ntp_subscriptions as s   INNER JOIN wp_ntp_plans as p WHERE s.PlanId = p.Plan_Id
         */
        // $subscriptions = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix . "ntp_subscriptions WHERE 1 ORDER BY `CreatedAt` DESC", "ARRAY_A");
        $subscriptions = $wpdb->get_results("SELECT s.id,
                                                    s.Subscription_Id,
                                                    s.First_Name,
                                                    s.Last_Name,
                                                    s.Email,
                                                    s.Tel,
                                                    s.UserID,
                                                    p.Title,
                                                    p.Amount,
                                                    s.StartDate,
                                                    s.status
                                                FROM  ".$wpdb->prefix . "ntp_subscriptions  as s 
                                                INNER JOIN wp_ntp_plans as p 
                                                WHERE s.PlanId = p.Plan_Id 
                                                ORDER BY s.CreatedAt DESC", "ARRAY_A");
        if(count($subscriptions)) {
            $errorCode = "00";
            $errorMsg = "";
        } else {
            $errorCode = "11";
            $errorMsg = __('There is no any subscription, yet','ntpRp');
        }
    
        $resultData = array(
            "code" => $errorCode,
            "message" => $errorMsg,
            "members" => $subscriptions
            );
        return $resultData;
    }

    function getSubscriptionInfinite(){
        global $wpdb;
        /**
         * Just Template 
         * SELECT s.id, s.Subscription_Id, s.First_Name, s.Last_Name, s.Email, s.Tel, s.UserID, p.Title, p.Amount, s.StartDate, s.status FROM wp_ntp_subscriptions as s   INNER JOIN wp_ntp_plans as p WHERE s.PlanId = p.Plan_Id
         */
        // $subscriptions = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix . "ntp_subscriptions WHERE 1 ORDER BY `CreatedAt` DESC", "ARRAY_A");
        $subscriptions = $wpdb->get_results("SELECT s.id,
                                                    s.Subscription_Id,
                                                    s.First_Name,
                                                    s.Last_Name,
                                                    s.Email,
                                                    s.Tel,
                                                    s.UserID,
                                                    p.Title,
                                                    p.Amount,
                                                    s.StartDate,
                                                    s.status
                                                FROM  ".$wpdb->prefix . "ntp_subscriptions  as s 
                                                INNER JOIN wp_ntp_plans as p 
                                                WHERE s.PlanId = p.Plan_Id 
                                                ORDER BY s.CreatedAt DESC", "ARRAY_A");
        if(count($subscriptions)) {
            $errorCode = "00";
            $errorMsg = "";
        } else {
            $errorCode = "11";
            $errorMsg = __('There is no any subscription, yet','ntpRp');
        }
    
        $resultData = array(
            "code" => $errorCode,
            "message" => $errorMsg,
            "members" => $subscriptions
            );
        return $resultData;
    }

    function getPlanListLive(){
        $url = BASE_URL_RECURRING_API.'plan/list';
        $data = array(
            'Signature' => self::getSignature(),
            "PlanStatus" => "Active"
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getPlanList(){
        global $wpdb;
        $plans = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix . "ntp_plans WHERE `Status` = 1 ORDER BY `CreatedAt` DESC", "ARRAY_A");
        if(count($plans)) {
            $errorCode = "00";
            $errorMsg = "";
        } else {
            $errorCode = "11";
            $errorMsg = __('There is no any Plan, yet','ntpRp');
        }
    
        $resultData = array(
            "code" => $errorCode,
            "message" => $errorMsg,
            "plans" => $plans
            );
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

    function delPlan($formData){
        $url = BASE_URL_RECURRING_API.'plan/delete'; 
        $planData = array(
            "PlanId" => $formData['PlanId']+0,
            "Signature" => self::getSignature(),
            "Unsubscribe" => $formData['Unsubscribe']
        );
    
        $postData = json_encode($planData);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function editPlan($formData){
        $url = BASE_URL_RECURRING_API.'plan/update'; 
        $planData = array(
            "PlanId" => $formData['PlanId'], 
            "Signature" => self::getSignature(),
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
            "Taxable"=> false ,
            "TermAndConditionAccepted" => $formData['TermAndConditionAccepted']
        );

        $postData = json_encode($planData);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }
}

function recurring_addPlan() {
    global $wpdb;

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
    
    // Add subscription to DB 
    if($jsonResultData['code'] === "00") {
        $wpdb->insert( 
            $wpdb->prefix . "ntp_plans", 
            array( 
                'Plan_Id'         => $jsonResultData['data']['planId'],
                'Title'           => $jsonResultData['data']['Title'],
                'Amount'          => $_POST['Amount'],
                'Currency'        => $_POST['Currency'],
                'Description'     => $_POST['planDescription'],
                'Recurrence_Type' => $_POST['RecurrenceType'],
                'Frequency_Type'  => $_POST['FrequencyType'],
                'Frequency_Value' => $_POST['FrequencyValue'],
                'Grace_Period'    => $_POST['GracePeriod'],
                'Initial_Paymen'  => $_POST['InitialPayment'] === 'true' ? true : false,
                'Status'          => $jsonResultData['data']['Status'],
                'CreatedAt'       => date("Y-m-d"),
                'UpdatedAt'       => date("Y-m-d")
            )
        );
    }

    $mySimulatedResult = array(
            'status'=> $jsonResultData['code'] === "00" ? true : false,
            'msg'=> $jsonResultData['message'],
            );
    echo json_encode($mySimulatedResult);
    die();
}
add_action('wp_ajax_addPlan', 'recurring_addPlan');


function recurring_delPlan() {
    global $wpdb;
    $a = new recurringAdmin();
    $planData = array(
            "PlanId" => $_POST['planId']+0,
            "Unsubscribe" => $_POST['unsubscribe'] === 'true' ? true : false
    );

    $jsonResultData = $a->delPlan($planData);
    
    /** To delete a plan */
    if($jsonResultData['code'] === "00") {
        $wpdb->update( 
            $wpdb->prefix . "ntp_plans", 
            array( 
                "Status"             => 2,
                'UpdatedAt'         => date("Y-m-d")
            ),
            array(
                'Plan_Id' => $_POST['planId']+0
            )
        );
    }

    $mySimulatedResult = array(
            'status'=> $jsonResultData['code'] === "00" ? true : false,
            'msg'=> $jsonResultData['message'],
            );
    echo json_encode($mySimulatedResult);
    die();
}
add_action('wp_ajax_delPlan', 'recurring_delPlan');

function recurring_editPlan() {
    global $wpdb;

    $a = new recurringAdmin();
    $planData = array(
            "PlanId" => $_POST['planId']+0,
            "Title" => $_POST['planTitile'],
            "RecurrenceType" =>  $_POST['RecurrenceType'],
            "Frequency" => array (
                "Type" => $_POST['FrequencyType'],
                "Value" => $_POST['FrequencyValue']+0
            ),
            "Description" => $_POST['planDescription'],
            "GracePeriod" => $_POST['GracePeriod']+0,
            "Amount" => $_POST['Amount']+0 ,
            "Currency" => $_POST['Currency'],
            "InitialPayment" => $_POST['InitialPayment'] === 'true' ? true : false,
            "TermAndConditionAccepted" => $_POST['TermAndConditionAccepted'] === 'true' ? true : false
    );

    $jsonResultData = $a->editPlan($planData);

    /** To Update the plan locally as well */
    if($jsonResultData['code'] === "00") {
        $wpdb->update( 
            $wpdb->prefix . "ntp_plans", 
            array( 
                "Title"             => $_POST['planTitile'],
                "Description"       => $_POST['planDescription'],
                "Amount"            => $_POST['Amount']+0 ,
                "Recurrence_Type"   => $_POST['RecurrenceType'],
                "Frequency_Type"    => $_POST['FrequencyType'],
                "Frequency_Value"   => $_POST['FrequencyValue']+0,
                "Grace_Period"      => $_POST['GracePeriod']+0,
                "Initial_Paymen"    => $_POST['InitialPayment'] === 'true' ? true : false,
                'UpdatedAt'         => date("Y-m-d")
            ),
            array(
                'Plan_Id' => $_POST['planId']+0
            )
        );
    }

    
    $mySimulatedResult = array(
            'status'=> $jsonResultData['code'] === "00" ? true : false,
            'msg'=> $jsonResultData['message'],
            'data'=> $planData,
            );
    echo json_encode($mySimulatedResult);
    die();
}
add_action('wp_ajax_editPlan', 'recurring_editPlan');

function getPlanInfo() {
    $planId = $_POST['planIdentity'];

    $planObj = new recurringFront();
    $arrayData = $planObj->getPlan($planId);

    if(isset($arrayData['code']) && ($arrayData['code'] == 11 || $arrayData['code'] == 12)) {
        $status = false;
        $planData = array();
    } else {
        $status = true;
        $plan = $arrayData['plan'];
        $planData = array(
            "Title" => $plan['Title'],
            "Description" => $plan['Description'],
            "Amount" => $plan['Amount'],
            "Currency" => $plan['Currency'],
            "RecurrenceType" => $plan['RecurrenceType'],
            "Frequency" => array (
                "Type" => $plan['Frequency']['Type'],
                "Value" => $plan['Frequency']['Value']
            ),
            "GracePeriod" => $plan['GracePeriod'],
            "InitialPayment" => $plan['InitialPayment'],
            "Status" => $plan['Status']
        );
    }

    echo json_encode(array(
        'status' => $status,
        'data'  => $planData
    ));
    die();
}

add_action('wp_ajax_getPlanInfo', 'getPlanInfo');

function getInfinitSubscribtion() {
    $start = $_POST['start']; 
    $limit = $_POST['limit']; 
    global $wpdb;
        /**
         * Query by pagination 
         * 
         * 	{ "data": "First_Name" },
		 * 	{ "data": "Last_Name" },
		 * 	{ "data": "Email" },
		 * 	{ "data": "Tel" },
		 * 	{ "data": "UserID" },
		 * 	{ "data": "Title" },
		 * 	{ "data": "Amount" },
		 * 	{ "data": "Status" },
		 * 	{ "data": "StartDate" },
		 * 	{ "data": "Subscription_Id" }
         * 
         * 
         */
        $subscriptions = $wpdb->get_results("SELECT s.id,
                                                    s.First_Name,
                                                    s.Last_Name,
                                                    s.Email,
                                                    s.Tel,
                                                    s.UserID,
                                                    p.Title,
                                                    p.Amount,
                                                    s.Status,
                                                    s.StartDate,
                                                    s.Subscription_Id
                                                FROM  ".$wpdb->prefix . "ntp_subscriptions  as s 
                                                INNER JOIN wp_ntp_plans as p WHERE s.PlanId = p.Plan_Id 
                                                ORDER BY s.id DESC LIMIT ".$start.",".$limit, "ARRAY_A");

        $subscriptionsTotalCount = $wpdb->get_results("SELECT count(*) as count FROM  ".$wpdb->prefix . "ntp_subscriptions", "ARRAY_A");

    $resultData = array (
        'recordsTotal' => $subscriptionsTotalCount[0]['count'],
        'data' => $subscriptions
      );

    echo json_encode($resultData);
    die();
}

add_action('wp_ajax_getInfinitSubscribtion', 'getInfinitSubscribtion');



function getSubscribtionCount() {
    global $wpdb;
        $subscriptionsTotalCount = $wpdb->get_results("SELECT count(*) as count FROM  ".$wpdb->prefix . "ntp_subscriptions WHERE 1 ", "ARRAY_A");
        echo ($subscriptionsTotalCount[0]['count']);
        die();
}


add_action('wp_ajax_getSubscribtionCount', 'getSubscribtionCount');

?>