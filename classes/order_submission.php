<?php 
namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( 'requests.php' );

use WSPO\REQUESTS;

class ORDER_SUBMISSION {

    public static function new_order_created($order_id) {
        $order_data = array();
        $order = \wc_get_order($order_id);

        $items = $order->get_items();
		foreach ( $items as $item ) {
            $item_obj = array();

            $item_sku = get_post_meta(\intval($item->get_product_id()), "WSPO_sku");
            if(\count($item_sku) > 0)
                $product_id = self::validate_sku($item_sku[0]);

            if($product_id)
                $item_obj = REQUESTS::get_product_data_by_id($product_id);

            if(isset($item_obj) && \count($item_obj) > 0) {

                $variation = new \WC_Product_Variation($item->get_variation_id());
                $variationName = implode(" / ", $variation->get_variation_attributes());
                $variation_id = 0;

                foreach($item_obj['attributes'][0]['options'] as $key => $_variation) {
                    if($variationName === $_variation)
                        $variation_id = $key;
                }

                if(\count($item_obj['variations']) > 0){
                    if(isset($item_obj['variations'][$$variation_id])) {
                        $order_data['line_items'][] = array(
                            'product_id' => $item_obj['id'],
                            'variation_id' => $item_obj['variations'][$$variation_id],
                            'quantity' => $item->get_quantity()
                        );
                    }
                } else {
                    $order_data['line_items'][] = array(
                        'product_id' => $item_obj['id'],
                        'quantity' => $item->get_quantity()
                    );
                }
            }
        }
        
        $order_data['billing'] = array(
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'state' => $order->get_billing_state(),
            'postcode' => $order->get_billing_postcode(),
            'country' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone()
        );

        $order_data['shipping'] = array(
            'first_name' => $order->get_shipping_first_name(),
            'last_name' => $order->get_shipping_last_name(),
            'address_1' => $order->get_shipping_address_1(),
            'address_2' => $order->get_shipping_address_2(),
            'city' => $order->get_shipping_city(),
            'state' => $order->get_shipping_state(),
            'postcode' => $order->get_shipping_postcode(),
            'country' => $order->get_shipping_country()
        );

        $order_data['payment_method'] = $order->get_payment_method();
        $order_data['payment_method_title'] = $order->get_payment_method_title();

        $order_id = REQUESTS::send_order_items($order_data);
    }

    public static function validate_sku($sku) {
        $temp = \explode('-', $sku, 2);
        if(\count($temp) == 2) {
            if($temp[0] === 'WSPO' && is_numeric($temp[1])) 
                return $temp[1];
            else
                return false;
        }
    }
}