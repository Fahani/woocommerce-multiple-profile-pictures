<?php

namespace WMPP\front;

use WMPP\database\Repository;
use WMPP\helpers\Utils;
use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * This class will take care of insert the profile pictures functionality into the Account details page of the user
 * @since 1.0.0
 */
class MultiUpload implements RegisterAction {

	/** @var array */
	protected $valid_mimes;

	/** @var array */
	protected $valid_formats;

	/** @var Repository */
	private $repository;

	/**
	 * Initializes class attributes
	 *
	 * @param Repository $repository
	 *
	 * @since 1.0.0
	 */
	public function __construct( Repository $repository ) {
		$this->repository    = $repository;
		$this->valid_mimes   = [ 'image/png', 'image/jpeg', 'image/bmp', 'image/x-ms-bmp' ];
		$this->valid_formats = [ 'png', 'jpeg', 'bmp' ];
	}

	/**
	 * Registers the actions to insert the picture inside the Account details form
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'template_redirect', [ $this, 'update_profile' ], 5 );
		add_action( 'woocommerce_before_edit_account_form', [ $this, 'enqueue_assets' ] );
		add_action( 'woocommerce_edit_account_form_tag', [ $this, 'add_multipart_to_form' ] );
		add_action( 'woocommerce_edit_account_form', [ $this, 'add_picture_selection_to_form' ] );
	}

	/**
	 * It loads assets into the page
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'wmpp_style', WMPP_PLUGIN_URL . '/assets/css/style.css' );
	}

	/**
	 * Process user's action when submitting the form. Using wmpp post variable to make sure it is our form.
	 * @return void
	 * @since 1.0.0
	 */
	public function update_profile() {
		if ( isset ( $_POST["wmpp"] ) ) {
			$this->delete_selected_pictures();
			$this->set_main_picture();
			$this->upload_new_picture();
		}
	}

	/**
	 * Deletes from folder and from db the checked pictures by the user
	 * @return void
	 * @since 1.0.0
	 */
	private function delete_selected_pictures() {
		if ( isset( $_POST['remove'] ) ) {
			foreach ( $_POST['remove'] as $pic_id ) {
				$pic_to_remove = $this->repository->get_picture_by_picture_id_and_user_id( $pic_id, wp_get_current_user()->ID );

				if ( empty( $pic_to_remove ) ) {
					wc_add_notice(
						sprintf(
							__( 'You can not delete a picture with id(%s)', 'wmpp' ),
							$pic_id
						),
						'error' );
					continue;
				}

				if ( ! $this->delete_from_uploads( $pic_to_remove[0]['pic_name'] ) ) {
					wc_add_notice(
						sprintf(
							__( 'There was an error deleting the picture id(%s)', 'wmpp' ),
							$pic_id ),
						'error' );
					continue;
				}

				$this->repository->delete_picture_by_picture_id( $pic_id );


				wc_add_notice( sprintf( __( 'Picture deleted id(%s)', 'wmpp' ), $pic_id ), 'success' );
			}
		}
	}

	/**
	 * Removes a file from the the path: wp-content/uploads/wmpp/users
	 *
	 * @param string $filename
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function delete_from_uploads( $filename ) {
		return unlink( wp_upload_dir()['basedir'] . '/wmpp/users/' . $filename );
	}

	/**
	 * It sets as main profile picture the one selected by the user
	 * @return void
	 * @since 1.0.0
	 */
	private function set_main_picture() {
		if ( isset ( $_POST['main'] ) ) {
			$pic_to_set_main = $this->repository->get_picture_by_picture_id_and_user_id( $_POST["main"], wp_get_current_user()->ID );
			if ( empty( $pic_to_set_main ) ) {
				wc_add_notice(
					sprintf(
						__( 'The picture id(%s) can not be set as main picture, it does not exist', 'wmpp' ),
						$_POST['main']
					)
					, 'error' );
			} else {
				$this->repository->unset_main_picture_by_user_id( wp_get_current_user()->ID );
				$this->repository->set_main_picture_by_picture_id( $_POST['main'] );

				wc_add_notice(
					sprintf(
						__( 'Picture id(%s) set as your main picture.', 'wmpp' ),
						$_POST['main']
					),
					'success' );
			}
		}
	}

