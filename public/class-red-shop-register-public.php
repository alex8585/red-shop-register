<?php


class Red_Shop_Register_Public {

	private $red_shop_register;
	private $version;

	public function __construct( $red_shop_register, $version ) {
		$this->view = new Red_Shop_Register_View();
		$this->red_shop_register = $red_shop_register;
		$this->version = $version;
		$this->flash = new Red_Shop_Register_Flash();
		$this->auth = new Red_Shop_Register_Auth();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->red_shop_register, plugin_dir_url( __FILE__ ) . 'css/red-shop-register-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->red_shop_register, plugin_dir_url( __FILE__ ) . 'js/red-shop-register-public.js', array( 'jquery' ), $this->version, false );

	}

	public function plugin_init() {
	
		
	}


	public function wp_init() {
		
		remove_action( 'register_new_user', 'wp_send_new_user_notifications' );

		add_shortcode('rsr_register_form', array($this, 'rsr_register_form'));
		add_shortcode('rsr_confirm_email', array($this, 'rsr_confirm_email'));
		add_shortcode('rsr_login_form', array($this, 'rsr_login_form'));

		add_filter( 'wp_nav_menu_items', [$this,'add_loginout_link'], 10, 2 );
		add_action( 'woocommerce_before_checkout_billing_form', [$this,'woocommerce_before_checkout_billing_form'],1,1);
		add_filter('woocommerce_form_field_args', [$this,'woocommerce_form_field_args'],1,3);
		//add_filter('woocommerce_checkout_fields', [$this,'woocommerce_checkout_fields'],1,3);
		add_filter('user_register', [$this,'user_register'],1,1);
		add_filter( 'authenticate' , [$this,'authenticate'],99,3);
		add_action( 'rsr_login_failed', [$this,'rsr_login_failed'],1,2);
		
	}

	public function authenticate($user, $username, $password) {
		if(!$user || is_wp_error($user) || $this->is_admin($user)) { return $user; }

		$error = new WP_Error();
		$user = get_user_by( 'login', $username );
		
		$confirmed = get_user_meta($user->ID, 'confirmed_user', true);
		
		if(!$confirmed) {
			$error->add('not_confirmed', __( 'Email not confirmed', RSR) );
		}
		
		if($error->has_errors()) { return $error; }

		return $user;
	}

	public function rsr_login_failed($username, $errors) {
		$redirect = get_permalink( get_option('login_page_id'));
		$user = get_user_by( 'login', $username );

		if( !$user ) {
			$this->flash->setErrors($errors);
			wp_safe_redirect( $redirect );
			exit();
		}

		if($this->is_admin($user)) { return; }

		$this->flash->setErrors($errors);
		wp_safe_redirect( $redirect );
		exit();
		
	}


	public function user_register($user_id) {
		//dd($user_id);
	}

	public function woocommerce_checkout_fields($fields) {
		unset($fields['shipping']);
		unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_country']);
		if(is_user_logged_in()) {
			unset($fields['billing']['billing_email']);
		}
		//unset($fields['billing']['billing_address_2']);
		
		//dd($fields);
		return $fields;
	}


	public function woocommerce_form_field_args($args, $key, $value) {
		$args['class'] = ['form-group'];
		$args['input_class'] = ['form-control'];
		return $args;
	}



	public function woocommerce_before_checkout_billing_form($checkout) {
		
		//dd($checkout);
	}
	
	public function wp() {
		if(!is_admin()) {
			$this->register_user();
			$this->confirm_email_redirect();
			$this->login_redirects();
		}
	}

	public function login_redirects() {
		global $post;
		if(!$post) {return;}
		if($post->ID == get_option('woocommerce_myaccount_page_id')) {
			if(!is_user_logged_in()) {
				$redirect = get_permalink( get_option('login_page_id'));
				//wp_safe_redirect( $redirect );
				//exit();
			}
		}

		$is_login_page = ( $post->ID == get_option('login_page_id') );

		if($is_login_page && is_user_logged_in()) {
			$redirect = get_permalink( get_option('woocommerce_myaccount_page_id'));
			wp_safe_redirect( $redirect );
			exit();
		}
	}


	public function rsr_login_form() {
		
		$redirect = get_permalink( get_option('woocommerce_myaccount_page_id'));
		$confirm_page_url = get_permalink( get_option('confirm_page_id'));
		$errors = $this->flash->getErrors();
		$messages = $this->flash->getMessages();
		$email = $_POST['user_email'] ?? '';
		$password = $_POST['user_pass'] ?? '';

		$not_confirmed = false;
		if(is_wp_error($errors)){
			$not_confirmed =  ( array_search('not_confirmed', $errors->get_error_codes()) !== false );
		} 
		//dd($not_confirmed);
		return $this->view->render(
			plugin_dir_path(  __FILE__  ) . '/templates/login_form.php',
			[
				'email' =>$email,
				'password'=> $password,
				'errors' => $errors,
				'messages' => $messages,
				'redirect'=>$redirect,
				'not_confirmed'=>$not_confirmed,
				'confirm_page_url'=>$confirm_page_url
			]
		);	
		
	}



