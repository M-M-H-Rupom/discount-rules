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
define('DSC_PATH', plugin_dir_path(__FILE__));
define('DSC_URL',plugin_dir_url(__FILE__));
function script_callback(){
    $version = DSC_DEBUG ? time() : DSC_VERSION ;
    wp_enqueue_style( 'custom-css', DSC_URL.'assets/css/all.min.css' , false ,$version);
    wp_enqueue_style( 'font-awesome-css', DSC_URL.'assets/css/style.css' , false ,$version);
    wp_enqueue_script( 'custom_main_js', DSC_URL. 'assets/js/main.js', array('jquery'), $version, true);
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
    if( !empty( $rows )) {
        foreach($rows as $row_key => $row_value){
            $discount_type[$row_key] = $row_value['select_discount'] ;
        }
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
    include DSC_PATH . 'templates/discount-rules.php';
    
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
            $product_discounts[$row_value['discount_item']['product_discount']] = $row_value['discount'];
        }elseif($row_value['select_discount'] == 'category_discount'){
            $category_discounts[$row_value['discount_item']['category_discount']] = $row_value['discount'];
        }elseif($row_value['select_discount'] == 'cart_amount_discount'){
            $min_amount_discounts[$row_value['discount_item']['cart_amount_discount']] = $row_value['discount'];
        }
    }
    $total_discount = 0;
    foreach($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $product->get_id();
        if ($product->is_type('variation')) {
            $product_id = $product->get_parent_id();
        }
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
            $total_discount += intval($discount_amount);
        }
    }
    // print_r($cart_total);
    if ($total_discount > 0) {
        $cart->add_fee(__('Custom Discount', 'wc'), -$total_discount);
    }
}