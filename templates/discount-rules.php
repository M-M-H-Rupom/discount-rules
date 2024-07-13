<?php
if (isset($_POST['discount_rules_submit'])) {
    if(isset($_POST['row_count'])){
        update_option('row_count', $_POST['row_count']);
    }
    if(isset($_POST['options'])){
        update_option('options', $_POST['options']);
    }
}
$rows = get_option( 'options');
// echo '<pre>';
// print_r($rows);
// echo '</pre>';

$row_count = get_option('row_count');
?>  
<div class="rules_container">
    <form action="" method="POST">
        <input type="hidden" name="row_count" id="row_count" value="<?php echo $row_count; ?>">
        <table class="form-table" id="discount_rules_table">
            <tr>
                <th>Select Discount</th>
                <th>Select One</th>
                <th>Enter Amount</th>
            </tr>
            <?php 
            if(!empty($rows)){
                foreach($rows as $row_key => $row_value){
                    // print_r($row_value);
                ?>  
                <tr class="discount_row" data-row-count="<?php echo $row_key ?>">
                    <td>
                        <div class="select_discount">
                            <select name="options[<?php echo $row_key ?>][select_discount]" id="options_<?php echo $row_count ?>_select_discount" class='discount_type'>
                                <option value="">Select Discount</option>
                                <option value="product_discount" <?php  echo selected($row_value['select_discount'] , 'product_discount') ?>>Product discount</option>
                                <option value="category_discount" <?php  echo selected($row_value['select_discount'] , 'category_discount') ?>>Category discount</option>
                                <option value="cart_amount_discount" <?php  echo selected($row_value['select_discount'] , 'cart_amount_discount') ?>>Cart amount discount</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="select_one">
                            <!-- product data here -->
                            <?php 
                            $products = wc_get_products(array(
                                'limit' => -1,
                                'status' => 'publish'
                            ));
                            
                            $product_list = '';
                            foreach ($products as $product) {
                                $selected = selected($row_value['discount_item']['product_discount'], $product->get_id(),false);
                                $product_list .= '<option value="' . $product->get_id() . '"' . $selected . '>' . $product->get_name() . '</option>';
                            }
                            ?>
                            <div class="select_product">
                                <select name="options[<?php echo $row_key ?>][discount_item][product_discount]" id="options_<?php echo $row_count ?>_select_product">
                                    <option value="">Select Product</option>
                                    <?php echo $product_list; ?>
                                </select>
                            </div>
                            <!-- cls -->
                            <!-- category data here -->
                            <?php
                            $categories = get_terms(array(
                                            'taxonomy' => 'product_cat',
                                            'hide_empty' => false
                                        ));
                            $category_list = '';
                            foreach ($categories as $category) {
                                $selected = selected($row_value['discount_item']['category_discount'], $category->term_id,false);
                                $category_list .= '<option value="' . $category->term_id . '"' . $selected . '>' . $category->name . '</option>';
                            }
                            ?>
                            <div class="select_category">
                                <select name="options[<?php echo $row_key ?>][discount_item][category_discount]" id="options_<?php echo $row_count ?>_select_category">
                                    <option value="">Select Category</option>
                                    <?php echo $category_list; ?>
                                </select>
                            </div>
                            <!-- cls -->
                            <!-- Cart amount here  -->
                            <div class="min_max_cart_amount">
                                <span>
                                    <input type="text" name="options[<?php echo $row_key ?>][discount_item][cart_amount_discount]" id="" placeholder="Min Card Amount" value="<?php echo $row_value['discount_item']['cart_amount_discount'] ?>">
                                </span>
                            </div>
                            <!-- cls -->
                        </div>
                    </td>
                    <td class="discount_amount_with_close_btn">
                        <div class="discount_amount">
                            <input type="text" name="options[<?php echo $row_key ?>][discount]" id="" placeholder="Enter amount" value="<?php echo $row_value['discount'] ?>">
                        </div>
                        <div class="close_btn">
                            <span><i class="fas fa-xmark"></i></span>
                        </div>
                    </td>
                </tr>
                <?php
                }
            }
            ?>
        </table>
        <div class="add_more_discount_row">
            <span>Add more </span>
        </div>
        <div class="save_btn">
            <input type="submit" value="Save Changes" name="discount_rules_submit">
        </div>
    </form>
</div>
<?php