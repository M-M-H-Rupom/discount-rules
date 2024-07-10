<?php
/**
 * Plugin Name: Discount
 * Description: hello
 * Version: 1.0
 * Author: Rupom
 * Text Domain: wc
 * 
 */

define('DSC_DEBUG',true);
define('DSC_VERSION', '1.0.0');
include plugin_dir_path( __FILE__ ) . '/ajax_handle.php';
function script_callback(){
    $version = DSC_DEBUG ? time() : DSC_VERSION ;
    wp_enqueue_style( 'custom-css', plugin_dir_url( __FILE__ ).'/style.css' , false ,$version);
    wp_enqueue_script( 'custom_main_js', plugin_dir_url( __FILE__ ). '/main.js', array('jquery'), $version, true);
    // Fetch all published products
    $products = wc_get_products(array(
        'limit' => -1,
        'status' => 'publish'
    ));
    $product_data = array();   //send to js
    foreach ($products as $product) {
        $product_data[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
        );
    }
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false
    ));
    $category_data = array();  //send to js
    foreach ($categories as $category) {
        $category_data[] = array(
            'id' => $category->term_id,
            'name' => $category->name,
        );
    }
    $rows = get_option( 'options');
    $discount_type = [];
    foreach($rows as $row_key => $row_value){
        $discount_type[$row_key] = $row_value['select_discount'] ;
    }
    
    // $product_data_json = json_encode($product_data);
    wp_localize_script('custom_main_js', 'ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'products' => $product_data,
        'categories' => $category_data,
        'discount_type' => $discount_type,
    ));

}
add_action( 'admin_enqueue_scripts', 'script_callback' );
function custom_admin_menu_callback(){
    add_menu_page( 'discount-rules', 'Discount-rules', 'manage_options', 'discount_rules','discount_rules_callback',false , 26 );
}
add_action( 'admin_menu', 'custom_admin_menu_callback');
function discount_rules_callback(){
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
                    <tr class="discount_row" data-row-count="">
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
                                    $selected = selected($row_value['discount_item']['product'], $product->get_id(),false);
                                    $product_list .= '<option value="' . $product->get_id() . '"' . $selected . '>' . $product->get_name() . '</option>';
                                }
                                ?>
                                <div class="select_product">
                                    <select name="options[<?php echo $row_key ?>][discount_item][product]" id="options_<?php echo $row_count ?>_select_product">
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
                                    $selected = selected($row_value['discount_item']['category'], $category->term_id,false);
                                    $category_list .= '<option value="' . $category->term_id . '"' . $selected . '>' . $category->name . '</option>';
                                }
                                ?>
                                <div class="select_category">
                                    <select name="options[<?php echo $row_key ?>][discount_item][category]" id="options_<?php echo $row_count ?>_select_category">
                                        <option value="">Select Category</option>
                                        <?php echo $category_list; ?>
                                    </select>
                                </div>
                                <!-- cls -->
                                <!-- Cart amount here  -->
                                <div class="min_max_cart_amount">
                                    <span>
                                        <input type="text" name="options[<?php echo $row_key ?>][discount_item][min_amount]" id="" placeholder="Min Card Amount" value="<?php echo $row_value['discount_item']['min_amount'] ?>">
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
                                <span>close</span>
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
}
// apply discount for product and category 
add_action('woocommerce_cart_calculate_fees','apply_discounts_callback', 10, 1);
function apply_discounts_callback($cart) {
    $rows = get_option( 'options');
    $product_discounts = array();
    $category_discounts = array();
    $min_amount_discounts = array();
    foreach($rows as $row_key => $row_value){
        if($row_value['select_discount'] == 'product_discount'){
            $product_discounts[$row_value['discount_item']['product']] = $row_value['discount'];
        }elseif($row_value['select_discount'] == 'category_discount'){
            $category_discounts[$row_value['discount_item']['category']] = $row_value['discount'];
        }elseif($row_value['select_discount'] == 'cart_amount_discount'){
            $min_amount_discounts[$row_value['discount_item']['min_amount']] = $row_value['discount'];
        }
    }
    $total_discount = 0;
    foreach($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $product->get_id();
        $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        // the product discount
        if(isset($product_discounts[$product_id])) {
            $total_discount += $product_discounts[$product_id] * $cart_item['quantity'];
        }else{
            foreach ($categories as $category_id) {
                if (isset($category_discounts[$category_id])) {
                    $total_discount += $category_discounts[$category_id] * $cart_item['quantity'];
                    break;
                }
            }
        }
    }
    // Check cart total min
    $cart_total = $cart->get_cart_contents_total();
    foreach($min_amount_discounts as $min_amount => $discount_amount){
        if ($cart_total >= $min_amount) {
            $total_discount += $discount_amount;
        }
    }
    // print_r($cart_total);
    if ($total_discount > 0) {
        $cart->add_fee(__('Custom Discount', 'wc'), -$total_discount);
    }
}