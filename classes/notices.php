<?php
namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NOTICES {

    public static function connection_error($message) {
        echo self::message_template($message);
    }

    public static function variable_product() {
        echo self::message_template("The Plugin Does not support variable products yet.", "warning");
    }

    public static function type_not_recognized() {
        echo self::message_template("Product Type not recognized !", "warning");
    }

    public static function synced_done() {
        echo self::message_template("Product(s) imported successfully.", "success");
    }

    public static function attribute_created() {
        echo self::message_template("Attribute imported successfully.", "success");
    }

    public static function attribute_failed() {
        echo self::message_template("Failed to import attribute.");
    }

    public static function synced_failed() {
        echo self::message_template("Failed to import product.");
    }

    public static function no_auth_found() {
        echo self::message_template("Please enter authentication first from the settings page.");
    }

    private static function message_template($message, $notice = "error") {
        return "
        <div class='notice notice-$notice'>
            <p>". __( $message, 'Woo-sync-products-orders' ) . "</p>
        </div>";
    }

    public static function create_attributes_first($attribute) {
        $name = \ltrim($attribute, "pa_");
        $message = "Attribute(s) missing! to import attribute ($name) click ";
        echo "
        <div class='notice notice-warning'>
            <p>". 
                __( $message, 'Woo-sync-products-orders' ) .
                " <span id='addThisAttr' data-attr='$attribute' class='cursor-pointer'>here</span>
            </p>
        </div>";
    }

    public static function WSPO_error_dependancy() {
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'WSPO Plugin Depends On Woocommerce. You need to install woocommerce first to be able to use it.', 'Woo-sync-products-orders' ); ?></p>
        </div>
        <?php
    }
}