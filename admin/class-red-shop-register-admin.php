<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Red_Shop_Register
 * @subpackage Red_Shop_Register/admin
 * @author     Your Name <email@example.com>
 */
class Red_Shop_Register_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $red_shop_register    The ID of this plugin.
	 */
	private $red_shop_register;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $red_shop_register       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $red_shop_register, $version ) {

		$this->red_shop_register = $red_shop_register;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Red_Shop_Register_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Red_Shop_Register_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->red_shop_register, plugin_dir_url( __FILE__ ) . 'css/red-shop-register-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Red_Shop_Register_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Red_Shop_Register_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->red_shop_register, plugin_dir_url( __FILE__ ) . 'js/red-shop-register-admin.js', array( 'jquery' ), $this->version, false );

	}

}
