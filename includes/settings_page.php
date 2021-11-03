<?php
defined( 'ABSPATH' ) or die( 'Sorry dude !' );

function clean($input){
    return sanitize_text_field($input);
}

function tsapi_settings_form_page_handler()
{
    
    wp_enqueue_media();

    $message = '';
    Global $dictionary;

    

    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) ) {
            
        $menu_setting = $_REQUEST['menu'] == "on" ? $_REQUEST['menu'] : 'off' ;
        $related_setting = $_REQUEST['related'] == "on" ? $_REQUEST['related'] : 'off' ;

        update_option($dictionary['ts_name'], clean($_POST['name']));
        update_option($dictionary['ts_menu'], clean($menu_setting));
        update_option($dictionary['ts_related'], clean($related_setting));
        update_option( $dictionary['ts_menu_image'], clean(absint( $_POST['image_attachment_id'] )));

        $message = __('Item was successfully saved', 'tsapi'); 
    }
    else {
        $menu_setting =get_option($dictionary['ts_menu']);
    }

    
    

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php esc_html_e('Settings', 'tsapi')?> </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo esc_attr($notice); ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo esc_attr($message); ?></p></div>
    <?php endif;?>


    <form method="POST" id='form'>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <input type="hidden" name="id" value="<?php echo esc_attr($item['id']) ?>"/>
        <table class="form-table" role="presentation">
            
            
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><?php esc_html_e('App name:', 'tsapi')?></th>
                    <td><label for="name"><input name="name" type="text" id="name" value="<?php echo esc_attr(get_option( $dictionary['ts_name'] )); ?>" > </label></td>
                </tr>       
            </tbody>
            
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><?php esc_html_e('Add Menu to Rest API :', 'tsapi')?></th>
                    <td><label for="menu"><input name="menu" type="checkbox" id="menu" <?php echo ($menu_setting == 'on' ? 'checked' : '' ) ?> > Enable menu in Rest API </label></td>
                </tr>       
            </tbody>

            <tbody>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><?php esc_html_e('Add Related posts to Rest API :', 'tsapi')?></th>
                    <td><label for="related"><input name="related" type="checkbox" id="related" <?php echo ($related_setting == 'on' ? 'checked' : '' ) ?> > Enable related in Rest API </label></td>
                </tr>       
            </tbody>

            
            
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <th scope="row"><?php esc_html_e('Menu cover photo :', 'tsapi')?></th>
                    <td>
                        <div class='image-preview-wrapper'>
                            <img id='image-preview' src='<?php echo wp_get_attachment_url( get_option( $dictionary['ts_menu_image']) ); ?>' height='100'>
                        </div>
                        <input id="upload_image_button" type="button" class="button" value="<?php esc_html_e( 'Upload image' ); ?>" />
                        <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?php echo esc_url(get_option( $dictionary['ts_menu_image'])); ?>'>
                    </td>
                </tr>       
            </tbody>
            

        </table>
        <p><input type="submit" value="<?php esc_html_e('Save', 'tsapi')?>" id="submit" class="button-primary" name="submit"></p>
    </form>




    
</div>

<script type='text/javascript'>

          <?php $my_saved_attachment_post_id = get_option( 'media_selector_attachment_id', 0 );?>

          jQuery( document ).ready( function( $ ) {

            // Uploading files
            var file_frame;
            var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            var set_to_post_id = <?php echo esc_attr($my_saved_attachment_post_id); ?>; // Set this

            jQuery('#upload_image_button').on('click', function( event ){

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if ( file_frame ) {
                    // Set the post ID to what we want
                    file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                    // Open frame
                    file_frame.open();
                    return;
                } else {
                    // Set the wp.media post id so the uploader grabs the ID we want when initialised
                    wp.media.model.settings.post.id = set_to_post_id;
                }

                // Create the media frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: 'Select a image to upload',
                    button: {
                        text: 'Use this image',
                    },
                    multiple: false // Set to true to allow multiple files to be selected
                });

                // When an image is selected, run a callback.
                file_frame.on( 'select', function() {
                    // We set multiple to false so only get one image from the uploader
                    attachment = file_frame.state().get('selection').first().toJSON();

                    // Do something with attachment.id and/or attachment.url here
                    $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
                    $( '#image_attachment_id' ).val( attachment.id );

                    // Restore the main post ID
                    wp.media.model.settings.post.id = wp_media_post_id;
                });

                    // Finally, open the modal
                    file_frame.open();
            });

            // Restore the main ID when the add media button is pressed
            jQuery( 'a.add_media' ).on( 'click', function() {
                wp.media.model.settings.post.id = wp_media_post_id;
            });
        });
    

    </script>

<style type="text/css">

</style>
<?php
}