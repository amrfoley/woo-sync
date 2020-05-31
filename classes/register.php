<?php
namespace WSPO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WSPO\SETTINGS;

class REGISTER {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );
		add_action( 'admin_init', array( __CLASS__, 'settings_init' ) );
	}

	/**
	 * Add Settings Menu
	 */
	public static function add_menu_item() {
        add_submenu_page( 
			'Woo-sync', 
			'settings', 
			'Settings', 
			'manage_options', 
			'settings', 
			array( __CLASS__, 'WSPO_options_page' ) 
		);
	}

	/**
	 * Add the Settings
	 */
	public static function settings_init() {
		register_setting( 'WSPO_settings_group', 'WSPO_settings' );

		// API Connect Section
		add_settings_section(
			'WSPO_api_connect_section',
			__( 'Connect to the External Store', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'api_instructions' ),'WSPO_settings_group'
		);

		// Store Home URL
		add_settings_field(
			'WSPO_store_url',
			__( 'Store Home URL', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'store_url_text_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// Consumer Key
		add_settings_field(
			'WSPO_consumer_key',
			__( 'Consumer Key', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'consumer_key_text_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// Consumer Secret
		add_settings_field(
			'WSPO_consumer_secret',
			__( 'Consumer Secret', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'consumer_secret_text_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
        );
        
		// default status
		add_settings_field(
			'WSPO_default_status',
			__( 'Product Status', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'default_status_text_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// import categories
		add_settings_field(
			'WSPO_import_categories',
			__( 'Import Product Categories', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'import_categories_checkbox_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// import thumbnail
		add_settings_field(
			'WSPO_import_thumbnails',
			__( 'Import Product Thumbnail', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'import_thumbnail_checkbox_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// import attribute
		add_settings_field(
			'WSPO_import_attributes',
			__( 'Import Product Attributes', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'import_attributes_checkbox_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// import variations
		add_settings_field(
			'WSPO_import_variations',
			__( 'Import Product Variations', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'import_variations_checkbox_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

		// sync orders
		add_settings_field(
			'WSPO_send_orders',
			__( 'Send Product Orders', 'Woo-sync-products-orders' ),
			array( __CLASS__, 'send_orders_checkbox_field' ), 'WSPO_settings_group', 'WSPO_api_connect_section'
		);

	}

	// Add a link to the docs on gettings API credentials 
	public static function api_instructions() {
		echo sprintf( __( 'You will need to enable the REST API on the external website, and <a href="%s" target="_blank">generate API keys</a>.',
			'Woo-sync-products-orders' ), 'https://docs.woocommerce.com/document/woocommerce-rest-api/#section-3' );
	}

	// Store Home URL
	public static function store_url_text_field() { ?>
		<input type='text' class="regular-text WSPO_store_url" name='WSPO_settings[WSPO_store_url]' value='<?= SETTINGS::get_store_url(); ?>'>
		<?php
	}

	// Consumer Key
	public static function consumer_key_text_field() { ?>
		<input type='text' class="regular-text WSPO_consumer_key" name='WSPO_settings[WSPO_consumer_key]' value='<?= SETTINGS::get_key(); ?>'>
		<?php
	}

	// Consumer Secret
	public static function consumer_secret_text_field() { ?>
		<input type='password' class="regular-text WSPO_consumer_secret" name='WSPO_settings[WSPO_consumer_secret]' value='<?= SETTINGS::get_secret(); ?>'>
		<?php
	}

	/* import category  */
	public static function import_categories_checkbox_field() {
		$importing_categories = SETTINGS::get_categories_choice(); ?>
		<input type='checkbox' class="regular-check WSPO_import_categories" name='WSPO_settings[WSPO_import_categories]'<?= ($importing_categories == "on")? ' checked="checked"' : ''; ?> />
		<?php
	}
	public static function import_thumbnail_checkbox_field() {
		$importing_thumbnails = SETTINGS::get_thunmbail_choice(); ?>
		<input type='checkbox' class="regular-check WSPO_import_thumbnails" name='WSPO_settings[WSPO_import_thumbnails]'<?= $importing_thumbnails? ' checked="checked"' : ''; ?> />
		<?php
	}
	public static function import_attributes_checkbox_field() {
		$importing_attributes = SETTINGS::get_attributes_choice(); ?>
		<input type='checkbox' class="regular-check WSPO_import_attributes" name='WSPO_settings[WSPO_import_attributes]'<?= ($importing_attributes == "on")? ' checked="checked"' : ''; ?> />
		<?php
	}
	public static function import_variations_checkbox_field() {
		$importing_variations = SETTINGS::get_variations_choice(); ?>
		<input type='checkbox' class="regular-check WSPO_import_variations" name='WSPO_settings[WSPO_import_variations]'<?= ($importing_variations == "on")? ' checked="checked"' : ''; ?> />
		<?php
	}
	public static function send_orders_checkbox_field() {
		$send_orders = SETTINGS::get_sending_orders_choice(); ?>
		<input type='checkbox' class="regular-check WSPO_send_orders" name='WSPO_settings[WSPO_send_orders]'<?= ($send_orders == "on")? ' checked="checked"' : ''; ?> />
		<?php
	}

	/** default status */
	public static function default_status_text_field() {
        $available_status = get_post_statuses();
        if(isset($available_status) && \count($available_status) > 0):
            $d_status = SETTINGS::get_default_status(); ?>
            <select class="regular-select WSPO_default_status" name="WSPO_settings[WSPO_default_status]">
            <?php foreach($available_status as $key => $value): ?>
                <option value="<?= $key; ?>"<?= ($key == $d_status)? ' selected' : ''; ?>><?= $value; ?></option>
            <?php endforeach; ?>
            </select>
        <?php endif; 
	}

	// Create the settings page.
	public static function WSPO_options_page() {
		?>
		<div class="wrap">
			<h1><?php echo __( 'Woo Sync Products & Orders', 'Woo-sync-products-orders' ); ?></h1>
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'WSPO_settings_group' );
				do_settings_sections( 'WSPO_settings_group' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

}

new REGISTER();