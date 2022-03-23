<?php
 add_action( 'wp_enqueue_scripts', 'enqueue_and_register_ntp_recurring_js_scripts' );
 add_action('wp_ajax_addNewSubscription', 'recurring_addSubscription');
 add_action('wp_ajax_nopriv_addNewSubscription', 'recurring_addSubscription');
 add_action('wp_ajax_updateSubscriberAccountDetails', 'recurring_updateSubscriberAccountDetails');
 add_action('wp_ajax_unsubscription', 'recurring_unsubscription');
 add_action('wp_ajax_getMySubscriptions', 'recurring_account_getMySubscriptions');
 add_action('wp_ajax_getMyNextPayment', 'recurring_getMyNextPayment');
 add_action('wp_ajax_getMyAccountDetails', 'recurring_getMyAccountDetails');
 add_action('wp_ajax_logoutAccount', 'recurring_logoutAccount');

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

    /**
     *  Authenticate User
     *  If not exist will be create
     *  */
    authenticateUser($Member); 


    $obj3DS = json_decode(stripslashes($_POST['ThreeDS']));
    $arr3DS = (array)$obj3DS;
    

    $a = new recurringFront();
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
            "Signature" => $a->getSignature(),
            "NotifyUrl" => $a->getNotifyUrl(),
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
     

    $jsonResultData = $a->setSubscription($subscriptionData);
    
    // Add subscription to DB 
    if($jsonResultData['code'] === "00") {
        $wpdb->insert( 
            $wpdb->prefix . "ntp_subscriptions", 
            array( 
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
                'Status'          => 100,
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
    wp_die();
}

function recurring_getMyNextPayment() {
    
    $a = new recurringAdmin();
    $nextPaymentData = array(
            "SubscriptionId" => $_POST['subscriptionId']+0
    );

    $jsonResultData = $a->getNextPayment($nextPaymentData);
    
    $mySimulatedResult = array(
            'status'=> isset($jsonResultData['code']) && $jsonResultData['code']!== "00" ? false : true,
            'msg'=> $jsonResultData['message'],
            'data' =>  $jsonResultData
            );
    echo json_encode($mySimulatedResult);
    die();
}

function recurring_unsubscription() {
    global $wpdb;
  
    $a = new recurringFront();
    $subscriptionData = array(
            "Signature" => $a->getSignature(),
            "SubscriptionId" => $_POST['SubscriptionId']+0
      );   
     
    $jsonResultData = $a->setUnsubscription($subscriptionData);
    
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
    }

    $mySimulatedResult = array(
        'status'=> $jsonResultData['code'] === "00" ? true : false,
        'msg'=> $jsonResultData['message'],
        );
    echo json_encode($mySimulatedResult);
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
                                                            <input type="text" class="form-control" id="SubscriptionId" placeholder="" value="'.$MyDetails[0]['Subscription_Id'].'" readonly>
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

                $msg = __('Password is changed & ','ntpRp');
            } else {
                $msg = __('Password is not changed & ','ntpRp');
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
                            'Subscription_Id' => $subscriptionAccountDetails['SubscriptionId'] 
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
                                          s.Subscription_Id,
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
                                <button type="button" class="btn btn-primary unsubscriptionMyAccounButton" data-subscriptionId="'.$plan['Subscription_Id'].'" data-planId="'.$plan['id'].'" data-planTitle="'.$plan['Title'].'" data-toggle="modal" data-target="#unsubscriptionMyAccountModal" >
                                    '.__('Unsubscription','ntpRp').'
                                </button>
                                <button type="button" class="btn btn-info" onclick="frontSubscriptionNextPayment('.$plan['Subscription_Id'].','.$plan['id'].',\''.$plan['Title'].'\')"><i class="fa fa-credit-card"></i></button>
                                </div>
                            </div>
                        </div>';
        }
    } else {
        $htmlThem = '<h4>'.__('You are not subscribe in any of our plans, yet!','ntpRp').'</h4>';
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
                                                                <button id="unsubscriptionButton" class="btn btn-secondary" type="button" onclick="unsubscription(); return false;">Unsubscribe</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div id="loading" class="d-flex align-items-center fade">
                                                        <strong>'.__('Loading...','ntpRp').'</strong>
                                                        <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                                                    </div>
                                                    <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                                                        <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
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
    $isEnable = count($planData) && $planData['Status'] === 1 ? '' : 'disabled';
    $showUserPassEmail = $current_user->ID != 0 ? 'readonly disabled' : '';
    $showUserPassEmailDiv = $current_user->ID != 0 ? 'd-none' : '';


    /** Check if user already exist */
    $subscription = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.'ntp_subscriptions'."` WHERE `Email` LIKE '".$current_user->user_email."' and `PlanId` = $planId and `Status` <> 2 LIMIT 1");
    if(count($subscription)) {
            /** Display Unsubscription button & Modal for Unsubscribe */
            $unsubscriptionButtonTitile = __('Unsubscription','ntpRp');
            $unsubscriptionTitle = __('Unsubscription','ntpRp');    
            $buttonHtml = '
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#unsubscriptionRecurringModal" '.$isEnable.'>
                    '.$unsubscriptionButtonTitile.'
                </button>';

            if($isEnable != 'disabled') {
                $modalHtml ='
                <!-- Modal -->
                <div class="modal fade" id="unsubscriptionRecurringModal" tabindex="-1" aria-labelledby="unsubscriptionRecurringModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 15px 0px 0px;">
                                <h2 class="modal-title" id="unsubscriptionRecurringModalLabel">'.$unsubscriptionTitle.'</h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">            
                                <div class="row">
                                    <div class="col-md-12 order-md-1">
                                        <form id="unsubscription-form" class="needs-validation" novalidate>
                                            '.__('Are you sure to unsubscribe from ','ntpRp').'
                                            '.$planData['Title'].' !?
                                            <br>
                                            '.__('To unsubscribe click on unsubscribe button.','ntpRp').' '.__('Otherwise close the window','ntpRp').'
                                            <hr>
                                            <input type="hidden" class="form-control" id="Id" value="'.$subscription[0]->id.'" readonly>
                                            <input type="hidden" class="form-control" id="Subscription_Id" value="'.$subscription[0]->Subscription_Id.'" readonly>
                                            <button id="unsubscriptionButton" class="btn btn-secondary" type="button" onclick="unsubscription(); return false;">Unsubscribe</button>
                                        </form>
                                    </div>
                                </div>
                                <div id="loading" class="d-flex align-items-center fade">
                                    <strong>'.__('Loading...','ntpRp').'</strong>
                                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                                </div>
                                <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                                    <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
                                </div>                                
                            </div>
                            <div class="modal-footer">
                                '.__('Supported by NETOPIA Payments').'
                            </div>
                        </div>
                    </div>
                </div>
                ';
                } else {
                    $modalHtml = '';
                }
            /////////////////////////////////////
    } else {
            /** Display Subscribe buttomn & Modal for subscription */
            $buttonTitile = !is_null($button) ? $button : __('Subscription','ntpRp');
            $modalTitle = !is_null($title) ? $title : __('Subscription details','ntpRp');    
            $buttonHtml = '
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recurringModal" '.$isEnable.'>
                    '.$buttonTitile.'
                </button>';

            if($isEnable != 'disabled') {
                $modalHtml ='
                <!-- Modal -->
                <div class="modal fade" id="recurringModal" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 15px 0px 0px;">
                                <h2 class="modal-title" id="recurringModalLabel">'.$modalTitle.'</h2>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">            
                                <div class="row">
                                    <div class="col-md-12 order-md-1">
                                    <h4 class="mb-3">'.__('Subscription detail','ntpRp').'</h4>
                                    <form class="needs-validation" novalidate>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <h3><b>'.$planData['Title'].'</b></h3>
                                                    <h4>'.$planData['Description'].'</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card mb-4 box-shadow">
                                                    <div class="card-header">
                                                        <h4 class="my-0 font-weight-normal">'.__('Amount','ntpRp').'</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <h1 class="card-title pricing-card-title">'.$planData['Amount'].' '.$planData['Currency'].' <small class="text-muted">/ '.$planData['Frequency']['Value'].' '.$planData['Frequency']['Type'].'</small></h1>
                                                        <input type="hidden" class="form-control" id="planID" value="'.$planId.'">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="mb-4">
                                        <h4 class="mb-3">'.__('Personal information').'</h4>
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
                                        
                                        <div class="row '.$showUserPassEmailDiv.'" >
                                            <div class="col-md-4 mb-3">
                                                <label for="username">'.__('Username','ntpRp').'</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">@</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="username" placeholder="Username" value="'.$current_user->user_login.'" required '.$showUserPassEmail.'>
                                                    <div class="invalid-feedback" style="width: 100%;">
                                                    Your username is required.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="password">'.__('Password','ntpRp').'</label>
                                                <input type="password" class="form-control" id="password" required '.$showUserPassEmail.'>
                                                <div class="invalid-feedback">
                                                    Please enter a valid password.
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="email">'.__('Email','ntpRp').'</label>
                                                <input type="email" class="form-control" id="email" placeholder="you@example.com" value="'.$current_user->user_email.'" required '.$showUserPassEmail.'>
                                                <div class="invalid-feedback">
                                                    Please enter a valid email address for shipping updates.
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3">
                                            <label for="address">'.__('Address','ntpRp').'</label>
                                            <input type="text" class="form-control" id="address" placeholder="1234 Main St" required>
                                            <div class="invalid-feedback">
                                                Please enter your shipping address.
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label for="tel">'.__('Tel','ntpRp').'</label>
                                                <input type="text" class="form-control" id="tel" placeholder="" required>
                                                <div class="invalid-feedback">
                                                Phone required.
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="country">'.__('Country','ntpRp').'</label>
                                                <select class="custom-select d-block w-100" id="country" required>
                                                <option value="">Choose...</option>
                                                <option value="642">Romania</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                Please select a valid country.
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="state">'.__('State','ntpRp').'</label>
                                                <select class="custom-select d-block w-100" id="state" required>'
                                                .getJudete().
                                                '</select>
                                                <div class="invalid-feedback">
                                                Please provide a valid state.
                                                </div>
                                            </div>                        
                                        </div>

                                        <hr class="mb-4">
                                        <h4 class="mb-3">'.__('Payment information', 'ntpRp').'</h4>
                                        <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cc-name">Name on card</label>
                                            <input type="text" class="form-control" id="cc-name" placeholder="" required>
                                            <small class="text-muted">Full name as displayed on card</small>
                                            <div class="invalid-feedback">
                                            Name on card is required
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cc-number">Credit card number</label>
                                            <input type="text" class="form-control" id="cc-number" placeholder="" required>
                                            <div class="invalid-feedback">
                                            Credit card number is required
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="cc-expiration-month">'.__('Expiration Month','ntpRp').'</label>
                                                <input type="text" class="form-control" id="cc-expiration-month" placeholder="" required>
                                                <div class="invalid-feedback">
                                                Expiration date required
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="cc-expiration-year">'.__('Expiration Year','ntpRp').'</label>
                                                <input type="text" class="form-control" id="cc-expiration-year" placeholder="" required>
                                                <div class="invalid-feedback">
                                                Expiration date required
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="cc-expiration">CVV</label>
                                                <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
                                                <div class="invalid-feedback">
                                                Security code required
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                &nbsp;
                                            </div>
                                        </div>
                                        <hr class="mb-4">
                                        <button id="addSubscriptionButton" class="btn btn-primary btn-lg btn-block" type="button" onclick="addSubscription(); return false;">Continue to checkout</button>
                                    </form>
                                    </div>
                                </div>
                                <div id="loading" class="d-flex align-items-center fade">
                                    <strong>'.__('Loading...','ntpRp').'</strong>
                                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                                </div>
                                    <div class="alert alert-dismissible fade" id="msgBlock" role="alert">
                                        <strong id="alertTitle">!</strong> <span id="msgContent"></span>.
                                    </div>
                                </div>
                            <div class="modal-footer">
                                '.__('Supported by NETOPIA Payments').'
                            </div>
                        </div>
                    </div>
                </div>
                ';
                } else {
                    $modalHtml = '';
                }
            }
   
   

    return $buttonHtml.$modalHtml;
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
    
    if(isset($arrayData['code']) && ($arrayData['code'] == 11 || $arrayData['code'] == 12)) {
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
                'msg'=> __('Username or Email is not correct!', 'ntpRp'),
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