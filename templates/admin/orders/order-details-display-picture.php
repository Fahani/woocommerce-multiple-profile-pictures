<?php
/**
 * @var $order_picture array
 */
?>
<p class="form-field form-field-wide wc-customer-user">
    <label><?php _e( 'Main profile picture', 'wmpp' ) ?>:</label>
    <img alt="<?php _e( 'Main profile picture', 'wmpp' ) ?>" class="components-popover__content"
         src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/orders/{$order_picture[0]['pic_name']}" ?>">
</p>