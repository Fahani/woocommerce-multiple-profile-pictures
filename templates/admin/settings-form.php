<div class="wrap">
    <h1><?php echo MultipleProfilePictures::PLUGIN_NAME ?></h1>
	<?php settings_errors(); ?>

    <form method="post" action="options.php">
	    <?php settings_fields( 'wmpp_options_group' ); ?>
	    <?php do_settings_sections( 'wmpp_settings' ); ?>
	    <?php submit_button(); ?>
    </form>
</div>