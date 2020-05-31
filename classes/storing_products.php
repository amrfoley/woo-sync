<?php

namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WSPO\SETTINGS;
use WSPO\REQUESTS;
use WSPO\NOTICES;

class STORING_PRODUCTS {

    public static function store_product(array $product) {
        // $post_exist = \get_page_by_title($product['title'], OBJECT, 'product');
        if(isset($product['type'])) {  //  && ! \is_object($post_exist)
            if($product['type'] == "simple") {
                return self::store_simple_product($product);
            } elseif($product['type'] == "variable") {
                return self::store_variable_product($product);
            }
        } else {
            return null;
        }
    }  

    private static function store_simple_product(array $product) {
        $post_id = self::storing_product($product);
        if(\is_int($post_id)) {                      
            wp_set_object_terms($post_id, 'simple', 'product_type');
            update_post_meta( $post_id, '_visibility', 'visible' );
            update_post_meta($post_id, '_sku', $product['sku']);            
            update_post_meta( $post_id, '_price', $product['price']);
            update_post_meta( $post_id, '_regular_price', $product['price']);

            return $post_id;
        }

        return false;
    }

    private static function store_variable_product(array $product) {

        $attributes = array();

        if(SETTINGS::get_attributes_choice() == "on") {
            $attributes = self::check_attributes($product['attributes']);
            if(\is_string($attributes))
                return $attributes;
        }

        $post_id = self::storing_product($product);
        if(\is_int($post_id)) {
            wp_set_object_terms($post_id, 'variable', 'product_type');
            update_post_meta( $post_id, '_visibility', 'search' );

            if(\count($attributes) > 0) {
                if(SETTINGS::get_variations_choice() == "on")
                    $variation_saved = self::save_variations($attributes);

                if(isset($variation_saved))
                    self::assign_product_variations(\intval($post_id), $attributes, $product["price"]);
            }

            return $post_id;
        } else {
            return false;
        }
    }

    private static function save_image(int $post_id, string $img) {
        if($img != "") {
            if (!function_exists('wp_generate_attachment_metadata')){
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            }
            $tmp = download_url(trim($img));  
            $file_array = array(
                'name'     => basename( $img ),
                'tmp_name' => $tmp
            );  
            $attach_id = media_handle_sideload($file_array, $post_id);      
            if(is_int($attach_id)) {
                update_post_meta($post_id,'_thumbnail_id',$attach_id);
            } 
        }

        return;
    }

    private static function storing_product(array $product) {
        $post = array(
            'post_author' => $product['author'],
            'post_content' => $product['content'],            
            'post_status' => SETTINGS::get_default_status(),
            'post_title' => $product['title'],
            'post_parent' => '',
            'post_type' => "product",
        );

        $post_id = \wp_insert_post( $post );

        if(\is_int($post_id)) {
            update_post_meta( $post_id, '_stock_status', 'instock');  
            add_post_meta( $post_id, "WSPO_sku", "WSPO-".$product['id'] );

            if(SETTINGS::get_thunmbail_choice() == "on") {
                self::save_image($post_id, $product['thumbnail']);
            }

            self::assign_category($post_id, $product['categories']);               
        }

        return $post_id;
    }

    private static function assign_category(int $post_id, array $categories) {

        if(\is_array($categories)) {
            foreach($categories as  $name => $slug) {
                if($name != "" && $slug != "") {
                    $term_id = \term_exists($slug);
                    if(! \is_null($term_id)) {
                        \wp_set_post_terms( $post_id, $term_id, 'product_cat' ); 
                    } else {
                        if(SETTINGS::get_categories_choice() == "on") {
                            $term = \wp_insert_term($name, 'product_cat');
                            if(\is_array($term))
                                \wp_set_post_terms( $post_id, $term['term_id'], 'product_cat' ); 
                        }
                    }
                }
            }
        }

    }

    private static function check_attributes(array $attributes) {
    
        foreach($attributes as $attribute => $variations) {
            $attrs = REQUESTS::get_attribute_data($attribute);            
            if(isset($attrs['slug'])) { 
                if(\taxonomy_exists($attrs['slug'])) {
                    $new_attributes[$attrs['slug']] = $variations;
                } else {
                    return $attrs['slug'];
                }

            }
        }

        return $new_attributes ?? false;
    }


    private static function save_variations(array $attributes) {
        foreach($attributes as $attribute => $variations) {
            if( taxonomy_exists($attribute) ) {
                $tax_variations = get_terms($attribute, array('hide_empty' => false));                
                if(is_array($tax_variations) && count($tax_variations) > 1) {            
                    foreach($variations as $variation) {
                        $found = false;
                        foreach($tax_variations as $tax_variation) {
                            if($tax_variation->slug == strtolower(trim($variation))) {
                                $found = true;
                                break;
                            }
                        }
                        if(!$found) {
                            $added = wp_insert_term( trim($variation), $attribute );
                        }
                    }              
                } else {
                    foreach($variations as $variation) {
                        $created = wp_insert_term( trim($variation), $attribute );
                    }
                }
            }
        }

        return true;
    }

    private static function assign_product_variations(int $post_id, array $attributes, $price) {
        $product = wc_get_product($post_id);

        foreach($attributes as $attribute => $variations) {
            if(\taxonomy_exists($attribute)) {
                $terms = get_terms($attribute, array('hide_empty' => false));
                foreach($variations as $term_slug) {
                    $term_slug = strtolower(trim($term_slug));
                    wp_set_object_terms( $post_id, $term_slug, $attribute, true );
                    $thedata = Array($attribute =>Array(
                        'name'=> $attribute,
                        'value'=> $term_slug,
                        'is_visible' => '1',
                        'is_variation' => '1',
                        'is_taxonomy' => '1'
                    ));
                    update_post_meta( $post_id,'_product_attributes',$thedata);
                                        
                    foreach($terms as $term) {
                        if($term->slug == $term_slug) {
                            $variation_post = array(
                                'post_title'  => $product->get_title(),
                                'post_name'   => "product-$post_id-$term_slug",
                                'post_status' => 'publish',
                                'post_parent' => $post_id,
                                'post_type'   => 'product_variation',
                                'guid'        => $product->get_permalink()
                            );
                            $variation_id = wp_insert_post( $variation_post );
                            $variation = new \WC_Product_Variation( $variation_id );
                            $variation->update_meta_data( "attribute_$attribute", $term_slug );
                            wp_set_post_terms( $post_id, $term_slug, $attribute, true );
                            $variation->set_regular_price($price);
                            $variation->set_price($price);
                            $variation->save();
                        }
                    }            
                }
            }
        }

        return true;
    }
}