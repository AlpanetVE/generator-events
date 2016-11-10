<?php
/**
 * WooCommerce Auth
 *
 * Handles wc-auth endpoint requests.
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce/API
 * @since    2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GE_Event' ) ) :

class GE_Event {

	/**
	 * Setup class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Register auth endpoint
		add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );

		// Handle auth requests
		add_action( 'parse_request', array( $this, 'handle_auth_requests' ), 0 );

	}

	public function add_query_vars( $vars ) {
		$vars[] = 'wc-auth-version';
		$vars[] = 'wc-auth-route';
		$vars[] = 'generateEvent';
		return $vars;
	}

	/**
	 * View
	 *
	 * @since 1.0.0
	 */
	public static function add_endpoint()
	{
	    add_rewrite_rule( 'generator-events-shortcodes.php$', 'index.php?generateEvent=1', 'top' );
	}

/*
	static function add_endpoint() {
		add_rewrite_rule( '^wc-auth/v([1]{1})/(.*)?', 'index.php?wc-auth-version=$matches[1]&wc-auth-route=$matches[2]', 'top' );
	}*/


	/**
	 * Handle auth requests.
	 *
	 * @since 2.4.0
	 */
	public function handle_auth_requests() {
		global $wp;
		if ( array_key_exists( 'generateEvent', $wp->query_vars ) ) {
		        var_dump("seeeeeeseeeeeeeee");
		    }
		return;
	}

}

endif;

return new GE_Event();
