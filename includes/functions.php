<?php 
defined( 'ABSPATH' ) or die( 'Sorry dude !' );

error_reporting(0);
Global $dictionary;

function tsapi_add_menu( $request_data ) {
    // using register_nav_menus primary menu name -> 'menu-1'
    $menuLocations = get_nav_menu_locations(); // Get nav locations set in theme, usually functions.php)
    $mid = isset($request_data["menu_id"]) ? sanitize_text_field($request_data["menu_id"]) : array_values($menuLocations)[0] ;
    $primaryNav = wp_get_nav_menu_items($mid); // Get the array of wp objects, the nav items for our queried location.
    

    foreach($primaryNav as $m){
        
        if (empty($m->menu_item_parent)) {
            $menu[$m->ID] = array(
                'ID'=> $m->object_id*1,
                'title'=>$m->title,/*'url'=>$m->url*/
                'children'=>array());
        }
        
        if ($m->menu_item_parent) {
            $menu[$m->menu_item_parent]['children'][] = array(
            'ID'=>$m->object_id*1,
            'title'=>$m->title,/*'url'=>$m->url*/);
         }
        $menufinal  = array();
        foreach($menu as $i => $v){
            $menufinal[] = $v;
        }
}
    return $menufinal;

}

//toggle the menu
if(get_option($dictionary['ts_menu']) == "on"){
add_action( 'rest_api_init', function(){
        //https://your-wp-domain-url.com/wp-json/custom-name/menu
        register_rest_route( 'tsapi', '/menu', array(
        'methods' => 'GET',
        'callback' => 'tsapi_add_menu',
    ) );

} );    
}



function tsapi_add_custom_settings() {
    // using register_nav_menus primary menu name -> 'menu-1'
    Global $dictionary;
    $settings = array();

    $settings["menu_image"] = wp_get_attachment_image_src(get_option($dictionary['ts_menu_image']), "medium")[0];
    $settings["settings"] = get_option($dictionary['ts_name']);
    return $settings;

}

//toggle the menu settings
if( (int)get_option($dictionary['ts_menu_image']) > 1 ){
    add_action( 'rest_api_init', function(){
            register_rest_route( 'tsapi', '/settings', array(
            'methods' => 'GET',
            'callback' => 'tsapi_add_custom_settings',
        ) );
    } );    
}

 
function tsapi_add_related_restapi( $request_data ) {
    $related = isset($request_data['related']) ? sanitize_text_field($request_data['related']) : 5 ; 
    $post_id = sanitize_text_field($request_data['post_id']);
    $ptags = wp_get_post_tags($post_id);
    // $first_tag = $ptags[0]->term_id;
    // get only ids
    foreach ($ptags as $tag) { $tags[] = $tag->term_id; }

    $uposts = new WP_Query( array(
        'tag__in' => $tags, //array($first_tag),
        'caller_get_posts' => 1,
        'posts_per_page' => $related,
        'post__not_in'   => array($post_id),
    ));
    
    // add image to related posts
    foreach ($uposts->posts as $post) {
        $post->images = tsapi_get_image_url($post);
    }

    return  $uposts->posts;
 }
add_action( 'rest_api_init', function () {
    register_rest_route( 'tsapi', '/post/related/', array(
            'methods' => 'GET',
            'callback' => 'tsapi_add_related_restapi'
    ));
});




add_action( 'rest_api_init', 'tsapi_add_remove_content' );
// "venue" is a custom post type I created using the WP Types plugin
function tsapi_add_remove_content() {
    // first register the field with WP REST API
    register_rest_field( 'post',
        'images',
        array(
            'get_callback'    => 'tsapi_get_image_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}



add_action( 'rest_api_init', function() {
    // first register the field with WP REST API
    register_rest_field( 'post',
        'content',
        array(
            'get_callback'    => 'tsapi_remove_content',
            'update_callback' => null,
            'schema'          => null,
        )
    );
} );

function tsapi_remove_content($post,  $field_name, $request ){
    
    if(!empty($request["page"]) or !empty($request["per_page"]) )
    unset($post["content"]) ;
    else
        return $post["content"];
    }



function tsapi_get_image_url($post) {
    
    $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' )['0'];
    $medium_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' )['0'];
    $large_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' )['0'];
    
    return array(
        'thumbnail' => $thumb_url,
        'medium' => $medium_url,
        'large'  => $large_url,
    );
}


function tsapi_default_settings(){
    Global $dictionary;
    if(get_option($dictionary['ts_menu']) == null) 
    update_option($dictionary['ts_menu'], 'on');
    
}