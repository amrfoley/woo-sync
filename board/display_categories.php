<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>


<div class="form py-2">
    <form action="" method="GET">
        <div class="mb-3">      
            <input type="hidden" name="page" value="Woo-sync" />                                          
            <select class="custom-select" name="category" required>
                <option value=""<?= ($selected_cat == "")? ' selected' : ''; ?> disabled>Choose Category</option>
                <?php foreach($response as $category): ?>
                    <option value="<?= $category['id']; ?>"<?= ($selected_cat == $category['id'])? ' selected' : ''; ?>><?= $category['name']; ?></option>
                <?php endforeach; ?>
            </select>           
        </div>
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>