<?php
 add_action('wp_enqueue_scripts', 'enqueue_and_register_ntp_recurring_js_scripts');
 add_action( 'wp_enqueue_scripts', 'ntp_recurring_enqueue_scripts', 10 );

 add_action('wp_ajax_addNewSubscription', 'recurring_addSubscription');
 add_action('wp_ajax_nopriv_addNewSubscription', 'recurring_addSubscription');
 add_action('wp_ajax_updateSubscriberAccountDetails', 'recurring_updateSubscriberAccountDetails');
 add_action('wp_ajax_unsubscription', 'recurring_unsubscription');
 add_action('wp_ajax_getMySubscriptions', 'recurring_account_getMySubscriptions');
 add_action('wp_ajax_getMyHistory', 'recurring_account_getMyHistory');
 add_action('wp_ajax_getMyNextPayment', 'recurring_getMyNextPayment');
 add_action('wp_ajax_getMyAccountDetails', 'recurring_getMyAccountDetails');
 add_action('wp_ajax_logoutAccount', 'recurring_logoutAccount');

 function ntp_recurring_enqueue_scripts() {
    wp_enqueue_style( 'ntp_recurring_front_css_datatables', plugin_dir_url( __FILE__ ) . 'css/addons/datatables.min.css',array(),'2.0' ,false);

    wp_register_script( 'ntp_recurring_front_script_datatables', plugin_dir_url( __FILE__ ) . 'js/addons/datatables.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script_datatables' );
}

 function enqueue_and_register_ntp_recurring_js_scripts(){
    wp_enqueue_style( 'ntp_recurring_front_css', plugin_dir_url( __FILE__ ) . 'css/bootstrap/bootstrap.min.css',array(),'3.0' ,false);
    
    wp_register_script( 'ntp_recurring_script', plugin_dir_url( __FILE__ ) . 'js/bootstrap/bootstrap.bundle.min.js', array('jquery'), '1.1.0', true );
    wp_enqueue_script( 'ntp_recurring_script' ); 

    wp_register_script( 'ntp_recurring_3ds', plugin_dir_url( __FILE__ ) . 'js/3DS.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_3ds' ); 
 }

 function frontResource() {
    wp_enqueue_script('my-jquery',plugin_dir_url( __FILE__ ).'js/recurringFront.js', array('jquery'));
    wp_localize_script( 'my-jquery', 'frontAjax', array('ajax_url' => admin_url( 'admin-ajax.php' )));
    }

add_action('wp_enqueue_scripts', 'frontResource');


