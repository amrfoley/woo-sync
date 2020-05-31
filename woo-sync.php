<?php
/**
 * Plugin Name: Woo Sync Products and Orders
 * Plugin URI: 
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Amr Foley
 * Author URI: 
 */

namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WSPO\SETTINGS; 

class WooSync {

    protected static $Woo = null;

	public static function instance() {
		if ( is_null( self::$Woo ) ) {
			self::$Woo = new self();
		}
		return self::$Woo;
    }
    
    public function __construct() {

        require_once( 'classes/settings.php' );
        SETTINGS::init();

        require_once( 'classes/admin.php' );
        add_action( 'init', array( 'WSPO\ADMIN', 'init' ) );

        require_once( 'classes/register.php' );
        add_action( 'init', array( 'WSPO\REGISTER', 'init' ) );
    }
}


function WSPO() {
	return WooSync::instance();
}

WSPO();