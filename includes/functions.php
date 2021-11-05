<?php 
defined( 'ABSPATH' ) or die( 'Sorry dude !' );

Global $dictionary;

function tsapi_add_menu( $request_data ) {
    // using register_nav_menus primary menu name -> 'menu-1'
    $menuLocations = get_nav_menu_locations(); // Get nav locations set in theme, usually functions.php)
    $mid = isset($request_data["menu_id"]) ? $request_data["menu_id"] : array_values($menuLocations)[0] ;
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
            'callback' => 'tsapi_add_related_restapi'
    ));
});


add_action( 'rest_api_init', 'tsapi_add_image_restapi' );
// "venue" is a custom post type I created using the WP Types plugin
function tsapi_add_image_restapi() {
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

function tsapi_get_image_url($post, $field_name, $request) {
    // I wanted the value to appear in the response as "youtube_embed", 
    // and I wanted the "wpcf-youtube-embed" custom field's value there
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), 'thumbnail' );
    $thumb_url = $thumb['0'];

    $medium = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), 'medium' );
    $medium_url = $medium['0'];

    $large = wp_get_attachment_image_src( get_post_thumbnail_id( $post->id ), 'large' );
    $large_url = $large['0'];

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