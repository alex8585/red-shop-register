<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/includes
 * @author     Your Name <email@example.com>
 */
class Red_Shop_Register_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$loginPageId = get_option('login_page_id');
        wp_delete_post($loginPageId );
        delete_option('login_page_id');

        $registerPageId = get_option('register_page_id');
        wp_delete_post( $registerPageId);
        delete_option('register_page_id');
	}

}
