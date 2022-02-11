<?php
 add_action( 'wp_enqueue_scripts', 'enqueue_and_register_ntp_recurring_js_scripts' );
 add_action( 'admin_enqueue_scripts', 'enqueue_and_register_ntp_recurring_admin_js_scripts' );

 function enqueue_and_register_ntp_recurring_js_scripts(){
    wp_enqueue_style( 'ntp_recurring_front_css', plugin_dir_url( __FILE__ ) . 'css/bootstrap/bootstrap.min.css',array(),'2.0' ,false);
    
    wp_register_script( 'ntp_recurring_script', plugin_dir_url( __FILE__ ) . 'js/bootstrap/bootstrap.bundle.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_script' );

    wp_register_script( 'ntp_recurring_front_script', plugin_dir_url( __FILE__ ) . 'js/recurring.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_front_script' );
 }

 function enqueue_and_register_ntp_recurring_admin_js_scripts(){
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/bootstrap/bootstrap.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/mdb.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/addons/datatables.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/style.css',array(),'3.3.0' ,false);

    
    wp_register_script( 'ntp_recurring_admin_script-popper', plugin_dir_url( __FILE__ ) . 'js/popper.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script-popper' );

    wp_register_script( 'ntp_recurring_admin_script-bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap/bootstrap.min.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script-bootstrap' );

    wp_register_script( 'ntp_recurring_admin_script-mdb', plugin_dir_url( __FILE__ ) . 'js/mdb.js', array(), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script-mdb' );

    wp_register_script( 'ntp_recurring_admin_script-datatables', plugin_dir_url( __FILE__ ) . 'js/addons/datatables.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script-datatables' );

    wp_register_script( 'ntp_recurring_admin_script', plugin_dir_url( __FILE__ ) . 'js/recurringAdmin.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_admin_script' );
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

function recurringModal($planId , $button, $title) {
    $current_user = wp_get_current_user();
    $buttonTitile = !is_null($button) ? $button : __('Subscription','ntpRp');
    $modalTitle = !is_null($title) ? $title : __('Subscription details','ntpRp');
    $planData = planInfo($planId);
    $isEnable = count($planData) && $planData['Status'] === 1 ? '' : 'disabled';
    $buttonHtml = '
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#recurringModal" '.$isEnable.'>
            '.$buttonTitile.'
        </button>
    ';
    if($isEnable != 'disabled') {
        $modalHtml ='
    <!-- Modal -->
    <div class="modal fade" id="recurringModal" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
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

                            <div class="row">
                                <div class="col-md-6 mb-3">
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
                                <div class="col-md-6 mb-3">
                                    <label for="email">'.__('Email','ntpRp').'</label>
                                    <input type="email" class="form-control" id="email" placeholder="you@example.com" value="'.$current_user->user_email.'">
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
                                    <label for="cc-expiration">Expiration</label>
                                    <input type="text" class="form-control" id="cc-expiration" placeholder="" required>
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
                            </div>
                            <hr class="mb-4">
                            <button class="btn btn-primary btn-lg btn-block" type="submit" onclick="addSubscription(); false;">Continue to checkout</button>
                        </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="100" style="padding: 5px 5px 0px 0px;">
                </div>
            </div>
        </div>
    </div>
    ';
    } else {
        $modalHtml = '';
    }

    return $buttonHtml.$modalHtml;
}

function getJudete() {
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
        $strObtion .='<option value="'.$key.'">'.$value.'</option>';
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
?>