<?php 
defined( 'ABSPATH' ) or die( 'Sorry dude !' );

Global $dictionary;

function wp_menu_route() {
    // using register_nav_menus primary menu name -> 'menu-1'
    $menuLocations = get_nav_menu_locations(); // Get nav locations set in theme, usually functions.php)
                                               // returns an array of menu locations ([LOCATION_NAME] = MENU_ID);
    $menuID = $menuLocations['primary']; // Get the *primary* menu added in register_nav_menus()
    $primaryNav = wp_get_nav_menu_items($menuID); // Get the array of wp objects, the nav items for our queried location.
    return $primaryNav;

}
function register_menu(){
        //https://your-wp-domain-url.com/wp-json/custom-name/menu
        register_rest_route( 'tsapi', '/menu', array(
        'methods' => 'GET',
        'callback' => 'wp_menu_route',
    ) );

}
//toggle the menu
if(get_option($dictionary['ts_menu']) == "on"){
add_action( 'rest_api_init', 'register_menu' );    
}



function wp_menu_settings_route() {
    // using register_nav_menus primary menu name -> 'menu-1'
    Global $dictionary;
    $settings = array();

    $settings["menu_image"] = wp_get_attachment_image_src(get_option($dictionary['ts_menu_image']), "medium")[0];
    return $settings;

}
function register_menu_settings(){
        register_rest_route( 'tsapi', '/menu_settings', array(
        'methods' => 'GET',
        'callback' => 'wp_menu_settings_route',
    ) );

}
//toggle the menu settings
if( (int)get_option($dictionary['ts_menu_image']) > 1 ){
add_action( 'rest_api_init', 'register_menu_settings' );    
}




function default_settings(){
    Global $dictionary;
    if(get_option($dictionary['ts_menu']) == null) 
    update_option($dictionary['ts_menu'], 'on');
    
}