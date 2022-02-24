<?php
function recurring_install () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
 
    $subscription_table_name = $wpdb->prefix . "ntp_subscriptions"; 
    $plan_table_name = $wpdb->prefix . "ntp_plans"; 
    $history_table_name = $wpdb->prefix . "ntp_history"; 

    

    $sql_subscription = "CREATE TABLE $subscription_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    Subscription_Id varchar(50) NOT NULL,
    First_Name varchar(50) NOT NULL,
    Last_Name varchar(50) NOT NULL,
    Email varchar(50) NOT NULL,
    Tel varchar(15) NOT NULL,
    Address varchar(50) NOT NULL,
    City varchar(50) NOT NULL,
    UserID varchar(50) NOT NULL,
    NextPaymentDate datetime NULL,
    PlanId int(11) NOT NULL,
    StartDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    EndDate datetime DEFAULT NULL,
    Status int(2) NOT NULL,
    CreatedAt datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    UpdatedAt datetime DEFAULT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";


    $sql_plan = "CREATE TABLE $plan_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    Plan_Id int(11) NOT NULL,
    Title varchar(50) NOT NULL,
    Amount DOUBLE(5,2) NOT NULL,
    Description varchar(50) NOT NULL,
    Frequency_Type varchar(15) NOT NULL,
    Frequency_Value int(5) NOT NULL,
    Grace_Period int(5) NOT NULL,
    Initial_Paymen ENUM('false', 'true') NOT NULL DEFAULT 'false',
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



require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql_subscription );
dbDelta( $sql_plan );
dbDelta( $sql_history );

add_option( 'recurring_db_version', "1.0" );
}
?>