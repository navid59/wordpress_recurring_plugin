<?php
class NetopiapaymentsRecurringPayment extends recurring
{
    protected $page_title = 'NETOPIA Payments Recurring Plugin';
    protected $menu_title = 'Recurring v1';
    protected $menuItems;
        
    public function __construct()
    {
    add_action( 'admin_menu', array( $this, 'create_plugin_settings' ));
    add_action( 'admin_init', array( $this, 'recurring_setup_section' ));
    add_action( 'admin_init', array( $this, 'recurring_setup_fields' ));

    add_action( 'admin_init', array( $this, 'recurring_message_section' ));
    add_action( 'admin_init', array( $this, 'recurring_message_fields' ));

    add_action( 'admin_init', array( $this, 'recurring_notify_section' ));
    add_action( 'admin_init', array( $this, 'recurring_notify_fields' ));
    }

    public function getSlug(){
        return $this->slug;
    }

    public function getPluginPath() {
        return plugin_dir_path( __FILE__ );
    }

    public function getVersion() {
        return get_bloginfo( 'version' );
    }

    public function create_plugin_settings() {
        // Add the menu item and page
        $this->menuItems = array(
            'main'=> array(
            'capability' => 'manage_options',
            'callback' => array( $this, 'dashbord' ),
            'icon' => 'dashicons-format-status',
            'position' => 100,
            'menuTitle' => 'Dashbord',
            'menuSlug' => 'recurring_dashbord'
            ),            
        'subscription'=> array(
            'capability' => 'manage_options',
            'callback' => array( $this, 'subscription_UI' ),
            'icon' => 'dashicons-format-status',
            'position' => 101,
            'pageTitle' => "Subscription management",
            'menuTitle' => 'Subscription',
            'menuSlug' => 'recurring_subscription'
        ),            
        'plan'=> array(
            'capability' => 'manage_options',
            'callback' => array( $this, 'plan_UI' ),
            'icon' => 'dashicons-format-status',
            'position' => 102,
            'pageTitle' => "Plan management",
            'menuTitle' => 'Plans',
            'menuSlug' => 'recurring_plan'
        ),        
        'report'=> array(
            'capability' => 'manage_options',
            'callback' => array( $this, 'report_UI' ),
            'icon' => 'dashicons-format-status',
            'position' => 103,
            'pageTitle' => "Report management",
            'menuTitle' => 'Reports',
            'menuSlug' => 'recurring_repport'
        ),        
        'temporary'=> array(
            'capability' => 'manage_options',
            'callback' => array( $this, 'tmp_UI' ),
            'icon' => 'dashicons-format-status',
            'position' => 103,
            'pageTitle' => "test temporary",
            'menuTitle' => 'TEMPRARY PAGE',
            'menuSlug' => 'TEMPRARY_PAGE'
        )
            
    );


        add_menu_page( $this->page_title, $this->menu_title, $this->menuItems['main']['capability'], $this->slug, $this->menuItems['main']['callback'], $this->menuItems['main']['icon'], $this->menuItems['main']['position'] );
        add_submenu_page($this->slug, $this->menuItems['subscription']['pageTitle'], $this->menuItems['subscription']['menuTitle'], $this->menuItems['subscription']['capability'], $this->menuItems['subscription']['menuSlug'], $this->menuItems['subscription']['callback'] );
        add_submenu_page($this->slug, $this->menuItems['plan']['pageTitle'], $this->menuItems['plan']['menuTitle'], $this->menuItems['plan']['capability'], $this->menuItems['plan']['menuSlug'], $this->menuItems['plan']['callback'] );
        add_submenu_page($this->slug, $this->menuItems['report']['pageTitle'], $this->menuItems['report']['menuTitle'], $this->menuItems['report']['capability'], $this->menuItems['report']['menuSlug'], $this->menuItems['report']['callback'] );
        add_submenu_page($this->slug, 'Tempory test title', 'Tempory test', $this->menuItems['temporary']['capability'], 'recurring_test', $this->menuItems['temporary']['callback'] );
    }




    public function recurring_notify_section() {
        add_settings_section( 'urls', 'Merchant Notify', array( $this, 'section_callback' ), 'notify_management' );
        add_settings_section( 'template', 'Subscriber Notify', array( $this, 'section_callback' ), 'notify_management' );
    }

