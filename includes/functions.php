<?php 
defined( 'ABSPATH' ) or die( 'Sorry dude !' );

Global $dictionary;

function wp_menu_route( $request_data ) {
    // using register_nav_menus primary menu name -> 'menu-1'
    $menuLocations = get_nav_menu_locations(); // Get nav locations set in theme, usually functions.php)
    $menuID = $menuLocations['menu-1'];                                 // returns an array of menu locations ([LOCATION_NAME] = 
    $mid = isset($request_data["menu_id"]) ? $request_data["menu_id"] : $menuID;

      // Get the *primary* menu added in register_nav_menus()
    $primaryNav = wp_get_nav_menu_items($mid); // Get the array of wp objects, the nav items for our queried location.
    return $primaryNav;

}

//toggle the menu
if(get_option($dictionary['ts_menu']) == "on"){
add_action( 'rest_api_init', function(){
        //https://your-wp-domain-url.com/wp-json/custom-name/menu
        register_rest_route( 'tsapi', '/menu', array(
        'methods' => 'GET',
        'callback' => 'wp_menu_route',
    ) );

} );    
}



function wp_menu_settings_route() {
    // using register_nav_menus primary menu name -> 'menu-1'
    Global $dictionary;
    $settings = array();

    $settings["menu_image"] = wp_get_attachment_image_src(get_option($dictionary['ts_menu_image']), "medium")[0];
    return $settings;

}

//toggle the menu settings
if( (int)get_option($dictionary['ts_menu_image']) > 1 ){
    add_action( 'rest_api_init', function(){
            register_rest_route( 'tsapi', '/menu_settings', array(
            'methods' => 'GET',
            'callback' => 'wp_menu_settings_route',
        ) );
    } );    
}

 
function related_posts_endpoint( $request_data ) {
    $related = isset($request_data['related']) ? $request_data['related'] : 5 ; 
    $tags = wp_get_post_tags($request_data['post_id']);
    // $first_tag = $tags[0]->term_id;

    $uposts = get_posts(
    array(
        'tag__in' => $tags, //array($first_tag),
        'caller_get_posts'=> 1,
        'posts_per_page' => $related,
        'post__not_in'   => array($request_data['post_id']),//your requested post id 
    )
    );
    return  $uposts;
 }
add_action( 'rest_api_init', function () {
    register_rest_route( 'tsapi', '/post/related/', array(
            'methods' => 'GET',
            'callback' => 'related_posts_endpoint'
    ));
});



function default_settings(){
    Global $dictionary;
    if(get_option($dictionary['ts_menu']) == null) 
    update_option($dictionary['ts_menu'], 'on');
    
}