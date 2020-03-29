<?php
/**
 * @var $main_picture array
 * @var $num_max_pics int
 * @var $num_pics_user int
 * @var $rest_pictures array
 */
?>

<?php if ( empty( $main_picture ) ) { ?>
    <span><?php _e( 'Your default picture', 'wmpp' ) ?></span>
    <img alt="<?php _e( 'Your default picture', 'wmpp' ) ?>" src="http://0.gravatar.com/avatar/?s=150&d=mm">
<?php } else { ?>
    <div class="border-1px padding-05-em">
        <span><?php _e( 'Your main profile picture', 'wmpp' ) ?></span><br>
        <img class="border-1px " alt="<?php _e( 'Your main profile picture', 'wmpp' ) ?>"
             src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/users/{$main_picture[0]['pic_name']}" ?>">
        <small class="block">ID: <?php echo $main_picture[0]['mpp_user_picture_id'] ?></small>
        <label><?php _e( 'Delete', 'wmpp' ) ?></label>
        <input type="checkbox" name="remove[]" value="<?php echo $main_picture[0]['mpp_user_picture_id'] ?>">
        <br><br>
        <span><?php _e( 'Do you want to replace the main picture? You can upload the replacement here', 'wmpp' ) ?></span><br>
        <input type="file" name="replace_main_picture">
    </div>
<?php } ?>

<?php if ( $num_max_pics == - 1 || $num_pics_user < $num_max_pics ) { ?>
    <br>
    <div class="border-1px padding-05-em">
        <span><?php _e( 'You can upload multiple pictures at once here', 'wmpp' ) ?></span>
        <br>
        <input type="file" name="profile_pictures[]" multiple="multiple">
    </div>
<?php } ?>
<br>

<?php if ( ! empty( $rest_pictures ) ) { ?>
<div class="border-1px padding-05-em">
	<?php _e( 'Your available profile pictures', 'wmpp' ) ?>
    <br><br>
	<?php } ?>

	<?php foreach ( $rest_pictures as $picture ) { ?>
        <div class="inline-block">
			<?php _e( 'Set as main', 'wmpp' ) ?> <input type="radio" name="main"
                                                        value="<?php echo $picture['mpp_user_picture_id'] ?>"><br>
            <img class="border-1px inline-block"
                 src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/users/{$picture['pic_name']}" ?>">
            <small class="block negative-top-margin-10px">ID: <?php echo $picture['mpp_user_picture_id'] ?></small>
            <label><?php _e( 'Delete', 'wmpp' ) ?></label>
            <input type="checkbox" name="remove[]" value="<?php echo $picture['mpp_user_picture_id'] ?>">
        </div>
	<?php } ?>
	<?php if ( ! empty( $rest_pictures ) ) { ?>
</div>
<?php } ?>

<br><br>
<input type="hidden" name="wmpp">
<strong><?php _e( 'Click "Save changes" to apply changes', 'wmpp' ) ?></strong>