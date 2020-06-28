<?php
$main_picture_post_id = get_user_meta( get_current_user_id(), 'main_picture', true );
$main_picture_meta    = null;

if ( $main_picture_post_id != false ) {
	$main_picture_url = get_post( $main_picture_post_id )->guid;
}
//$num_max_pics = get_option( 'max_profile_pictures' );
?>

<?php if ( ! $main_picture_post_id ) { ?>
    <span><?php _e( 'Your default picture', 'wmpp' ) ?></span>
    <img id="main-picture" alt="<?php _e( 'Your default picture', 'wmpp' ) ?>"
         src="http://0.gravatar.com/avatar/?s=150&d=mm" width="150px">
<?php } else { ?>
    <div>
        <span><?php _e( 'Your main profile picture', 'wmpp' ) ?></span><br>
        <img id="main-picture" class="border-1px "
             data-id="<?php echo $main_picture_post_id ?>"
             alt="<?php _e( 'Your main profile picture', 'wmpp' ) ?>"
             src="<?php echo $main_picture_url ?>" width="150px">
    </div>
<?php } ?>
<br>
<a id="new-uploader" onclick="wp_media_dialog();" href="#">
	<?php _e( 'MANAGE YOUR PICTURES HERE', 'wmpp' ) ?> <i class="fa fa-images"></i>
</a>
<br><br>