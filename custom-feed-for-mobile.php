<?php
/**
* Plugin Name: Custom  REST API for mobile
* Description: This plugin to help you to customize your rest api.
* Version:     1.0
* Plugin URI:  https://Techsharks.af
* Author:      Alireza Akbari
* Author URI:  Techsharks.af
* License:     GPLv2 or later
* License URI: https://Techsharks.af
* Text Domain: tsapi
* Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'Sorry dude !' );

require plugin_dir_path( __FILE__ ) . 'includes/variables.php';
require plugin_dir_path( __FILE__ ) . 'includes/settings_page.php';
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';


function tsapi_custom_admin_styles() {
    wp_enqueue_style('custom-styles', plugins_url('/css/styles.css', __FILE__ ));
	}
add_action('admin_enqueue_scripts', 'tsapi_custom_admin_styles');


function tsapi_plugin_load_textdomain() {
    load_plugin_textdomain( 'sharks', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'tsapi_plugin_load_textdomain' );

function tsapi_languages()
{
    load_plugin_textdomain('sharks', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'tsapi_languages');


add_action('plugins_loaded', 'default_settings');





function tsapi_admin_menu()
{

    add_menu_page(
        __('Custom REST API', 'sharks'),    //page title
        __('Custom REST API', 'sharks'),    // menu title
        'activate_plugins',                 //capablity
        'CustomTS',                         // menu slug 
        'tsapi_settings_form_page_handler');      //function 
                                            // icon url 
                                            // position
}

add_action('admin_menu', 'tsapi_admin_menu');