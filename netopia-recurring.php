<?php
/**
 * @package NetopiaRecurringPaymentPlugin
 */

/*
Plugin Name: NETOPIA Payments - Recurring payment
Plugin URI: https://www.netopia-system.com.ro/
Description: An subscription managment, inclouding periodic payment.
Author: Netopia
Version: 0.0.1
License: GPLv2 or later
Text Domain : ntpRp
Domain Path: /languages/
*/


defined( 'ABSPATH' ) or die('Access denied');
/** To assign short code */
add_shortcode('NTP-Recurring', 'assignToRecurring');

/** To loade the language */
load_plugin_textdomain( 'ntpRp', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages/' );

include_once( 'config/static.php' );
include_once( 'packages/recurring.php' );
include_once( 'packages/recurring-admin.php' );
include_once( 'packages/recurring-front.php' );
include_once( 'wc-netopiapayments-recurring.php' );
include_once( 'wc-netopiapayments-recurring-front.php' );


$recurringPlugin = new NetopiapaymentsRecurringPayment();







