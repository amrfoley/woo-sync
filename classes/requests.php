<?php 
namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WSPO\SETTINGS;

class REQUESTS {
    private static $rest_api_url = 'wp-json/wc/v3/products/';

    const CATEGORY_URL = "categories?per_page=50&hide_empty=true";    

    private static function remote_connection($url, $method = 'GET', $data = array()) {

        $wp_request_headers = array(
            'Authorization' => 'Basic ' . base64_encode(SETTINGS::get_key().':'.SETTINGS::get_secret())
        );

        if(\count($data) > 0) {
            $data_array = array(
                'method' => $method,
                'headers' => $wp_request_headers,
                'body' => $data
            );
        } else {
            $data_array = array(
                'method' => $method,
                'headers' => $wp_request_headers,
            );
        }

        return self::dencode_response(wp_remote_request($url, $data_array));
    }

    private static function dencode_response($response) {
        if(isset($response) && is_array($response)) {
            if($response["response"]["code"] === 200) {
                $decoded_response = json_decode($response["body"], true);
            } else {
                $error = json_decode($response["body"], true);
                $decoded_response = $error['message'];
            }
            return $decoded_response;
        }

        return false;
        
    }
    
    public static function get_categories($url) {
        $wp_request_url = SETTINGS::get_store_url().self::$rest_api_url.$url;
        return self::remote_connection($wp_request_url);
    }    

    public static function get_category_products($category, $page = 1) {
        
        if($category == "") return false;

        $wp_request_url = SETTINGS::get_store_url().self::$rest_api_url."?status=publish&page=$page&category=".$category;
        return self::remote_connection($wp_request_url);
    }

    public static function get_attribute_data($id) {
        $wp_request_url = SETTINGS::get_store_url().self::$rest_api_url."attributes/".$id;
        return self::remote_connection($wp_request_url);
    }

    public static function get_product_variations(int $product_id, int $variation_id) {
        $wp_request_url = SETTINGS::get_store_url().self::$rest_api_url.$product_id."\/variations/".$variation_id;
        return self::remote_connection($wp_request_url);
    }

    public static function get_product_data_by_id($id) {
        return self::remote_connection(SETTINGS::get_store_url().self::$rest_api_url.$id) ?? [];
    }

    public static function send_order_items(array $order) {

        if(\count($order) > 0) {
            $wp_request_url = SETTINGS::get_store_url()."wp-json/wc/v3/orders";
            $orderData = self::remote_connection($wp_request_url, "POST", $order);
        }

        return $orderData ?? false;
    }

}