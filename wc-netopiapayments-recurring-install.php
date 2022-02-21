<?php
function recurring_install () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
 
    $subscription_table_name = $wpdb->prefix . "ntp_subscriptions"; 
    $plan_table_name = $wpdb->prefix . "ntp_plans"; 
    $history_table_name = $wpdb->prefix . "ntp_history"; 

    

    $sql_subscription = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    name tinytext NOT NULL,
    text text NOT NULL,
    url varchar(55) DEFAULT '' NOT NULL,
    PRIMARY KEY  (id)
    ) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

add_option( 'recurring_db_version', "1.0" );
 }
?>