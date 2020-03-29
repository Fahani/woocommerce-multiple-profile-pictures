<?php
/**
 * @var $order_picture array
 */
?>
<img alt="<?php _e( 'Main profile picture', 'wmpp' ) ?>" width="20" class="components-popover__content"
     src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/orders/{$order_picture[0]['pic_name']}" ?>">

