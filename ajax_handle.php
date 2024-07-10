<?php
// function get_row_count() {
//     if (isset($_POST['row_count'])) {
//         update_option( 'row_count', $_POST['row_count']);
//         // $new_row_count = get_option( 'row_count' );
//         $row_count = get_option( 'row_count' );
//         wp_send_json_success($row_count);
//     }
// }
// add_action('wp_ajax_get_row_count', 'get_row_count');
// // // based on category discount 
// function get_category_for_discount() {
//     if (isset($_POST['discount_type']) && $_POST['discount_type'] == 'category_discount') {
//         $categories = get_terms(array(
//             'taxonomy' => 'product_cat',
//             'hide_empty' => false
//         ));
//         $category_options = [];
//         foreach ($categories as $category) {
//             $category_options[] = array(
//                 'id' => $category->term_id,
//                 'name' => $category->name
//             );
//         }
//         wp_send_json_success($category_options);
//     }
// }
// add_action('wp_ajax_get_cat_discount', 'get_category_for_discount');

