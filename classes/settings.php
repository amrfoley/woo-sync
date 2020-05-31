<?php

namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SETTINGS {
    private static $username;    
    private static $password; 
    private static $site_url;
    private static $status;
    private static $categories;
    private static $thumbnails;
    private static $attributes;
    private static $variations;
    private static $send_orders;

    public static function init() {
        $options = get_option( 'WSPO_settings' );
        self::$site_url = $options['WSPO_store_url'] ?? "";
        self::$username = $options['WSPO_consumer_key'] ?? "";
        self::$password = $options['WSPO_consumer_secret'] ?? "";
        self::$status = $options['WSPO_default_status'] ?? "draft";
        self::$categories = $options['WSPO_import_categories'] ?? false;
        self::$thumbnails = $options['WSPO_import_thumbnails'] ?? false;
        self::$attributes = $options['WSPO_import_attributes'] ?? false;
        self::$variations = $options['WSPO_import_variations'] ?? false;
        self::$send_orders = $options['WSPO_send_orders'] ?? false;
    }

    public static function get_key() {
        return self::$username;
    }
    public static function get_secret() {
        return self::$password;
    }
    public static function get_store_url() {
        return self::$site_url;
    }
    public static function get_default_status() {
        return self::$status;
    }
    public static function get_attributes_choice() {
        return self::$attributes;
    }
    public static function get_variations_choice() {
        return self::$variations;
    }
    public static function get_thunmbail_choice() {
        return self::$thumbnails;
    }
    public static function get_categories_choice() {
        return self::$categories;
    }
    public static function get_sending_orders_choice() {
        return self::$send_orders;
    }
}