	public function confirm_email_redirect() {
		
		$action = isset( $_GET['rsr_action']) ? $_GET['rsr_action'] :'';
		if($action != 'confirm_email')  { return; }

		if ( isset( $_GET['key'] ) ) {
			list( $confirm_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
			$confirm_cookie       = 'confirm-user-' . COOKIEHASH;
		
			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
			setcookie( $confirm_cookie, $value, 0, $confirm_path, COOKIE_DOMAIN, is_ssl(), true );
			wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
			exit;
		}
		
	}
	

	public function register_user() {
		global $post;
		if(!$post) {return;}
		$is_login_page = ( $post->ID == get_option('register_page_id') );

		if($is_login_page && is_user_logged_in()) {
			$redirect = get_permalink( get_option('woocommerce_myaccount_page_id'));
			wp_safe_redirect( $redirect );
			exit();
		}

		$http_post = ( $_SERVER['REQUEST_METHOD'] == 'POST' );
		$action = isset($_POST['rsr_action']) ? $_POST['rsr_action'] :'';

		if(!$http_post || !($action == 'register_form' ) ) {  return;  }
		
		$redirect = get_page( get_option('register_page_id'))->guid;

		//if($redirect != get_post()->guid ) { return; }

		$user_login = '';
		$user_email = '';

		if ( isset( $_POST['user_email'] ) && is_string( $_POST['user_email'] ) ) {
			$user_email = wp_unslash( $_POST['user_email'] );
			$user_login = $user_email;
		}
		
		$user_id = register_new_user( $user_login, $user_email );
		
		if ( ! is_wp_error( $user_id ) ) {
			
			if ( isset( $_POST['user_pass'] ) ) {
				wp_set_password( $_POST['user_pass'], $user_id );
			}
	
			$this->flash->setMessages(['success'=> __( 'Registration is complete. Check your mail for confirmation.', 'red-shop-register') ]);
			$this->auth->send_confirmation_email($user_id);
			//$WC_Emails = new WC_Emails();
			//$WC_Emails->customer_new_account( $user_id);
			update_user_meta($user_id,'confirmed_user',0);
			wp_safe_redirect( $redirect );
			exit();
		} else {
			$wp_errors = $user_id;
			$this->flash->setErrors($wp_errors);
		}
		
	}
	

	public function rsr_confirm_email() {
		$error = false;
		$action = isset($_GET['rsr_action']) ? $_GET['rsr_action'] :'';
		if( $_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['user_email']))  {
				$login = $_POST['user_email'];
				$user = get_user_by( 'login', $login );
				if($user) {
					$confirmed = get_user_meta($user->ID, 'confirmed_user', true);
					if( $confirmed ) {
						$this->flash->setMessages(['success'=> __( 'Email has already been confirmed', 'red-shop-register') ]);
					} else {
						$this->auth->send_confirmation_email($user->ID);
						$this->flash->setMessages(['success'=> __( 'Check your mail for confirmation.', 'red-shop-register')]);
					}
				} else {
					$error = new WP_Error( 'invalid_email', __( 'Email not register', 'red-shop-register' ) );
				}
			}
		} elseif($action == 'confirm_email') {
			$confirm_cookie       = 'confirm-user-' . COOKIEHASH;
			if ( isset( $_COOKIE[ $confirm_cookie ] ) && 0 < strpos( $_COOKIE[ $confirm_cookie ], ':' ) ) {
				list( $login, $key ) = explode( ':', wp_unslash( $_COOKIE[ $confirm_cookie ] ), 2 );
			} 

			if ( empty( $login ) || ! is_string( $login ) ) {
				$error = new WP_Error( 'invalid_key', __( 'Invalid key' ) );
			}

			$user = get_user_by( 'login', $login );
			if( !$user ) {
				$error = new WP_Error( 'invalid_key', __( 'Invalid key' ) );
			} else {
				$confirmed = get_user_meta($user->ID, 'confirmed_user', true);
				if( $confirmed ) {
					$this->flash->setMessages(['success'=> __( 'Email has already been confirmed', 'red-shop-register') ]);
				} else {
					
					$result  = $this->auth->check_confirm_key( $key, $user );
		
					if(is_wp_error($result ) ){
						$error = $result;
					} else {
						update_user_meta($user->ID, 'confirmation_hash', '');
						update_user_meta($user->ID, 'confirmed_user', 1);
						$this->flash->setMessages(['success'=> __( 'Email has been confirmed', 'red-shop-register') ]);
					}
				}
			}
		}
		

		$this->flash->setErrors($error);
		
		$redirect = get_page( get_option('confirm_page_id'))->guid;
		$errors = $this->flash->getErrors();
		$messages = $this->flash->getMessages();
		
		return $this->view->render(
			plugin_dir_path(  __FILE__  ) . '/templates/confirm_page.php',
			[
				'errors' => $errors,
				'messages' => $messages,
				'redirect'=>$redirect,
			]
		);	
		
	}

	public function rsr_register_form() {
		
		$redirect = get_permalink( get_option('register_page_id'));
		$errors = $this->flash->getErrors();
		$messages = $this->flash->getMessages();

		$email = $_POST['user_email'] ?? '';
		$password = $_POST['user_pass'] ?? '';
		return $this->view->render(
			plugin_dir_path(  __FILE__  ) . '/templates/register_form.php',
			[
				'email' => $email,
				'password' => $password,
				'errors' => $errors,
				'messages' => $messages,
				'redirect'=>$redirect
			]
		);	
		
	}
	
	public function add_loginout_link( $items, $args ) {
		$login_url = get_permalink( get_option('login_page_id') ); 
		//print_r($login_url);die;
		if (is_user_logged_in() && $args->menu->name == 'top') {
			$items .= '<li><a href="'. wp_logout_url($login_url) .'">Log Out</a></li>';
		}
		elseif (!is_user_logged_in() && $args->menu->name == 'top') {
			
			$items .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . 
			$login_url .'">' . __( 'Log In', 'red-shop-register') . '</a></li>';
		}
		//print_r($args);die;
		return $items;
	}

	private function is_admin($user) {
		$user_meta = get_userdata($user->ID);
		$user_roles = $user_meta->roles;
		
		if( in_array("administrator", $user_roles) ) {
			return true;
		}
		return false;
	}
}
