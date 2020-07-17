<?php

define("RSR", 'red-shop-register');





if(function_exists('print_filters_for') ) {
    function print_filters_for( $hook = '' ) {
        global $wp_filter;
        if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
            return;
    
        print '<pre>';
        print_r( $wp_filter[$hook] );
        print '</pre>';
    }
}

if ( ! function_exists( 'wp_authenticate' ) ) {
    function wp_authenticate( $username, $password ) {
        
        $username = sanitize_user( $username );
        $password = trim( $password );
        
        
        $user = apply_filters( 'authenticate', null, $username, $password );
        //dd($user);
        if ( $user == null ) {
            // TODO what should the error message be? (Or would these even happen?)
            // Only needed if all authentication handlers fail to return anything.
            $user = new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: Invalid username, email address or incorrect password.' ) );
        }

        $ignore_codes = array( 'empty_username', 'empty_password' );

        if ( is_wp_error( $user ) && ! in_array( $user->get_error_code(), $ignore_codes ) ) {
            $errors = $user;
            do_action( 'wp_login_failed', $username );
            
            do_action( 'rsr_login_failed', $username, $errors );
        }
        //dd($user);
        return $user;
    }
}