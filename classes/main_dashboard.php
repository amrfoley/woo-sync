<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once("requests.php");

use WSPO\REQUESTS;
use WSPO\NOTICES;
?>

<h1><?php echo __( 'Woo Sync Products & Orders', 'Woo-sync-products-orders' ); ?></h1>
<h3>Please Choose Category.</h3>

<?php
if($response = REQUESTS::get_categories(REQUESTS::CATEGORY_URL)) {   
    if(is_array($response)) {
        $selected_cat = $_GET['category'] ?? "";
        include_once(dirname(__DIR__)."/board/display_categories.php");
    } else {
        require_once("notices.php");
        NOTICES::connection_error($response);
    }

    if(isset($_GET['creat_attr'])) {
        require_once("notices.php");
        if($_GET['creat_attr'] === "true")
            NOTICES::attribute_created();
        else
            NOTICES::attribute_failed();
    }

    if(isset($_GET['category'])) {
        $category = $_GET['category'];
        $page = ($_GET['paged']) ?? "1";
        $category_products = REQUESTS::get_category_products($category, $page);

        if(is_array($category_products)) {
            include_once(dirname(__DIR__)."/board/display_category_produsts.php");
        } else {
            require_once("notices.php");
            NOTICES::connection_error("connection error");
        }
    }
} else {
    require_once("notices.php");
    NOTICES::connection_error("Can't reach server");
}