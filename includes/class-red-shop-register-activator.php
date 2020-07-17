<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/includes
 * @author     Your Name <email@example.com>
 */
class Red_Shop_Register_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        update_option('woocommerce_ship_to_destination', 'billing_only');

		$loginPage = array(
            'post_title' => __('Login page', 'login-page'),
            'post_content' => '[rsr_login_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
            'post_date' => date('Y-m-d h:i:s')
        );
        $loginPostId = wp_insert_post($loginPage);
        update_option('login_page_id', $loginPostId);
        update_post_meta( $loginPostId, '_wp_page_template', 'template-blank-1.php' );

        $registerPage = array(
            'post_title' => __('Register page', 'register-page'),
            'post_content' => '[rsr_register_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
            'post_date' => date('Y-m-d h:i:s')
        );
        $registerPostId = wp_insert_post($registerPage);
        update_option('register_page_id', $registerPostId);
        update_post_meta( $registerPostId, '_wp_page_template', 'template-blank-1.php' );

        $confirmPage = array(
            'post_title' => __('Confirm email', 'confirm-email-page'),
            'post_content' => '[rsr_confirm_email]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => get_current_user_id(),
            'post_date' => date('Y-m-d h:i:s')
        );
        $confirmPostId = wp_insert_post($confirmPage);
        update_option('confirm_page_id',  $confirmPostId);
        update_post_meta(  $confirmPostId, '_wp_page_template', 'template-blank-1.php' );
	}

}
