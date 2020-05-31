<?php
namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WSPO\NOTICES;
use WSPO\ORDER_SUBMISSION;
use WSPO\SETTINGS;

class ADMIN {
    public static function init() {
        add_action('admin_init', array( __CLASS__, 'check_woocommerce_activation'));        
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wpdocs_enqueue_custom_admin_style' ));
        add_action('admin_menu', array( __CLASS__, 'Woo_plugin_setup_menu'));        
        
        if(SETTINGS::get_sending_orders_choice() == "on") {
            require_once( 'order_submission.php' );
            add_action( 'woocommerce_thankyou', array( __CLASS__, 'handle_WSPO_orders' ), 10, 3 ); 
        }

        // ajax for creating new attributes.
        add_action('wp_ajax_WSPO_attribute_creation', array(__CLASS__, 'create_taxonomy'));
    }
    
    public static function check_woocommerce_activation() {
        if (! is_plugin_active('woocommerce/woocommerce.php') ) {
            require_once("notices.php");
            add_action( 'admin_notices', array( 'NOTICES', 'WSPO_error_dependancy' ));
            deactivate_plugins(plugin_basename(__FILE__));
            return false;
        }
        return true;

    }

    public static function Woo_plugin_setup_menu(){
        add_menu_page( 
            'WooSync Plugin Page', 
            'Woo Sync', 
            'manage_options', 
            'Woo-sync', 
            array( __CLASS__, 'ts_dashboard' ),
            plugins_url( 'assets/imgs/sync.png', dirname(plugin_basename( __FILE__ )) )
        );
    }

    public static function ts_dashboard(){
        if(SETTINGS::get_store_url() && SETTINGS::get_key() && SETTINGS::get_secret()) {
            require_once("main_dashboard.php");
        } else {
            require_once("notices.php");
            NOTICES::no_auth_found();
        }
    } 

    public static function wpdocs_enqueue_custom_admin_style() {
        wp_register_style( 'custom_Woo_admin_bootstrap', "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css", false, '1.0.0' );
        wp_enqueue_style( 'custom_Woo_admin_bootstrap' );
        wp_register_style( 'custom_Woo_admin_css', plugins_url( 'assets/css/styles.css', dirname(plugin_basename( __FILE__ )) ), false, '1.0.0' );        
        wp_enqueue_style( 'custom_Woo_admin_css' );
        wp_register_script( 'Woo_admin_jquery', "https://code.jquery.com/jquery-3.4.1.min.js", array('jquery'), '1.0.0', true );
        wp_register_script( 'Woo_admin_js', plugins_url( 'assets/js/Woo-board.js', dirname(plugin_basename( __FILE__ )) ), array('jquery'), '1.0.0', true );        
        wp_enqueue_script( 'Woo_admin_jquery' );
        wp_enqueue_script( 'Woo_admin_js' );
    } 

    public static function handle_WSPO_orders($order_id) {
        ORDER_SUBMISSION::new_order_created($order_id);
    }

    public static function create_taxonomy() {
        global $wpdb;
        $taxonomy = \ltrim($_POST['name'], "pa_");
        $data = array(
            'attribute_label'   => $taxonomy,
            'attribute_name'    => $taxonomy,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        );            
        $wpdb->insert( "{$wpdb->prefix}woocommerce_attribute_taxonomies", $data );                
        do_action('woocommerce_attribute_added', $wpdb->insert_id, $data);
        wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
        delete_transient('wc_attribute_taxonomies');

        echo $wpdb->insert_id ?? false;

        wp_die();
    }
}