	/**
	 * This function takes care of the action of uploading a new picture
	 * @return void
	 * @since 1.0.0
	 */
	private function upload_new_picture() {
		if ( isset ( $_FILES['profile_pictures'] ) && $_FILES['profile_pictures']['size'][0] > 0 ) {

			$num_pics = count( $_FILES['profile_pictures']['size'] );

			for ( $i = 0; $i < $num_pics; $i ++ ) {
				$name      = $_FILES['profile_pictures']['name'][ $i ];
				$temp_name = $_FILES['profile_pictures']['tmp_name'][ $i ];

				$num_pics_user = $this->repository->get_number_pics_by_user( wp_get_current_user()->ID );
				$num_max_pics  = get_option( 'max_profile_pictures' );

				if ( $num_max_pics != - 1 && $num_pics_user >= $num_max_pics ) {
					wc_add_notice(
						sprintf(
							__( 'You have reached the maximum of pics you can upload (%s). %s will not be uploaded', 'wmpp' ),
							$num_max_pics, $name
						)
						, 'error' );
					break;
				}

				$picture_id = $this->save_picture( $temp_name );

				if ( $picture_id === false ) {
					continue;
				}

				wc_add_notice( sprintf( __( 'Your picture %s has been uploaded', 'wmpp' ), $name ), 'success' );
			}
		}
	}

	/**
	 * This function takes of saving the picture into the uploads folder and into the wp_woocommerce_mpp_user_picture table
	 *
	 * @param string $temp_name
	 *
	 * @return false|int
	 */
	private function save_picture( $temp_name ) {
		$mime = wp_get_image_mime( $temp_name );

		if ( ! in_array( $mime, $this->valid_mimes ) ) {
			wc_add_notice(
				sprintf(
					__( 'Invalid image format, please try one of these formats: %s', 'wmpp' ),
					implode( ', ', $this->valid_formats )
				)
				, 'error' );

			return false;
		}

		$editor = wp_get_image_editor( $temp_name );

		if ( is_wp_error( $editor ) ) {
			wc_add_notice( $editor->get_error_message(), 'error' );

			return false;
		}

		$result = $editor->resize( 150, 150, false );

		if ( is_wp_error( $result ) ) {
			wc_add_notice( $editor->get_error_message(), 'error' );

			return false;
		}

		$final_name = Utils::generate_name( $mime );

		$result = $editor->save( wp_upload_dir()['basedir'] . "/wmpp/users/$final_name" );

		if ( is_wp_error( $result ) ) {
			wc_add_notice( $editor->get_error_message(), 'error' );

			return false;
		}

		return $this->repository->insert_picture( wp_get_current_user()->ID, "$final_name", $mime, 0 );
	}

	/**
	 * Loads the encoding multi part attribute into the form from a template
	 * @return void
	 * @since 1.0.0
	 */
	public function add_multipart_to_form() {
		include( WMPP_DIR_PATH . 'templates/myaccount/add-multi-part-to-form.php' );
	}

	/**
	 * Loads the template that will display the information about the user's pictures and allow him to upload,
	 * delete and select a main picture
	 * @return void
	 * @since 1.0.0
	 */
	public function add_picture_selection_to_form() {
		$main_picture  = $this->repository->get_main_picture_by_user_id( wp_get_current_user()->ID );
		$num_pics_user = $this->repository->get_number_pics_by_user( wp_get_current_user()->ID );
		$num_max_pics  = get_option( 'max_profile_pictures' );
		$rest_pictures = $this->repository->get_no_main_pictures_by_user_id( wp_get_current_user()->ID );
		include( WMPP_DIR_PATH . 'templates/myaccount/add-picture-selection-to-form.php' );
	}

}