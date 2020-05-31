# Woo-sync
This plugin enable you to sync Products from any diserd website you choose. You choose the category and start to import both simple and variable products from the source site and import it to yours. You can edit the data of each product such as title, description and price before they got imported. When a product form the source site got purchased, the plugin automatically send the chosen product in an order to the source site.


## Installation
1. Go to the [releases tab](https://github.com/) in this repo.
2. Find the "Latest release" and click to download `woocommerce-external-product-embed.zip`.
3. Starting at the Plugins page in the WordPress Admin, select "Add New" and then "Upload Plugin".
3. Upload and Activate the plugin.
3. Go to Settings > WooCommerce External Products.
4. Add in the [credentials](https://docs.woocommerce.com/document/woocommerce-rest-api/) to connect to external site.

### A few notes about this plugin:
* This plugin is dependent on woocommerce so you must install it first.
* The source site API endpoint must be read/write to import products and export orders of these products if purchased.
* To get use of the submitting orders feature. Every product imported from the source site creates custom sku meta field with product id (id of the product in the   source website). You can change default sku field but leave the sku meta field to use this feature.

## Usage

Go to the plugins page and import the plugin. Make sure woocommerce is installed first.
Go to settings of the plugin and enter the source website url, API key and API secret.
choose the status you want to give to the imported products and if you want to import each product thumbnail, category, attributes and variations (if exists).
start importing your required products from the main plugin page by chosing the category first.
Edit any input field per row (product) before saving and to make sure this product get stored check the checkbox at the end of each row or check them all with the checkbox of the table header.
The plugin import 10 products per request and in order to avoid duplication. The row imported will not be eligible to get stored agian and a green icon will appear instead of the checkbox and the product title will be clickable.
at the end of the page there is the save button to actullay import the products into your database and the paginations buttons for the next/prevoius requests.


## Frequently Asked Questions