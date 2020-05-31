<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once(dirname(__DIR__)."/classes/save_products.php"); ?>

<div class="wrap">
<table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Image</th>
      <th scope="col">Title</th>
      <th scope="col">Description</th>
      <th scope="col">suggested Price</th>
      <th scope="col">
        <div class="custom-control custom-checkbox" id="defaultCheckAll">
            <input id="defaultChecked" type="checkbox" class="custom-control-input" checked="checked" />
            <label class="custom-control-label" for="defaultChecked"></label>
        </div>
      </th>
    </tr>
  </thead>
  <tbody>

    <?php if(empty($category_products)): $lastPage = true; ?>
      <tr>
        <td colspan="6">
          <h2>No Products Found</h2>
        </td>
      </tr>
    <?php else: ?>
      <form action="" method="POST">
        <?php $count = 1; foreach($category_products as $product):
          $post_exist = get_page_by_title($product['name'], OBJECT, 'product'); ?>
          <tr>
            <th scope="row"><?= $count; ?></th>
            <?php if(is_object($post_exist)): ?>            
              <td>
                <img src="<?= get_the_post_thumbnail_url($post_exist->ID, 'thumbnail'); ?>" alt="">
              </td>
              <td>
                <a href="<?= get_the_permalink($post_exist->ID); ?>" target="_blank">
                  <?= $post_exist->post_title; ?>
                </a>
              </td>
              <td></td>
              <td></td>
              <td><img src="<?= plugins_url( 'assets/imgs/checklist.png', dirname(plugin_basename( __FILE__ )) ) ?>" style="max-width: 90px" /></td>
            <?php else: ?>
              <td>
                <?php if(is_array($product['images']) && count($product['images']) > 0): 
                  $product_image = $product['images'][0]['src']; ?>
                  <img src="<?= $product_image; ?>" class="WSPO_img">
                  <input type="hidden" name="product_<?= $count; ?>_img" value="<?= $product_image; ?>" />
                <?php endif; ?>
              </td>
              <td><input type="text" name="product_<?= $count; ?>_title" value="<?= $product['name']; ?>"></td>
              <td><textarea name="product_<?= $count; ?>_description" cols="30" rows="10"><?= $product['short_description']; ?></textarea></td>
              <td><input type="number" name="product_<?= $count; ?>_price" value="<?= $product['price'] ?? $product['regular_price']; ?>"></td>
              <td>
                <div class="custom-control custom-checkbox">
                  <input id="check-<?= $product['id']; ?>" type="checkbox" class="custom-control-input SelectedId" name="product_<?= $count; ?>_checked" checked="checked" />
                  <label class="custom-control-label" for="check-<?= $product['id']; ?>"></label>
                </div>
              </td>          
              <!-- product type -->
              <input type="hidden" name="product_<?= $count; ?>_type" value="<?= $product['type']; ?>" />

              <!-- product sku -->
              <input type="hidden" name="product_<?= $count; ?>_sku" value="<?= $product['sku']; ?>" />   

              <!-- product original id -->
              <input type="hidden" name="product_<?= $count; ?>_id" value="<?= $product['id']; ?>" />        

              <!-- product categories -->
              <?php foreach($product['categories'] as $category): ?>
                <input type="hidden" name="product_<?= $count; ?>_categories[]" value="<?= $category['name'].'&*&'.$category['slug']; ?>" />
              <?php endforeach; ?> 

              <!-- product attributes and variations -->
              <?php if($product['type'] == "variable"): ?>

                <?php foreach($product['attributes'] as $attr): ?>
                  <input type="hidden" name="product_<?= $count; ?>_attributes[]" value="<?= $attr['id'] ?>" />
                  <?php foreach($attr['options'] as $attr_variation): ?>
                    <input type="hidden" name="product_<?= $count; ?>_attributes_variations[]" value="<?= $attr_variation; ?>" />
                  <?php endforeach; ?>
                <?php endforeach; ?>

              <?php endif; ?>  
            <?php endif; ?>
          </tr>
          <?php $count++; endforeach; ?>
        <!-- <input type="hidden" name="page" value="<?php //echo $_GET['page'] ?? "1"; ?>" /> -->

        <tr>
          <td colspan="6">
            <button class="btn btn-primary" type="submit" name="savingProducts">Save</button>
          </td>
        </tr>
      </form>
    <?php endif; ?>
  </tbody>
</table>

<div class="d-flex align-items center w-100">
  <?php if($page > 1): ?>
    <a href="?page=Woo-sync&category=<?= $_GET['category']; ?>&paged=<?= intval($page) - 1; ?>" class="btn btn-light mr-auto">Prev</a>
  <?php endif; ?>
  <?php if(!isset($lastPage)): ?>
    <a href="?page=Woo-sync&category=<?= $_GET['category']; ?>&paged=<?= intval($page) + 1; ?>" class="btn btn-light ml-auto">Next</a>
  <?php endif; ?>
</div>
</div>