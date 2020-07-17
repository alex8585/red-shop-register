<?php


class Red_Shop_Register_Auth {

	
    public function send_confirmation_email($user_id) {
        $user = get_user_by( 'id', $user_id );
        $url = get_page( get_option('confirm_page_id'))->guid;

        $key = wp_generate_password( 20, false );

        if ( empty( $wp_hasher ) ) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            $wp_hasher = new PasswordHash( 8, true );
        }
        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
        update_user_meta($user_id,'confirmation_hash',$hashed);
        //$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

        $switched_locale = switch_to_locale( get_user_locale( $user ) );

        /* translators: %s: user login */
        $message  = sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
        $message .= __( 'Confirm your mail.', 'red-shop-register') . "\r\n\r\n";
        $message .=   "{$url}?rsr_action=confirm_email&key=$key&login=" . rawurlencode( $user->user_login ) . "\r\n\r\n";

        
        $wp_new_user_notification_email = array(
            'to'      => $user->user_email,
            'subject' =>  __( 'Confirm your mail.', 'red-shop-register'),
            'message' => $message,
            'headers' => '',
        );
		//print_r($wp_new_user_notification_email);die;
        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

        wp_mail(
            $wp_new_user_notification_email['to'],
            wp_specialchars_decode( sprintf( $wp_new_user_notification_email['subject'], $blogname ) ),
            $wp_new_user_notification_email['message'],
            $wp_new_user_notification_email['headers']
        );

        if ( $switched_locale ) {
            restore_previous_locale();
        }
    }
    
    public function check_confirm_key( $key, $user ) {
	
		global  $wp_hasher;
	
		$key = preg_replace( '/[^a-z0-9]/i', '', $key );
	
		if ( empty( $key ) || ! is_string( $key ) ) {
			return new WP_Error( 'invalid_key', __( 'Invalid key', 'red-shop-register' ) );
		}
	
		
		$db_key = get_user_meta($user->ID,'confirmation_hash',true);
		if ( ! $db_key ) {
			return new WP_Error( 'invalid_key', __( 'Invalid key', 'red-shop-register' ) );
		}
		
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
	
		
		$expiration_duration = 300;
	
		if ( false !== strpos( $db_key, ':' ) ) {
			list( $pass_request_time, $pass_key ) = explode( ':', $db_key, 2 );
			$expiration_time                      = $pass_request_time + $expiration_duration;
		} else {
			$pass_key        = $db_key;
			$expiration_time = false;
		}
		
		if ( ! $pass_key ) {
			return new WP_Error( 'invalid_key', __( 'Invalid key', 'red-shop-register' ) );
		}
		
		$hash_is_correct = $wp_hasher->CheckPassword( $key, $pass_key );
		
		if ( $hash_is_correct && $expiration_time && time() < $expiration_time ) {
			return $user->ID;
		} elseif ( $hash_is_correct && $expiration_time ) {
			// Key has an expiration time that's passed
			return new WP_Error( 'expired_key', __( 'Expired key' , 'red-shop-register') );
		}
		
		if ( hash_equals( $db_key, $key ) || ( $hash_is_correct && ! $expiration_time ) ) {
			return new WP_Error( 'expired_key', __( 'Expired key' , 'red-shop-register') );
		}
	
		return new WP_Error( 'invalid_key', __( 'Invalid key', 'red-shop-register' ) );
	}

}
