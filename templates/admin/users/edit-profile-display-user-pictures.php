<?php
/**
 * @var $main_picture string
 * @var $rest_pictures array
 */
?>

<table class="form-table">
    <tbody>
    <tr>
        <th><?php _e( 'Main Customer Picture', 'wmpp' ) ?></th>
        <td>
			<?php if ( ! empty( $main_picture_url ) ) { ?>
                <div class="padding-05-em inline-block">
                    <img class="border-1px " alt="Main picture" src="<?php echo $main_picture_url ?>" width="150px"><br>
                </div>
			<?php } ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Customer\'s other pictures', 'wmpp' ) ?></th>
        <td>
			<?php foreach ( $rest_pictures as $picture ) { ?>
                <div class="inline-block">
                    <img class="border-1px inline-block" src="<?php echo $picture->guid ?>" width="150px">
                </div>
			<?php } ?>
        </td>
    </tr>
    </tbody>
</table>