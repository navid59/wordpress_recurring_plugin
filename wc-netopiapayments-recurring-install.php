<?php
function recurring_install () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
 
    $subscription_table_name = $wpdb->prefix . "ntp_subscriptions"; 
    $plan_table_name = $wpdb->prefix . "ntp_plans"; 
    $history_table_name = $wpdb->prefix . "ntp_history"; 
    $activity_table_name = $wpdb->prefix . "ntp_activity"; 

    

    $sql_subscription = "CREATE TABLE $subscription_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    Subscription_Id varchar(50) NOT NULL,
    First_Name varchar(50) NOT NULL,
    Last_Name varchar(50) NOT NULL,
    Email varchar(50) NOT NULL,
    Tel varchar(15) NOT NULL,
    Address varchar(255) NOT NULL,
    City varchar(50) NOT NULL,
    UserID varchar(50) NOT NULL,
    NextPaymentDate datetime NULL,
    PlanId varchar(50) NOT NULL,
    StartDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    EndDate datetime DEFAULT NULL,
    Status int(2) NOT NULL,
    CreatedAt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    UpdatedAt datetime DEFAULT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";


    $sql_plan = "CREATE TABLE $plan_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    Plan_Id varchar(50) NOT NULL,
    Title varchar(50) NOT NULL,
    Amount DOUBLE(5,2) NOT NULL,
    Currency varchar(5) NULL,
    Description varchar(50) NOT NULL,
    Recurrence_Type varchar(15) NOT NULL,
    Frequency_Type varchar(15) NOT NULL,
    Frequency_Value int(5) NOT NULL,
    Grace_Period int(5) NOT NULL,
    Initial_Paymen ENUM('false', 'true') NOT NULL DEFAULT 'false',
    Status int(2) NOT NULL,
    CreatedAt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    UpdatedAt datetime DEFAULT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";


    $sql_history = "CREATE TABLE $history_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    TransactionID varchar(50) NOT NULL,
    PaymentComment varchar(50) NULL,
    Label varchar(15) NOT NULL,
    status varchar(15) NOT NULL,
    CreatedAt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql_activity = "CREATE TABLE $activity_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    Subscription_Id varchar(50) NOT NULL,
    UserID varchar(50) NOT NULL,
    Comment varchar(255) NULL,
    Label varchar(15) NOT NULL,
    CreatedAt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";



require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql_subscription );
dbDelta( $sql_plan );
dbDelta( $sql_history );
dbDelta( $sql_activity );

add_option( 'recurring_db_version', "1.0" );
}

function recurring_account_page() {
    $strPageTitle = wp_strip_all_tags(__('Subscription Account', 'ntpRp'));
    $slugPage = sanitize_title($strPageTitle);

    /** Check if page already exist */
    $page = get_page_by_path( $slugPage , OBJECT );

    if (isset($page)) {
        /**
         * The Subscription Account page is already exist
         * So, Not change the page content
         */
    } else {
        // Create post object
        $ntpAccountPage = array(
            'post_title'    => $strPageTitle,
            'post_content'  => '<!-- wp:shortcode -->[NTP-Recurring-My-Account]<!-- /wp:shortcode -->',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page',
        );
    
        // Generate Recurring Account Page
        wp_insert_post( $ntpAccountPage );
    }    
}
?>