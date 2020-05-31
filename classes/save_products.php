<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once("storing_products.php");

use WSPO\NOTICES;
use WSPO\STORING_PRODUCTS;

if(isset($_POST['savingProducts'])) {
    $prefix = "product_";
    for($i = 1; $i < 11; $i++) {
        $product = array();
        if(isset($_POST[$prefix.$i.'_checked']) && $_POST[$prefix.$i.'_checked'] == "on") {
            $product['title']     = $_POST[$prefix.$i.'_title'] ?? "";
            $product['content']   = $_POST[$prefix.$i.'_description'] ?? "";
            $product['type']      = $_POST[$prefix.$i.'_type'] ?? "";
            $product['price']     = $_POST[$prefix.$i.'_price'] ?? "";
            $product['sku']       = $_POST[$prefix.$i.'_sku'] ?? "";
            $product['thumbnail'] = $_POST[$prefix.$i.'_img'] ?? "";
            $product['author']    = \intval(\wp_get_current_user()->data->ID);

            if(isset($_POST[$prefix.$i.'_categories'])) {
                foreach($_POST[$prefix.$i.'_categories'] as $category) {
                    $tmp = \explode('&*&', $category, 2);
                    if(\count($tmp) == 2) {
                        $product['categories'][$tmp[0]] = $tmp[1];
                    }
                }
            }
            if(isset($_POST[$prefix.$i.'_attributes'])) {
                foreach($_POST[$prefix.$i.'_attributes'] as $attribute) {
                    $product['attributes'][$attribute] = array();
                    if(isset($_POST[$prefix.$i.'_attributes_variations'])) {
                        foreach($_POST[$prefix.$i.'_attributes_variations'] as $variation) {
                            $product['attributes'][$attribute][] = $variation;
                        }
                    }
                }
            }
        } 
        if(\count($product) > 0) {
            $stored = STORING_PRODUCTS::store_product($product);
            require_once("notices.php");
            if(is_string($stored)) {
                NOTICES::create_attributes_first($stored);
                break;
            }
            if(is_int($stored))                
                NOTICES::synced_done();

            if(is_null($stored))
                NOTICES::type_not_recognized();

            if($stored == false)
                NOTICES::synced_failed();
        }     
    }
}