function recurring_addSubscription() {
    global $wpdb;

    $current_user = wp_get_current_user();  
    if($current_user->ID == 0) {
        $Member = array (
            "Name" => $_POST['Name'],
            "LastName" => $_POST['LastName'],
            "UserID" => $_POST['UserID'],
            "Pass" => $_POST['Pass'],
            "Email" => $_POST['Email'],
            "Address" => $_POST['Address'],
            "City" => $_POST['City'],
            "Tel" => strval($_POST['Tel'])
        );
    } else {
        $MemberDetails = $wpdb->get_results("SELECT *
                                    FROM  ".$wpdb->prefix . "ntp_subscriptions as s 
                                    WHERE s.UserID = '".$current_user->user_login."' 
                                    ORDER BY s.id DESC 
                                    LIMIT 1", "ARRAY_A");

        $Member = array (
            "Name" => $current_user->first_name,
            "LastName" => $current_user->last_name,
            "UserID" => $current_user->user_login,
            "Email" => $current_user->user_email,
            "Address" => !isset($MemberDetails[0]['Address']) ? NULL : $MemberDetails[0]['Address'],
            "City" => !isset($MemberDetails[0]['City']) ? NULL : $MemberDetails[0]['City'],
            "Tel" => !isset($MemberDetails[0]['Tel']) ? NULL : $MemberDetails[0]['Tel']
        );
    }
    

    /**
     *  Authenticate User
     *  If not exist will be create
     *  */
    authenticateUser($Member); 


    $obj3DS = json_decode(stripslashes($_POST['ThreeDS']));
    $arr3DS = (array)$obj3DS;
    

    $obj = new recurringFront();
    $subscriptionData = array(
        "Member" => array (
            "UserID" => $Member['UserID'],
            "Name" => $Member['Name'],
            "LastName" => $Member['LastName'],
            "Email" => $Member['Email'],
            "Address" => $Member['Address'],
            "City" => $Member['City'],
            "Tel" => strval($Member['Tel'])
        ),
        "Merchant" => array(
            "Signature" => $obj->getSignature(),
            "NotifyUrl" => $obj->getNotifyUrl(),
            "Tolerance" =>  true,
            "IntervalRetry" => 3
        ),
        "Plan" =>  array(
            "PlanId" => $_POST['PlanID']+0, 
            "StartDate" => date("Y-m-d")."T00:00:00-00:00",
            "EndDate" => ""
        ),
        "PaymentConfig" => array(
            "Instrument" => array (
                "Type" => "card",
                "Account" => strval($_POST['Account']),
                "ExpMonth" => $_POST['ExpMonth']+0,
                "ExpYear" => $_POST['ExpYear']+0,
                "SecretCode" => strval($_POST['SecretCode']),
                "Token" => ""
            ),
            "ThreeDS2" => $arr3DS
        )
      );   
     

    $jsonResultData = $obj->setSubscription($subscriptionData);

    // Add subscription to DB 
    if($jsonResultData['code'] === "00") {
        $arrSubscriptionData = array( 
                'Subscription_Id' => $jsonResultData['data']['subscriptionId'],
                'First_Name'      => $Member['Name'],
                'Last_Name'       => $Member['LastName'],
                'Email'           => $Member['Email'],
                'Tel'             => $Member['Tel'],
                'Address'         => $Member['Address'],
                'City'            => $Member['City'],
                'UserID'          => $Member['UserID'],
                'NextPaymentDate' => date("Y-m-d"),
                'PlanId'          => $_POST['PlanID'],
                'StartDate'       => date("Y-m-d"),
                'EndDate'         => "",
                'Status'          => 1, // Payment is success
                'CreatedAt'       => date("Y-m-d"),
                'UpdatedAt'       => date("Y-m-d")
            );
    } elseif ($jsonResultData['code'] === "19") {
        $arrSubscriptionData = array(
                'Subscription_Id' => $jsonResultData['data']['subscriptionId'],
                'First_Name'      => $_POST['Name'],
                'Last_Name'       => $_POST['LastName'],
                'Email'           => $_POST['Email'],
                'Tel'             => $_POST['Tel'],
                'Address'         => $_POST['Address'],
                'City'            => $_POST['City'],
                'UserID'          => $_POST['UserID'],
                'NextPaymentDate' => date("Y-m-d"),
                'PlanId'          => $_POST['PlanID'],
                'StartDate'       => date("Y-m-d"),
                'EndDate'         => "",
                'Status'          => 0, // Payament is faeiled
                'CreatedAt'       => date("Y-m-d"),
                'UpdatedAt'       => date("Y-m-d")
            );
    }

    // Add subscription to DB 
    $wpdb->insert( $wpdb->prefix . "ntp_subscriptions", $arrSubscriptionData );

    // Sned mail
    $obj->informMember("New subscription", "Congratulation you successfully subscribed");

    $mySimulatedResult = array(
        'status'=> $jsonResultData['code'] === "00" ? true : false,
        'msg'=> $jsonResultData['message'],
        );
    echo json_encode($mySimulatedResult);
    wp_die();
}

function recurring_getMyNextPayment() {
    
    $a = new recurringAdmin();
    $nextPaymentData = array(
            "SubscriptionId" => $_POST['subscriptionId']+0
    );

    $jsonResultData = $a->getNextPayment($nextPaymentData);
    
    $myNextPaymentResult = array(
            'status'=> isset($jsonResultData['code']) && $jsonResultData['code']!== "00" ? false : true,
            'msg'=> !empty($jsonResultData['message']) ? $jsonResultData['message'] : '',
            'data' =>  $jsonResultData
            );
    echo json_encode($myNextPaymentResult);
    die();
}

function recurring_unsubscription() {
    global $wpdb;
  
    $obj = new recurringFront();
    $subscriptionData = array(
            "Signature" => $obj->getSignature(),
            "SubscriptionId" => $_POST['SubscriptionId']+0
      );   
     
    $jsonResultData = $obj->setUnsubscription($subscriptionData);
    
    // Update subscription to DB 
    if($jsonResultData['code'] === "00") {
        $wpdb->update( 
            $wpdb->prefix . "ntp_subscriptions", 
            array( 
                'Status'          => 2,
                'UpdatedAt'       => date("Y-m-d")
            ),
            array(
                'id' => $_POST['Id'],
                'Subscription_Id' => $_POST['SubscriptionId']
            )
        );

        // Sned mail
        $obj->informMember("Unsubscription", "Hope to see you soon");

    }

    $unsubscribeResult = array(
        'status'=> $jsonResultData['code'] === "00" ? true : false,
        'msg'=> $jsonResultData['message'],
        );
    echo json_encode($unsubscribeResult);
    wp_die();
}

function recurring_logoutAccount() {
    wp_logout();
    
    $mySimulatedResult = array(
        'status'=> true,
        'msg'=> __('Logout with success','ntpRp'),
        'redirectUrl'=> get_home_url().'/subscription-account',
    );
    
    echo json_encode($mySimulatedResult);
    wp_die();
}

function recurring_getMyAccountDetails() {
    global $wpdb;

    /** Get Current user Info */
    $current_user = wp_get_current_user();
    // 1- get current user 
    // 2- know ID & User name
    $userName = $current_user->user_login;
    
    $MyDetails = $wpdb->get_results("SELECT *
                                    FROM  ".$wpdb->prefix . "ntp_subscriptions as s 
                                    WHERE s.UserID = '".$current_user->user_login."'", "ARRAY_A");

    $mySimulatedResult = array(
                                'status' => true,
                                'msg'    => '',
                                'data'   => '<div class="row" id="myAccountForm">
                                                <form class="needs-validation" novalidate>
                                                    <h4 class="mb-3">'.__('Personal information').'</h4>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <input type="hidden" class="form-control" id="SubscriptionId" placeholder="" value="'.$MyDetails[0]['Subscription_Id'].'" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="firstName">'.__('First name','ntpRp').'</label>
                                                            <input type="text" class="form-control" id="firstName" placeholder="" value="'.$current_user->first_name.'" required>
                                                            <div class="invalid-feedback">
                                                            Valid first name is required.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="lastName">'.__('Last name','ntpRp').'</label>
                                                            <input type="text" class="form-control" id="lastName" placeholder="" value="'.$current_user->last_name.'" required>
                                                            <div class="invalid-feedback">
                                                            Valid last name is required.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row" >
                                                        <div class="col-md-4 mb-3">
                                                            <label for="username">'.__('Username','ntpRp').'</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">@</span>
                                                                </div>
                                                                <input type="text" class="form-control" id="username" placeholder="Username" value="'.$current_user->user_login.'" required>
                                                                <div class="invalid-feedback" style="width: 100%;">
                                                                Your username is required.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="password">'.__('Password','ntpRp').'</label>
                                                            <input type="password" class="form-control" id="password" required>
                                                            <div class="invalid-feedback">
                                                                Please enter a valid password.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="email">'.__('Email','ntpRp').'</label>
                                                            <input type="email" class="form-control" id="email" placeholder="you@example.com" value="'.$current_user->user_email.'" required>
                                                            <div class="invalid-feedback">
                                                                Please enter a valid email address for shipping updates.
                                                            </div>
                                                        </div>
                                                    </div>
                                                
                                                    
                                                    <div class="mb-3">
                                                        <label for="address">'.__('Address','ntpRp').'</label>
                                                        <input type="text" class="form-control" id="address" placeholder="1234 Main St" value="'.$MyDetails[0]['Address'].'" required>
                                                        <div class="invalid-feedback">
                                                            Please enter your shipping address.
                                                        </div>
                                                    </div>
                                                
                                                    <div class="row">
                                                        <div class="col-md-5 mb-3">
                                                            <label for="tel">'.__('Tel','ntpRp').'</label>
                                                            <input type="text" class="form-control" id="tel" placeholder="" value="'.$MyDetails[0]['Tel'].'" required>
                                                            <div class="invalid-feedback">
                                                            Phone required.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <label for="country">'.__('Country','ntpRp').'</label>
                                                            <select class="custom-select d-block w-100" id="country" required>
                                                            <option value="">Choose...</option>
                                                            <option value="642" selected>Romania</option>
                                                            </select>
                                                            <div class="invalid-feedback">
                                                            Please select a valid country.
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="state">'.__('State','ntpRp').'</label>
                                                            <select class="custom-select d-block w-100" id="state" required>'
                                                            .getJudete($MyDetails[0]['City']).
                                                            '</select>
                                                            <div class="invalid-feedback">
                                                            Please provide a valid state.
                                                            </div>
                                                        </div>                        
                                                    </div>
                                                    <hr class="mb-4">
                                                    <button class="btn btn-primary btn-lg btn-block" type="button" id="" onclick="updateMyAccountDetails(); return false;" >'.__('Update', 'ntpRp').'</button>
                                                </form>
                                            </div>    
                                            <div class="jumbotron text-center alert alert-dismissible fade" id="msgBlock" role="alert">
                                                <h1 id="alertTitle" class="display-5"></h1>
                                                <p class="lead">
                                                    <strong>
                                                        <span id="msgContent"></span>
                                                    </strong>
                                                </p>
                                                <div id="myAccountGoToHome">
                                                    <hr>
                                                    <p class="lead">
                                                        <a class="btn btn-primary btn-sm" href="'.get_home_url().'" role="button">'.__('Continue to homepage','ntpRp').'</a>
                                                    </p>
                                                </div>
                                            </div>',
                                );

    echo json_encode($mySimulatedResult);
    wp_die();
}


function recurring_updateSubscriberAccountDetails() {
    global $wpdb;
    $msg = '';

    $subscriptionAccountDetails = array (
        "SubscriptionId" => $_POST['SubscriptionId'],
        "Name" => $_POST['Name'],
        "LastName" => $_POST['LastName'],
        "UserID" => $_POST['UserID'],
        "Pass" => $_POST['Pass'],
        "Email" => $_POST['Email'],
        "Address" => $_POST['Address'],
        "City" => $_POST['City'],
        "Tel" => strval($_POST['Tel'])
    );


    /**
     *  Authenticate User
     *  If user is that one who is already logined.
     *  if choeasen email is exist 
     *  */
    
    $current_user = wp_get_current_user();
    
    if($current_user->user_login != $subscriptionAccountDetails['UserID']) {
        $validateAuthResult = array(
            'status'=> false,
            'msg'=> __('You are not allowded to change Username','ntpRp'),
        );
        echo json_encode($validateAuthResult);
        wp_die();
    }

    if(!is_email($subscriptionAccountDetails['Email'])) {
        $validateEmailFormat = array(
            'status'=> false,
            'msg'=> __('The email address is not correct!', 'ntpRp'),
        );
        echo json_encode($validateEmailFormat);
        wp_die();
    }

    $validateChosenEmail = email_exists( $subscriptionAccountDetails['Email']);
    if($validateChosenEmail != false && $validateChosenEmail != $current_user->id) {
        $validateEmailResult = array(
            'status'=> false,
            'msg'=> __('The email is already exist', 'ntpRp'),
        );
        echo json_encode($validateEmailResult);
        wp_die();
    }

    if($subscriptionAccountDetails['Pass'] != "") {
        if(!isStrongPass($subscriptionAccountDetails['Pass'])) {
            $validatePassLenght = array(
                'status'=> false,
                'msg'=> __('The password is not a suitable password!','ntpRp'),
            );
            echo json_encode($validatePassLenght);
            wp_die();
        } else {
            /*
            * ChangePassword
            */
            $hash = wp_hash_password($subscriptionAccountDetails['Pass']);
            $passChangeStatus = $wpdb->update(
                $wpdb->prefix . "users",
                array(
                    'user_pass'           => $hash,
                    'user_activation_key' => '',
                ),
                array( 'ID' => $current_user->ID )
            );

            /* 
            * Clear cache of current user
            * Logout & Then Login 
            */ 
            if($passChangeStatus != false ) {
                clean_user_cache($current_user->ID);
                wp_clear_auth_cookie();
                wp_set_current_user($current_user->ID);
                wp_set_auth_cookie($current_user->ID, true, false);

                $user = get_user_by('id', $current_user->ID);
                update_user_caches($user);

                $msg = __('Password is changed . ','ntpRp');
            } else {
                $msg = __('Password is not changed . ','ntpRp');
            }
        }
    }
    
   
    /*
    * First SHOULD Update the subscriber info on Server by API
    * Then update the local data
    * BUT Temporary, just update local data
    */
    
    $updateResult = $wpdb->update( 
                        $wpdb->prefix . "ntp_subscriptions", 
                        array( 
                            'First_Name'      => $subscriptionAccountDetails['Name'],
                            'Last_Name'       => $subscriptionAccountDetails['LastName'],
                            'Email'           => $subscriptionAccountDetails['Email'],
                            'Address'         => $subscriptionAccountDetails['Address'],
                            'City'            => $subscriptionAccountDetails['City'],
                            'Tel'             => $subscriptionAccountDetails['Tel'],
                            'UpdatedAt'       => date("Y-m-d")
                        ),
                        array(
                            'UserID' => $subscriptionAccountDetails['UserID'] 
                        )
                    );
    
    if($updateResult != false) {
        update_user_meta( $current_user->id, "first_name",  $subscriptionAccountDetails['Name'] ) ;
        update_user_meta( $current_user->id, "last_name",  $subscriptionAccountDetails['LastName'] ) ;

        $args = array(
            'ID'         => $current_user->id,
            'user_email' => esc_attr( $subscriptionAccountDetails['Email'] )
        );
        wp_update_user( $args );

        
        $msg.=__( 'Data is updated successfully!','ntpRp');
        
    }

    $mySimulatedResult = array(
        'status'=> true,
        'msg'=> $msg
    );
    
    echo json_encode($mySimulatedResult);
    wp_die();
}

function recurring_account_getMyHistory() {
    global $wpdb;

    /** Get Current user Info */
    $current_user = wp_get_current_user();
    $htmlThem = 'Payment History';

    $userPaymentHistory = $wpdb->get_results("SELECT 
                                                    s.UserId,
                                                    h.Subscription_Id,
                                                    h.TransactionID,
                                                    h.Comment,
                                                    h.Status,
                                                    h.CreatedAt,
                                                    p.Title,
                                                    p.Amount
                                                FROM ".$wpdb->prefix . "ntp_subscriptions as s
                                                INNER JOIN ".$wpdb->prefix . "ntp_history as h
                                                ON h.Subscription_Id = s.Subscription_Id 
                                                INNER JOIN ".$wpdb->prefix . "ntp_plans as p
                                                ON s.PlanId = p.Plan_Id
                                                WHERE s.UserId = '$current_user->user_login'
                                                ORDER BY `CreatedAt` DESC", "ARRAY_A");

        $obj = new recurringAdmin();
        for($i = 0; $i < count($userPaymentHistory); $i++) {
            $userPaymentHistory[$i]['Status'] = $obj->getStatusStr('report', $userPaymentHistory[$i]['Status']);
        }
    
    $myHistoryResult = array(
        'status' => true,
        'msg'    => '',
        'data'   => '<div class="row">
                        <h3 class="card-title">'. __($htmlThem,'ntpRp').'<span id="who"></span></h3>
                        </div>
                        <div class="row">
                            <table id="myHistoryDataTable" class="table" width="100%">
                                <thead>
                                <tr>
                                    <th>'. __('Date','ntpRp').'</th>
                                    <th>'. __('Title & Amount','ntpRp').'</th>
                                    <!-- <th>'. __('Transaction ID','ntpRp').'</th> -->
                                    <!-- <th>'. __('Comment','ntpRp').'</th> -->
                                    <th>'. __('Status','ntpRp').'</th>
                                </tr>
                                </thead>
                                <tbody id="mySubscriberPaymentHistoryList">
                                    <!-- History List -->
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>'. __('Date','ntpRp').'</th>
                                    <th>'. __('Title & Amount','ntpRp').'</th>
                                    <!-- <th>'. __('Transaction ID','ntpRp').'</th> -->
                                    <!-- <th>'. __('Comment','ntpRp').'</th> -->
                                    <th>'. __('Status','ntpRp').'</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>',
        'histories'   => $userPaymentHistory,
        );

    echo json_encode($myHistoryResult);
    wp_die();
}

function recurring_account_getMySubscriptions() {
    global $wpdb;

    /** Get Current user Info */
    $current_user = wp_get_current_user();

    $myPlans = $wpdb->get_results("SELECT p.id,
                                          p.Plan_Id,
                                          p.Title,
                                          p.Amount,
                                          p.Currency,
                                          p.Description,
                                          p.Recurrence_Type,
                                          p.Frequency_Type,
                                          p.Frequency_Value,
                                          p.Grace_Period,
                                          p.Initial_Paymen,
                                          p.Status,
                                          s.id as userId,
                                          s.First_Name,
                                          s.Last_Name,
                                          s.Status,
                                          s.Subscription_Id
                                    FROM  ".$wpdb->prefix . "ntp_plans as p 
                                    LEFT JOIN ".$wpdb->prefix . "ntp_subscriptions as s 
                                    ON p.Plan_Id = s.PlanId 
                                    WHERE s.UserID = '".$current_user->user_login."' AND s.Status <> 2", "ARRAY_A");

    $htmlThem = '';
    if(count($myPlans)) {
        foreach($myPlans as $plan) {
            $htmlThem.= '<div class="col-sm-6 pb-2">
                            <div class="card">
                                <div class="card-body">
                                <h2 class="card-title">'.$plan['Title'].'</h2>
                                <h3 class="card-title">'.$plan['Amount'].' '.$plan['Currency'].'</h3>
                                <h4 class="card-title">'.$plan['Frequency_Type'].' / '.$plan['Frequency_Value'].'</h4>
                                <p class="card-text">'.$plan['Description'].'</p>
                                <button type="button" class="btn btn-primary unsubscriptionMyAccounButton" data-subscriptionId="'.$plan['Subscription_Id'].'" data-userId="'.$plan['userId'].'" data-planTitle="'.$plan['Title'].'" data-toggle="modal" data-target="#unsubscriptionMyAccountModal" >
                                    '.__('Unsubscription','ntpRp').'
                                </button>
                                <button type="button" class="btn btn-info" title="'.__('Check next payment','ntpRp').'" onclick="frontSubscriptionNextPayment('.$plan['Subscription_Id'].','.$plan['id'].',\''.$plan['Title'].'\')"><i class="fa fa-credit-card"></i></button>
                                </div>
                            </div>
                        </div>';
        }
    } else {
        $htmlThem = '<h4>'.__('You are not subscribe in any of our plans!','ntpRp').'</h4>';
        $htmlThem .= '<h5>'.__('Please, check them out!','ntpRp').'</h5>';
    }
    
    $frontNextPayment = '<!-- Modal -->
                        <div id="nextPaymentModal" class="modal fade" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                    <h2 class="modal-title" id="nextPaymentModalLabel">'. __('Payment Schedule','ntpRp') .'</h2>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                    <div id="">
                                        '. __('Next payment schedule for ', 'ntpRp').'
                                        <strong>
                                            <span id="subscriberName"></span>
                                        </strong>
                                    </div>        
                                    <div>
                                        <h5>'. __('Date', 'ntpRp') .' : <span id="nextPaymentDate"> - </span></h5>
                                        <h5>'. __('Status', 'ntpRp').' : <span id="nextPaymentStatus"> - </span></h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                    </div>
                                    <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                                        <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>';

    $frontModalUnsubscriptionHtml ='<!-- Modal -->
                                    <div class="modal fade" id="unsubscriptionMyAccountModal" tabindex="-1" aria-labelledby="unsubscriptionRecurringModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 15px 0px 0px;">
                                                    <h2 class="modal-title" id="unsubscriptionRecurringModalLabel">'.__('Unsubscription', 'ntpRp').'</h2>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">            
                                                    <div class="row">
                                                        <div class="col-md-12 order-md-1">
                                                            <form id="unsubscription-form" class="needs-validation" novalidate>
                                                                '.__('Are you sure to unsubscribe from ','ntpRp').'
                                                                <span id="PlanTitle" > - </span> !?
                                                                <br>
                                                                '.__('To unsubscribe click on unsubscribe button.','ntpRp').' '.__('Otherwise close the window','ntpRp').'
                                                                <hr>
                                                                <input type="hidden" class="form-control" id="Id" value="" readonly>
                                                                <input type="hidden" class="form-control" id="Subscription_Id" value="" readonly>
                                                                <button id="unsubscriptionButton" class="btn btn-secondary" type="button" onclick="unsubscriptionMyAccount(); return false;">Unsubscribe</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div id="loading" class="d-flex align-items-center fade">
                                                        <strong>'.__('Loading...','ntpRp').'</strong>
                                                        <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                                                    </div>
                                                    <div class="alert alert-dismissible fade" id="myAccountMsgBlock" role="alert">
                                                        <strong id="myAccountAlertTitle">!</strong> <span id="myAccountMsgContent"></span>.
                                                    </div>                                
                                                </div>
                                                <div class="modal-footer">
                                                    '.__('Supported by NETOPIA Payments').'
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    ';

    $frontModalAddSubscriptionHtml = '';
                                        
    $mySimulatedResult = array(
        'status' => true,
        'msg'    => '',
        'data'   => '<div class="row">
                        '.$htmlThem.'
                    </div>
                    <div class="row">
                    '.$frontNextPayment.'
                    '.$frontModalUnsubscriptionHtml.'
                    '.$frontModalAddSubscriptionHtml.'
                    </div>',
        );


    echo json_encode($mySimulatedResult);
    wp_die();
}


function assignToRecurring ($data) {
        $title  = isset($data['title']) && $data['title'] !== null ? $data['title'] : null;
        $button = isset($data['button']) && $data['button'] !== null ? $data['button'] : null;
        $planId = isset($data['plan_id']) && $data['plan_id'] !== null ? $data['plan_id'] : null;
        if(!is_null($planId)) {
            $str = recurringModal ($planId, $button, $title);
        } else {
            $str = ''; 
        } 
    return $str;
}


function ntpMyAccount() {
    $obj = new recurringFront();
    $accountPageContent = $obj->getAccountPageSetting();

    if(is_user_logged_in()) {
        $strHTML = '
                    <div class="">
                        <div class="row">
                            <div class="" id="">
                                <ul class="nav nav-pills nav-flush flex-column bg-light">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link border-bottom" id="frontAccountMysubscription" ><i class="fa fa-bell" style="padding-right:15px;"></i> '.__('My subscriptions', 'ntpRp').'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link border-bottom" id="frontAccountMyPaymentHistory" ><i class="fas fa-file-invoice-dollar" style="padding-right:15px;"></i> '.__('My History', 'ntpRp').'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link border-bottom" id="frontAccountDetails" ><i class="fa fa-user-circle" style="padding-right:15px;"></i> '.__('Account details', 'ntpRp').'</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link border-bottom" id="frontAccountLogout" ><i class="fas fa-sign-out-alt" style="padding-right:15px;"></i> '.__('Logout', 'ntpRp').'</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col" id="ntpAccount">
                                <h2 id="ntpAccountSubtitle">'.$accountPageContent['subtitle'].'</h2>
                                <div class="col" id="ntpAccountBody">
                                    <p id="ntpAccountP1">'.$accountPageContent['firstParagraph'].'</p>
                                    <p id="ntpAccountP2">'.$accountPageContent['secoundParagraph'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>';
    } else {
        $strHTML = '
                    <div class="">
                        <div class="row">
                            '.wp_login_form().'
                            <p class="">'.__('Forgot password? Click','ntpRp').' <a href="'.wp_lostpassword_url().'.">'.__('here', 'ntpRp').'</a> '.__('to reset it', 'ntpRp').'.</p>
                        </div>
                        <div class="row" >
                            <div class="col jumbotron text-center alert alert-dismissible fade" id="msgBlock" role="alert">
                                <h1 id="alertTitle" class="display-5"></h1>
                                <p class="lead">
                                    <strong>
                                        <span id="msgContent"></span>
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div>';
    }
    
echo $strHTML;
}

function recurringModal($planId , $button, $title) {
    global $wpdb;

    /** Get Current user Info */
    $current_user = wp_get_current_user();

    /** Get Plan Info */
    $planData = planInfo($planId);
    $isActivePlan = count($planData) && $planData['Status'] === 1 ? true : false;

    $unsubscriptionButtonTitile = __('Unsubscription','ntpRp');
    $unsubscriptionTitle = __('Unsubscription','ntpRp'); 
    
    $buttonTitile = !is_null($button) ? $button : __('Subscription','ntpRp');
    $modalTitle = !is_null($title) ? $title : __('Subscription details','ntpRp');

    $isLoggedIn = $current_user->ID != 0 ? true : false;

    if($isActivePlan) {
        if($isLoggedIn) {
            // 1 - Check if alerady has this Plan           
            /** Check if user already exist */
            $subscription = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.'ntp_subscriptions'."` WHERE `Email` LIKE '".$current_user->user_email."' and `PlanId` = $planId and `Status` <> 2 LIMIT 1");
            if(count($subscription)) {
                /** Display Unsubscriptiuon */
                $buttonHtml = '
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#unsubscriptionRecurringModal">
                        '.$unsubscriptionButtonTitile.'
                    </button>';
                    include_once('include/partial/frontModalUnsubscription.php');
            } else {
                /** Display Subscription */ 
                /** Display Subscribe button & Modal subscription for LoggedIn user */    
                $buttonHtml = '
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recurringModal">
                        '.$buttonTitile.'
                    </button>';
                include_once('include/partial/frontModalSubscription.php');
            }
        } else {
            /** Display Subscribe buttomn & Modal subscription for Guest users */    
            $buttonHtml = '
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recurringModal">
                    '.$buttonTitile.'
                </button>';
            include_once('include/partial/frontModalSubscription.php');
        }
    } else {
        // Plan is not active / not exist / The licence is expired,... so don't display subscribe / Unsubscribe Botton
        $buttonHtml = '';
    }
    
    return $buttonHtml;
}

function getJudete($selectedStr = "") {
    $judete = array(
        'Alba' => 'Alba',
        'Argeș' => 'Argeș',
        'Arad' => 'Arad',
        'București' => 'București',
        'Bacău' => 'Bacău',
        'Bihor' => 'Bihor',
        'Bistrița Năsăud' => 'Bistrița Năsăud',
        'Brăila' => 'Brăila',
        'Botoșani' => 'Botoșani',
        'Brașov' => 'Brașov',
        'Buzău' => 'Buzău',
        'Cluj' => 'Cluj',
        'Călărași' => 'Călărași',
        'Caraș-Severin' => 'Caraș-Severin',
        'Constanța' => 'Constanța',
        'Covasna' => 'Covasna',
        'Dâmbovița' => 'Dâmbovița',
        'Dolj' => 'Dolj',
        'Gorj' => 'Gorj',
        'Galați' => 'Galați',
        'Giurgiu' => 'Giurgiu',
        'Hunedoara' => 'Hunedoara',
        'Harghita' => 'Harghita',
        'Ilfov' => 'Ilfov',
        'Ialomița' => 'Ialomița',
        'Iași' => 'Iași',
        'Mehedinți' => 'Mehedinți',
        'Maramureș' => 'Maramureș',
        'Mureș' => 'Mureș',
        'Neamț' => 'Neamț',
        'Olt' => 'Olt',
        'Prahova' => 'Prahova',
        'Sibiu' => 'Sibiu',
        'Sălaj' => 'Sălaj',
        'Satu-Mare' => 'Satu-Mare',
        'Suceava' => 'Suceava',
        'Tulcea' => 'Tulcea',
        'Timiș' => 'Timiș',
        'Teleorman' => 'Teleorman',
        'Vâlcea' => 'Vâlcea',
        'Vrancea' => 'Vrancea',
        'Vaslui' => 'Vaslui'
        );
    $strObtion = '<option value="">Choose...</option>';        
    foreach($judete as $key => $value) {
        if($selectedStr == "") {
            $strObtion .='<option value="'.$key.'">'.$value.'</option>';
        } else {
            if($selectedStr == $value ) {
                $strObtion .='<option value="'.$key.'" selected >'.$value.'</option>';
            } else {
                $strObtion .='<option value="'.$key.'" >'.$value.'</option>';
            }
        }
    }
    return $strObtion;
}

function planInfo($planId) {
    $a = new recurringFront();
    $arrayData = $a->getPlan($planId);

    if(isset($arrayData['code']) && in_array($arrayData['code'], array(11, 12, 404))) {
        $planData = array();
    } else {
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
    return $planData;
}

function authenticateUser($userInfo) {
    $current_user = wp_get_current_user();
    if($current_user->ID == 0) {
        if(is_email($userInfo['Email']) != false ) {
            // Create User
            createUser($userInfo);
        } else {
            $authenticateResult = array(
                'status'=> false,
                'msg'=> __('Email is not correct!', 'ntpRp'),
                );
            echo json_encode($authenticateResult);
            wp_die();
        }        
    } else {
        if($userInfo['UserID'] != $current_user->user_login || $userInfo['Email'] != $current_user->user_email ) {
            $authenticateResult = array(
                'status'=> false,
                'msg'=> __('Username or Email is not correct! | You already have an account.', 'ntpRp'),
                );
            echo json_encode($authenticateResult);
            wp_die();
        }
    }
}


function createUser($userInfo) {
    // $userInfo['UserID'], $userInfo['Email'], $userInfo['Pass']
    if(email_exists($userInfo['Email']) || username_exists($userInfo['UserID'])) {
        $obj = new recurringFront();
        $loginUrlLink = $obj->getLoginUrl();
        $userExist = array(
            'status'=> false,
            'msg'=> email_exists($userInfo['Email']) && username_exists($userInfo['UserID']) ? __('This user is already exist! Please Signin first.','ntpRp').'<a href="'.$loginUrlLink.'">'.__('Sign In here', 'ntpRp').'</a>' : __('The user or email are already exist!.', 'ntpRp'),
            );
        echo json_encode($userExist);
        wp_die();
    } else {
        $createdUserID = wp_create_user( $userInfo['UserID'], $userInfo['Pass'], $userInfo['Email'] );
        if($createdUserID) {
            update_user_meta( $createdUserID, "first_name",  $userInfo['Name'] ) ;
            update_user_meta( $createdUserID, "last_name",  $userInfo['LastName'] ) ;
            

            // Login auto the new user to wordpress
            clean_user_cache($createdUserID);
            wp_clear_auth_cookie();
            wp_set_current_user($createdUserID);
            wp_set_auth_cookie($createdUserID, true, false);

            $user = get_user_by('id', $createdUserID);
            update_user_caches($user);
        }
    }
}

function isStrongPass($passwordStr) {
    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
    if(preg_match($pattern, $passwordStr)){
        return true;
    } else {
        return false;
    }
}

?>