<?php
class recurringAdmin extends recurring {
    function getSubscriptionListLive(){
        $url = self::getApiUrl('subscription/list');
        $data = array(
            'Signature' => self::getSignature()
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    /**
     * Get subscription list
     * Use it at Datatable in admin
     */
    // function getSubscriptionList(){
    //     die('Important!! Not Need, Will Delete');
    //     global $wpdb;
    //     // $subscriptions = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix . "ntp_subscriptions WHERE 1 ORDER BY `CreatedAt` DESC", "ARRAY_A");
    //     $subscriptions = $wpdb->get_results("SELECT s.id,
    //                                                 s.Subscription_Id,
    //                                                 s.First_Name,
    //                                                 s.Last_Name,
    //                                                 s.Email,
    //                                                 s.Tel,
    //                                                 s.UserID,
    //                                                 p.Title,
    //                                                 p.Amount,
    //                                                 s.StartDate,
    //                                                 s.status
    //                                             FROM  ".$wpdb->prefix . "ntp_subscriptions  as s 
    //                                             INNER JOIN ".$wpdb->prefix . "ntp_plans as p 
    //                                             WHERE s.PlanId = p.PlanId 
    //                                             GROUP BY s.UserID 
    //                                             ORDER BY s.CreatedAt DESC", "ARRAY_A");
    //     if(count($subscriptions)) {
    //         $errorCode = "00";
    //         $errorMsg = "";
    //     } else {
    //         $errorCode = "11";
    //         $errorMsg = __('There is no any subscription, yet','ntpRp');
    //     }
    
    //     $resultData = array(
    //         "code" => $errorCode,
    //         "message" => $errorMsg,
    //         "members" => $subscriptions
    //         );
    //     return $resultData;
    // }

    // function getSubscriptionInfinite(){
    //     die('Important!! Not Need, Will Delete');
    //     global $wpdb;
        
    //     $subscriptions = $wpdb->get_results("SELECT s.id,
    //                                                 s.Subscription_Id,
    //                                                 s.First_Name,
    //                                                 s.Last_Name,
    //                                                 s.Email,
    //                                                 s.Tel,
    //                                                 s.UserID,
    //                                                 p.Title,
    //                                                 p.Amount,
    //                                                 s.StartDate,
    //                                                 s.status
    //                                             FROM  ".$wpdb->prefix . "ntp_subscriptions  as s 
    //                                             INNER JOIN ".$wpdb->prefix . "ntp_plans as p 
    //                                             WHERE s.PlanId = p.PlanId 
    //                                             GROUP BY s.UserID 
    //                                             ORDER BY s.CreatedAt DESC", "ARRAY_A");
    //     if(count($subscriptions)) {
    //         $errorCode = "00";
    //         $errorMsg = "";
    //     } else {
    //         $errorCode = "11";
    //         $errorMsg = __('There is no any subscription, yet','ntpRp');
    //     }
    
    //     $resultData = array(
    //         "code" => $errorCode,
    //         "message" => $errorMsg,
    //         "members" => $subscriptions
    //         );
    //     return $resultData;
    // }

    function getPlanListLive(){
        $url = self::getApiUrl('plan/list');
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
        $obj = new recurringAdmin();

        $plans = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix . $obj->getDbSourceName('plan')." WHERE `Status` = 1 ORDER BY `CreatedAt` DESC", "ARRAY_A");
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
        $url = self::getApiUrl('plan'); 
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

    /**
     * Get data Direct From API - Server
     */
    function getReportListLive(){
        $url = self::getApiUrl('payment/list');
        $data = array(
            'Signature' => self::getSignature(),
            "PaymentStatus" => "All"
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getReportList(){
        global $wpdb;
        $obj = new recurringAdmin();

        $plans = $wpdb->get_results("SELECT 
                                    h.id,
                                    s.UserId,
                                    h.Subscription_Id,
                                    h.TransactionID,
                                    h.Comment,
                                    h.Status,
                                    h.CreatedAt,
                                    p.Title,
                                    p.Amount
                                    FROM  ".$wpdb->prefix . $obj->getDbSourceName('history')." as h 
                                    INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('subscription')." as s
                                    ON h.Subscription_Id = s.Subscription_Id 
                                    INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('plan')." as p
                                    ON s.PlanId = p.PlanId
                                    ORDER BY `CreatedAt` DESC", "ARRAY_A");
        if(count($plans)) {
            $errorCode = "00";
            $errorMsg = "";
        } else {
            $errorCode = "11";
            $errorMsg = __('There is no any payment report, yet','ntpRp');
        }
    
        $resultData = array(
            "code" => $errorCode,
            "message" => $errorMsg,
            "report" => $plans
            );
        return $resultData;
    }




    function delPlan($formData){
        $url = self::getApiUrl('plan/delete'); 
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
        $url = self::getApiUrl('plan/update'); 
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

    function getNextPayment($formData){
        $url = self::getApiUrl('schedule/payment');
        $data = array(
            'Signature' => self::getSignature(),
            "SubscriptionId" => $formData['SubscriptionId']
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }

    function getLastPayment($subscriptionId) {
        global $wpdb;
        $obj = new recurringAdmin();

        /**Get last payment of user for specific plan */
        $lastPayment = $wpdb->get_results("SELECT 
                                            * 
                                        FROM `".$wpdb->prefix.$obj->getDbSourceName('history')."` 
                                        WHERE 
                                            Subscription_Id = $subscriptionId 
                                            AND 
                                            status = '00' 
                                        ORDER BY CreatedAt DESC 
                                        LIMIT 1", "ARRAY_A");
        if(count($lastPayment)) {
            $date = new DateTime($lastPayment[0]['CreatedAt']);
            return $date->format('Y-m-d');
        } else {
            return '-';
        }
    }

    /**
    * Verify Credential Data
    */
    function isValidCredentialData($apiKey, $signature ){
        $url = self::getApiUrl('validate');
        $data = array(
            "ApiKey" => $apiKey,
            'Signature' => $signature
        );
    
        $postData = json_encode($data);
    
        $resultData = self::getData($url, $postData);
        return $resultData;
    }
}

function recurring_addPlan() {
    global $wpdb;

    $obj = new recurringAdmin();

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
    
        $jsonResultData = $obj->setPlan($planData);
        
        // Add subscription to DB 
        if($jsonResultData['code'] === "00") { 
            // Api response
            $status = true;
            $msg = $jsonResultData['message'];
    
            $dbInsertResult  = $wpdb->insert(
                $wpdb->prefix . $obj->getDbSourceName('plan') , 
                array( 
                    'PlanId'         => $jsonResultData['data']['planId'],
                    'Title'           => $jsonResultData['data']['Title'],
                    'Amount'          => $_POST['Amount'],
                    'Currency'        => $_POST['Currency'],
                    'Description'     => $_POST['planDescription'],
                    'Recurrence_Type' => $_POST['RecurrenceType'],
                    'Frequency_Type'  => $_POST['FrequencyType'],
                    'Frequency_Value' => $_POST['FrequencyValue'],
                    'Grace_Period'    => $_POST['GracePeriod'],
                    'Initial_Payment'  => $_POST['InitialPayment'] == 'true' ? 'true' : 'false',
                    'Status'          => $jsonResultData['data']['Status'],
                    'CreatedAt'       => date("Y-m-d"),
                    'UpdatedAt'       => date("Y-m-d")
                )
            );
    
            if($dbInsertResult) {
                // local response
                $status = true;
                $msg = $msg.__(' is ready to use.');
            } else {
                 // local response
                 $status = false;
                 $msg = __('Plan added in Recurring API Successfully! But is not added in your system successfully');
            }
    
        } else {
            // Api response
            $status = false;
            $msg = $jsonResultData['message'];
        }



    $addPlanResult = array(
            'status'=> $status,
            'msg'=> $msg,
            );
    echo json_encode($addPlanResult);
    die();
}
add_action('wp_ajax_addPlan', 'recurring_addPlan');


function recurring_delPlan() {
    global $wpdb;
    $obj = new recurringAdmin();
    $planData = array(
            "PlanId" => $_POST['planId']+0,
            "Unsubscribe" => $_POST['unsubscribe'] === 'true' ? true : false
    );

    $jsonResultData = $obj->delPlan($planData);
    
    /** To delete a plan */
    if($jsonResultData['code'] === "00") {
        $wpdb->update( 
            $wpdb->prefix . $obj->getDbSourceName('plan') , 
            array( 
                "Status"             => 2,
                'UpdatedAt'         => date("Y-m-d")
            ),
            array(
                'PlanId' => $_POST['planId']+0
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

    $obj = new recurringAdmin();
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

    $jsonResultData = $obj->editPlan($planData);

    /** To Update the plan locally as well */
    if($jsonResultData['code'] === "00") {
         // Api response
         $status = true;
         $msg = $jsonResultData['message'];
    } else {
        // Api response
        $status = false;
        $msg = $jsonResultData['message'];
    }
    
    if($status) {
        // Update Local 
        $updatePlan = $wpdb->update( 
            $wpdb->prefix . $obj->getDbSourceName('plan'), 
            array( 
                "Title"             => $_POST['planTitile'],
                "Description"       => $_POST['planDescription'],
                "Amount"            => $_POST['Amount']+0 ,
                "Recurrence_Type"   => $_POST['RecurrenceType'],
                "Frequency_Type"    => $_POST['FrequencyType'],
                "Frequency_Value"   => $_POST['FrequencyValue']+0,
                "Grace_Period"      => $_POST['GracePeriod']+0,
                "Initial_Payment"    => $_POST['InitialPayment'] == 'true' ? 'true' : 'false',
                'UpdatedAt'         => date("Y-m-d")
            ),
            array(
                'PlanId' => $_POST['planId']+0
            )
        );

        if($updatePlan) {
            $status = true;
            $msg = $msg.__(' ,ready to use it.','ntpRp');
        } else {
            $status = false;
            $msg = $msg.__(' ,Local data is not updated!.','ntpRp');
        }
    }
    
    $mySimulatedResult = array(
            'status'=> $status,
            'msg'=> $msg,
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

    if(isset($arrayData['code']) && ($arrayData['code'] == 11 || $arrayData['code'] == 12) || is_null($arrayData)) {
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
    $obj = new recurringAdmin();
    $start = $_POST['start']; 
    $limit = $_POST['limit'];
    $maxInDT = 2;
    global $wpdb;
        /**
         * Query by pagination 
         * 
         * 	{ "data": "First_Name" },
		 * 	{ "data": "Last_Name" },
		 * 	{ "data": "Email" },
		 * 	{ "data": "Tel" },
		 * 	{ "data": "UserID" },
		 * 	{ "data": "PlanTitle" },
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
                                                    min(s.Status) as Status,
                                                    s.StartDate,
                                                    s.Subscription_Id,
                                                    COUNT(*) as planCounter,
                                                    JSON_ARRAYAGG(
                                                        json_object(
                                                            'name', p.Title,
                                                            'amount', p.Amount,
                                                            'currency', p.Currency
                                                        )
                                                    ) as PlanList
                                                FROM  ".$wpdb->prefix . $obj->getDbSourceName('subscription')." as s 
                                                INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('plan')." as p 
                                                WHERE s.PlanId = p.PlanId 
                                                GROUP BY UserID
                                                ORDER BY s.id DESC 
                                                LIMIT ".$start.",".$limit, "ARRAY_A");

        $subscriptionsTotalCount = $wpdb->get_results("SELECT count(*) as count FROM  (SELECT * FROM ".$wpdb->prefix.$obj->getDbSourceName('subscription')." GROUP BY UserID ) as ntp_s", "ARRAY_A");


        for ($i = 0 ; $i < count($subscriptions) ; $i++) {
            $subscriptions[$i]['Status'] = $obj->getStatusStr('subscription',$subscriptions[$i]['Status']);
            
            // Customize Plan info for Datatable
            $PlanList = json_decode($subscriptions[$i]['PlanList'], true);
            $planInfoStr = '';
            for($j=0; $j < $maxInDT ; $j++) {
                $planInfoStr .= $PlanList[$j]['name'].' , '.$PlanList[$j]['amount'].' '.$PlanList[$j]['currency'];
                if(count($PlanList) >= $maxInDT ) {
                    $planInfoStr .= '<br>';
                } else {
                    break;
                }
            }
            if(count($PlanList) > $maxInDT) {
                $planInfoStr.= __(' & more','ntpRp');
            }
            
            $subscriptions[$i]['PlanTitle'] = $planInfoStr;
            $subscriptions[$i]['StartDate'] = date('Y-m-d', strtotime($subscriptions[$i]['StartDate']));
            $subscriptions[$i]['Action'] = '
            <button type="button" class="btn btn-secondary" onclick="subscriptionHistory(\''.$subscriptions[$i]['UserID'].'\')" style="margin-right:5px;" title="'.__('Subscriber history','ntpRp').'"><i class="fa fa-history"></i></button>
            <button type="button" class="btn btn-success" onclick="subscriptionDetails(\''.$subscriptions[$i]['UserID'].'\')" style="margin-right:5px;" title="'.__('Subscriber Info','ntpRp').'"><i class="fa fa-info"></i></button>
            <span class="fa-stack fa-1x" data-count="'.$subscriptions[$i]['planCounter'].'" title="'.__('Total Nr of subscription','ntpRp').'">
                <i class="fa fa-circle fa-stack-2x"></i>
                <i class="fa fa-bell fa-stack-1x fa-inverse"></i>
            </span>';
        }
    
    $resultData = array (
        'recordsTotal' => $subscriptionsTotalCount[0]['count'],
        'data' => $subscriptions
      );

    echo json_encode($resultData);
    die();
}
add_action('wp_ajax_getInfinitSubscribtion', 'getInfinitSubscribtion');

function recurring_getNextPayment() {
    
    $a = new recurringAdmin();
    $nextPaymentData = array(
            "SubscriptionId" => $_POST['subscriptionId']+0
    );

    $jsonResultData = $a->getNextPayment($nextPaymentData);
    
    $mySimulatedResult = array(
            'status'=> isset($jsonResultData['code']) && $jsonResultData['code']!== "00" ? false : true,
            'msg'=> isset($jsonResultData['message']) ? $jsonResultData['message'] : '',
            'data' =>  $jsonResultData
            );
    echo json_encode($mySimulatedResult);
    die();
}
add_action('wp_ajax_getNextPayment', 'recurring_getNextPayment');

/** 
 * Get sunscriber info
 */
function recurring_getSubscriptionDetail(){
    global $wpdb;
    $subscriptionInternUserId = $_POST['userId'];
    $planList = array();
    $obj = new recurringAdmin();

    $subscription = $wpdb->get_results("SELECT s.id,
                                                s.Subscription_Id,
                                                s.First_Name,
                                                s.Last_Name,
                                                s.Email,
                                                s.Tel,
                                                s.UserID,
                                                s.Address,
                                                s.City,
                                                p.Title,
                                                p.Amount,
                                                s.StartDate,
                                                s.status
                                            FROM  ".$wpdb->prefix . $obj->getDbSourceName('subscription')." as s 
                                            INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('plan')." as p 
                                            ON s.PlanId = p.PlanId 
                                            WHERE s.userId = '$subscriptionInternUserId'
                                            LIMIT 1", "ARRAY_A");
    if(count($subscription)) {
        /**Get list of user's plans */
        $planList = $wpdb->get_results("SELECT 
                                            p.id,
                                            p.PlanId,
                                            p.Title,
                                            p.Amount,
                                            s.First_Name,
                                            s.Last_Name,
                                            s.Subscription_Id,
                                            s.StartDate,
                                            s.Status
                                        FROM `".$wpdb->prefix . $obj->getDbSourceName('plan')."` as p
                                        INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('subscription')." as s
                                        ON s.PlanId = p.PlanId
                                        WHERE s.UserID = '$subscriptionInternUserId'
                                        ORDER BY s.StartDate DESC", "ARRAY_A");

        // $obj = new recurringAdmin();
        for($i = 0; $i < count($planList); $i++) {
            $planList[$i]['Status'] = $obj->getStatusStr('plan', $planList[$i]['Status']);
            $planList[$i]['LastPayment'] = $obj->getLastPayment($planList[$i]['Subscription_Id']);
        }

        $errorCode = "00";
        $errorMsg = "";
    } else {
        $errorCode = "11";
        $errorMsg = __('Subscription is not found','ntpRp');
    }

    $resultData = array(
        "code" => $errorCode,
        "message" => $errorMsg,
        "data" => $subscription,
        "plans" => $planList
        );
    echo json_encode($resultData);
    die();
}
add_action('wp_ajax_getSubscriptionDetail', 'recurring_getSubscriptionDetail');

function recurring_getSubscriptionHistory() {
    global $wpdb;
    $userId = $_POST['userId'];
    $obj = new recurringAdmin();

    $userPaymentHistory = $wpdb->get_results("SELECT 
                                                s.UserId,
                                                h.Subscription_Id,
                                                h.TransactionID,
                                                h.Comment,
                                                h.Status,
                                                h.CreatedAt,
                                                p.Title,
                                                p.Amount
                                            FROM ".$wpdb->prefix . $obj->getDbSourceName('subscription')." as s
                                            INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('history')." as h
                                            ON h.Subscription_Id = s.Subscription_Id 
                                            INNER JOIN ".$wpdb->prefix . $obj->getDbSourceName('plan')." as p
                                            ON s.PlanId = p.PlanId
                                            WHERE s.UserId = '$userId'
                                            ORDER BY `CreatedAt` DESC", "ARRAY_A");

    // $obj = new recurringAdmin();
    for($i = 0; $i < count($userPaymentHistory); $i++) {
        $userPaymentHistory[$i]['Status'] = $obj->getStatusStr('report', $userPaymentHistory[$i]['Status']);
    }
    $resultData = array(
        "code" => "00",
        "message" => "",
        "histories" => $userPaymentHistory
        );
    echo json_encode($resultData);
    die();
}

add_action('wp_ajax_getSubscriptionHistory', 'recurring_getSubscriptionHistory');

function recurring_uploadKey() {
   
    $objCertificate = new certificate();
    $uploadeResult = $objCertificate->cerValidation();

    echo json_encode($uploadeResult);
}
add_action('wp_ajax_uploadKey', 'recurring_uploadKey');

function recurring_verifyCredentialData() {
    $obj = new recurringAdmin();
    $apiKey = $obj->getApiKey();
    $signature = $obj->getSignature();

    $jsonResultData = $obj->isValidCredentialData($apiKey, $signature);

    $validateCredentialResult = array(
        'code'=> $jsonResultData['code'],
        'msg'=> $jsonResultData['message'],
        );
    echo json_encode($validateCredentialResult);
    die();
}
add_action('wp_ajax_verifyCredentialData', 'recurring_verifyCredentialData');

?>