    public function recurring_notify_fields() {
        $fields = array(
            array(
                'uid' => $this->slug.'_notify_url',
                'label' => 'Subscription notify url',
                'section' => 'urls',
                'type' => 'link',
                'options' => false,
                'placeholder' => 'The notify URL',
                'helper' => '',
                'supplemental' => 'ex. subdcription/notify.php',
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_email_template',
                'label' => 'Email template',
                'section' => 'template',
                'type' => 'select',
                'options' => array(
                    'confirm' => 'Confirm, is classic template for email, which subscriber will receive after payment.'
                ),
                'placeholder' => 'Text goes here',
                'helper' => '',
                'supplemental' => '',
                'default' => 'maybe'
            )
        );

        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'notify_management', $field['section'], $field );
            register_setting( 'notify_management', $field['uid'] );
        }

    }

    public function recurring_message_section() {
        add_settings_section( 'message', 'Default message ', array( $this, 'section_callback' ), 'message_management' );
    }

    public function recurring_message_fields() {
        $fields = array(
            array(
                'uid' => $this->slug.'_subscription_reg_msg',
                'label' => __('Success message of subscription'),
                'section' => 'message',
                'type' => 'textarea',
                'options' => false,
                'placeholder' => __('A success message for display after subscription'),
                'helper' => __(''),
                'supplemental' => __('Success subscription message'),
                'default' => 'Congratulation! You subscribed successfully. Thank you for choosing us.'
            ),
            array(
                'uid' => $this->slug.'_subscription_valid_card_reg_msg',
                'label' => __('Success message of subscription by only validate card'),
                'section' => 'message',
                'type' => 'textarea',
                'options' => false,
                'placeholder' => __('A success message for display after subscription by only validate a card, not actual payment.'),
                'helper' => __(''),
                'supplemental' => __('Subscription by only validate card message'),
                'default' => 'Congratulation! You subscribed successfully.Enjoy the free trial time. The payment will manage automatically after free period time.'
            ),
            array(
                'uid' => $this->slug.'_subscription_reg_failed_msg',
                'label' => __('Failed message of subscription'),
                'section' => 'message',
                'type' => 'textarea',
                'options' => false,
                'placeholder' => __('A failed message for display after failed subscription'),
                'helper' => __(''),
                'supplemental' => __('Failed subscription message'),
                'default' => 'Your subscription failed. Please try again.'
            ),
            array(
                'uid' => $this->slug.'_subscription_valid_card_reg_failed_msg',
                'label' => __('Failed message of subscription by just validate a card'),
                'section' => 'message',
                'type' => 'textarea',
                'options' => false,
                'placeholder' => __('A failed message for display after failed subscription by only validate a card'),
                'helper' => __(''),
                'supplemental' => __('Failed subscription message on only validate card'),
                'default' => 'Your subscription failed. Please verify card data & try again.'
            )
        );

        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'message_management', $field['section'], $field );
            register_setting( 'message_management', $field['uid'] );
        }
    }

    public function recurring_setup_section() {
        add_settings_section( 'general', 'General section ', array( $this, 'section_callback' ), 'netopia_recurring' );
        add_settings_section( 'conditions', 'Subscription conditions', array( $this, 'section_callback' ), 'netopia_recurring' );
        add_settings_section( 'declaration', 'Subscription declaration', array( $this, 'section_callback' ), 'netopia_recurring' );
    }

    public function recurring_setup_fields() {
        $fields = array(
            array(
                'uid' => $this->slug.'_api_key',
                'label' => __('API Key'),
                'section' => 'general',
                'type' => 'text',
                'options' => false,
                'placeholder' => __('40 character - without space'),
                'helper' => __(''),
                'supplemental' => __('You have it from your admin panel'),
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_signature',
                'label' => __('Signature ID'),
                'section' => 'general',
                'type' => 'text',
                'options' => false,
                'placeholder' => __('128 character - without space'),
                'helper' => __(''),
                'supplemental' => __('You have it from your admin panel'),
                'default' => ''
            ),
            array(
                'uid' => $this->slug.'_conditions',
                'label' => '',
                'section' => 'conditions',
                'type' => 'checkbox',
                'options' => array(
                    'conditions_description' => __('Declare that there is a clear and complete description of any kind of subscription plan displayed in website/application'),
                    'conditions_price_currency' => __('Declare that the prices and currency are clear displayed for any subscription'),
                    'conditions_modify_info' => __('Declare that, the any kind of modification on the subscription plans will inform to subscribers as well'),
                ),
                'helper' => '',
                'supplemental' => '',
                'default' => array()
            ),
            array(
                'uid' => $this->slug.'_declaration',
                'label' => '',
                'section' => 'declaration',
                'type' => 'special_checkbox',
                'options' => array(
                    'declaration_for_business' => __('Declare that accept all the terms & conditions.'),
                ),
                'helper' => '',
                'supplemental' => '',
                'default' => array(),
                'items' => array(
                    'including terms & conditions of NETOPIA Payments',
                    'including terms & conditions of Recurring service of NETOPIA Payments'),
            )
        );
        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'netopia_recurring', $field['section'], $field );
            register_setting( 'netopia_recurring', $field['uid'] );
        }
    }

    public function section_callback( $arguments )
    {
        switch( $arguments['id'] ){
            case 'general':
                echo '';
                break;
            case 'conditions':
                echo '';
                break;
            case 'declaration':
                echo '';
                break;
            case 'ssl':
                echo '';
                break;
            case 'img':
                echo '';
                break;
            case 'cerere':
                echo '<spam style="padding-right: 20px;" >Currenty you have <strong>'.rand(0,100).' creadit</strong> 
                        <br> witch meanse you have possibility to send <strong>'.rand(0,1000).' SMS </strong> total&nbsp;&nbsp;&nbsp;';
                echo '<br>So you can simply ask for charge by clicking at <a href="https://www.web2sms.ro/balance/feed/products" target="_blank">here</a></spam>';
                break;
            case 'msg2sms':
                echo '';
                break;
        }
    }

    public function field_callback( $arguments ) {
        $value = get_option( $arguments['uid'] ); // Get the current value, if there is one
        if( ! $value ) { // If no value exists
            $value = $arguments['default']; // Set to our default
        }

        // Check which type of field we want
        switch( $arguments['type'] ){
            case 'text': // If it is a text field
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea': // If it is a textarea
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select': // If it is a select dropdown
            case 'select_ssl':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $label );
                    }
                    printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
                    if( $arguments['type'] == 'select_ssl'){
                        printf('<button type="button" id="%2$s_verify" class="button button-primary">%s</button>', 'Verify SSL Certificate', $arguments['uid']);
                    }
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        @$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator, null, null );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
            case 'special_checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        @$options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], 'checkbox', $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator, null, null );
                    }
                    if( ! empty($arguments['items']) ){
                        foreach ($arguments['items'] as $item){
                            printf( '<li>%s</li>', $item );
                        }
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
            case 'link': // If it is a text field
                printf( '<span><b>%1$s%2$s%3$s</b></span>', 'https://',$_SERVER['HTTP_HOST'], '/' );
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], 'text', $arguments['placeholder'], $value );
                printf('<button type="button" id="%2$s_verify" class="button button-primary">%1$s</button>', 'check', $arguments['uid']);
                break;
            case 'send_request': // Ask NETOPIA Payments
                //
                break;
                case 'send_button': // Send SMS to All
                    printf('<button type="button" id="%2$s_send" class="button button-primary">%1$s</button>', 'send to all ;-)', $arguments['uid']);
                    break;
        }

        // If there is help text
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper ); // Show it
        }

        // If there is supplemental text
        if( $supplimental = $arguments['supplemental'] ){
            printf( '<p class="description">%s</p>', $supplimental ); // Show it
        }
    }

    public function dashbord() {
        settings_errors();
        @$active_tab = $_GET[ 'tab' ] ? $_GET[ 'tab' ] : null;
        ?>
        <div class="wrap">
            <div class="row">
                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="150" style="padding: 20px 25px 0px 0px;">
                <span style="font-size: xx-large"><?=$this->page_title ?></span>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="?page=netopia_recurring&tab=display_about_plugin" class="nav-tab <?php echo $active_tab == 'display_about_plugin' ? 'nav-tab-active' : ''; ?>"><?php echo __('About plugin','ntpRp')?></a>
                <a href="?page=netopia_recurring&tab=display_setting" class="nav-tab <?php echo $active_tab == 'display_setting' ? 'nav-tab-active' : ''; ?>"><?php echo __('Setting','ntpRp')?></a>
                <a href="?page=netopia_recurring&tab=display_notify_management" class="nav-tab <?php echo $active_tab == 'display_notify_management' ? 'nav-tab-active' : ''; ?>"><?php echo __('Notify management','ntpRp')?></a>
                <a href="?page=netopia_recurring&tab=display_message_management" class="nav-tab <?php echo $active_tab == 'display_message_management' ? 'nav-tab-active' : ''; ?>"><?php echo __('Message management','ntpRp')?></a>
            </h2>
            <form method="post" action="options.php">
                <?php
                if( $active_tab == 'display_setting' ) {
                    settings_fields( 'netopia_recurring' );
                    do_settings_sections( 'netopia_recurring' );
                    submit_button();
                }elseif($active_tab == 'display_about_plugin') {
                    require_once ('include/about_plugin.php');
                }elseif($active_tab == 'display_send_all') {
                    settings_fields( 'netopia_recurring_sned2all' );
                    do_settings_sections( 'netopia_recurring_sned2all' );
                } elseif($active_tab == 'display_message_management') {
                    settings_fields( 'message_management' );
                    do_settings_sections( 'message_management' );
                    submit_button();
                } elseif($active_tab == 'display_notify_management') {
                    settings_fields( 'notify_management' );
                    do_settings_sections( 'notify_management' );
                    submit_button();
                } else {
                    require_once ('include/welcomepage.php');
                }
                ?>
            </form>
        </div>
        <?php
    }

    public function subscription_UI() {
        ?>
        <div class="wrap">
            <div class="row">
                <img src="<?php echo URL_NETOPIA_PAYMENTS_LOGO ?>" width="150" style="padding: 20px 25px 0px 0px;">
                <span style="font-size: xx-large"><?=$this->menuItems['subscription']['pageTitle'] ?></span>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="#" class="nav-tab nav-tab-active"><?php echo __('Subscription list','ntpRp')?></a>
            </h2>
            
            <div class="row float-right">
                <nav class="">
                    <button type="button" class="btn btn-warning" onclick="getSubscriptions()">Sync Data</button>
                </nav>
            </div>
            <?php include_once('include/subscriptions.php');?>
        <div>    
        <?php
    }

    public function plan_UI() {
        @$active_tab = $_GET[ 'tab' ] ? $_GET[ 'tab' ] : null;
        ?>
        <div class="wrap">
            <div class="row">
                <img src="<?php echo URL_NETOPIA_PAYMENTS_LOGO ?>" width="150" style="padding: 20px 25px 0px 0px;">
                <span style="font-size: xx-large"><?=$this->menuItems['plan']['pageTitle'] ?></span>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="?page=recurring_plan&tab=plan_list" class="nav-tab <?php echo $active_tab == 'plan_list' ? 'nav-tab-active' : ''; ?>"><?php echo __('Plan list','ntpRp')?></a>
                <a href="?page=recurring_plan&tab=add_plan" class="nav-tab <?php echo $active_tab == 'add_plan' ? 'nav-tab-active' : ''; ?>"><?php echo __('Add / Edit Plan','ntpRp')?></a>
            </h2>
            <?php 
            if($active_tab == 'add_plan') {
                include_once('include/planAddEditForm.php');
            }else {
                echo '<div class="row float-right">
                        <nav class="">
                            <button type="button" class="btn btn-warning" onclick="getSubscriptions()">Sync Data</button>
                        </nav>
                     </div>';
            include_once('include/plans.php');
            }
            ?>
        <div>
        <?php
    }

    public function report_UI() {
        ?>
        <div class="wrap">
            <div class="row">
                <img src="<?php echo URL_NETOPIA_PAYMENTS_LOGO ?>" width="150" style="padding: 20px 25px 0px 0px;">
                <span style="font-size: xx-large"><?=$this->menuItems['report']['pageTitle'] ?></span>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="#" class="nav-tab nav-tab-active"><?php echo __('Payment history','ntpRp')?></a>
            </h2>
            
            <div class="row float-right">
                <nav class="">
                    <button type="button" class="btn btn-warning" onclick="getSubscriptions()">Sync Data</button>
                </nav>
            </div>
            <?php include_once('include/reports.php');?>
        <div> 
        <?php
    }

    public function tmp_UI() {
        @$active_tab = $_GET[ 'tab' ] ? $_GET[ 'tab' ] : null;
        ?>
        <div class="wrap">
            <div class="row">
                <img src="https://suport.mobilpay.ro/np-logo-blue.svg" width="150" style="padding: 20px 25px 0px 0px;">
                <span style="font-size: xx-large"><?=$this->page_title ?></span>
            </div>
            <h2 class="nav-tab-wrapper">
                <a href="?page=recurring_test&tab=display_tmp_0" class="nav-tab <?php echo $active_tab == 'display_tmp_0' ? 'nav-tab-active' : ''; ?>"><?php echo __('TMP 0','ntpRp')?></a>
                <a href="?page=recurring_test&tab=display_tmp_1" class="nav-tab <?php echo $active_tab == 'display_tmp_1' ? 'nav-tab-active' : ''; ?>"><?php echo __('TMP 1','ntpRp')?></a>
                <a href="?page=recurring_test&tab=display_tmp_2" class="nav-tab <?php echo $active_tab == 'display_tmp_2' ? 'nav-tab-active' : ''; ?>"><?php echo __('TMP 2','ntpRp')?></a>
                <a href="?page=recurring_test&tab=display_tmp_3" class="nav-tab <?php echo $active_tab == 'display_tmp_3' ? 'nav-tab-active' : ''; ?>"><?php echo __('TMP 3','ntpRp')?></a>
            </h2>
            <?php
                if( $active_tab == 'display_tmp_0' ) {
                    echo "AAAA";
                    echo "<pre>";
                    echo "<h1>TEST Section</h1>";
                    var_dump($this->getSignature());
                    var_dump($this->getApiKey());
                    echo "</pre>";
                }elseif($active_tab == 'display_tmp_1') {
                    echo "BBBB";
                }elseif($active_tab == 'display_tmp_2') {
                    echo "CCCC";
                } elseif($active_tab == 'display_tmp_3') {
                    echo "DDDD";
                } else {
                    echo "EEEE";
                }
            ?>
        </div>
        <?php
    }
}