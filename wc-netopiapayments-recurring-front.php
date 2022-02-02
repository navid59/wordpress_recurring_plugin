<?php
 add_action( 'wp_enqueue_scripts', 'enqueue_and_register_ntp_recurring_js_scripts' );
 add_action( 'admin_enqueue_scripts', 'enqueue_and_register_ntp_recurring_admin_js_scripts' );

 function enqueue_and_register_ntp_recurring_js_scripts(){
    wp_register_script( 'ntp_recurring_script', plugin_dir_url( __FILE__ ) . 'js/recurring.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'ntp_recurring_script' );
 }

 function enqueue_and_register_ntp_recurring_admin_js_scripts(){
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/bootstrap/bootstrap.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/mdb.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/addons/datatables.min.css',array(),'2.0' ,false);
    wp_enqueue_style( 'ntp_recurring_admin_css', plugin_dir_url( __FILE__ ) . 'css/style.css',array(),'2.0' ,false);

    
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

function assignToRecurring() {
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;

 

    $str = '<p class="text-justify">';
    $str .= '<b>Nic Name : </b>'.$current_user->user_nicename.'<br>';
    $str .= '<b>Display Name : </b>'.$current_user->display_name.'<br>';
    $str .= '<b>Email  : </b>'.$current_user->user_email.'<br>';
    if($current_user->ID) {
        $str .= '<button type="button" class="btn btn-secondary" onclick="myFunction()">NETOPIA PAYMENT Subscribe for '.$current_user->user_nicename.' with email '.$current_user->user_email.'</button>';
    } else {
        $str .= '<button type="button" class="btn btn-warning" onclick="myFunction()">NETOPIA PAYMENT Subscribe</button>';
    }
    $str .= '</p>';
    return $str;
 }

?>