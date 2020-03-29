<?php
/**
 * @var $main_picture array
 * @var $rest_pictures array
 */
?>

<table class="form-table">
    <tbody>
    <tr>
        <th><?php _e( 'Main Customer Picture', 'wmpp' ) ?></th>
        <td>
			<?php if ( ! empty( $main_picture ) ) { ?>
                <div class="padding-05-em inline-block">
                    <img class="border-1px " alt="Main picture"
                         src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/users/{$main_picture[0]['pic_name']}" ?>"><br>
                    <small class="block">ID: <?php echo $main_picture[0]['mpp_user_picture_id'] ?></small>
                </div>
			<?php } ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Customer\'s other pictures', 'wmpp' ) ?></th>
        <td>
			<?php foreach ( $rest_pictures as $picture ) { ?>
                <div class="inline-block">

                    <img class="border-1px inline-block"
                         src="<?php echo wp_upload_dir()['baseurl'] . "/wmpp/users/{$picture['pic_name']}" ?>">
                    <small class="block">ID: <?php echo $picture['mpp_user_picture_id'] ?></small>
                </div>
			<?php } ?>
        </td>
    </tr>
    </tbody>
</table>