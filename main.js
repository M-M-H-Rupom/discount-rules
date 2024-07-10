;(function($){
    // $('.discount_row').find('.select_one').children().hide();
    // console.log(ajax_obj.discount_type);
    // let discount_types = ajax_obj.discount_type ;
    // for(let type in discount_types){
    //     if(discount_types[type] == 'product_discount'){
    //         $(this).closest('.discount_row').find('.select_one').children().hide();
    //             $(this).closest('.discount_row').find('.select_product').show();
    //     }else if(discount_types[type] == 'category_discount'){
    //         $(this).closest('.discount_row').find('.select_one').children().hide();
    //         $(this).closest('.discount_row').find('.select_category').show();
    //     }
    // }
    $(document).on('change','.discount_type',function(){
        let discount_type = $(this).val();
        let this_class = $(this);
        switch (discount_type) {
            case 'product_discount':
                $(this).closest('.discount_row').find('.select_one').children().hide();
                $(this).closest('.discount_row').find('.select_product').show();
                
            break;
            case 'category_discount':
                $(this).closest('.discount_row').find('.select_one').children().hide();
                $(this).closest('.discount_row').find('.select_category').show();
               
            break;
            case 'cart_amount_discount':
                $(this).closest('.discount_row').find('.select_one').children().hide();
                $(this).closest('.discount_row').find('.min_max_cart_amount').show();
            break;
        }
    })
    $('.add_more_discount_row span').on('click',function(){
        let row_count = $('#row_count').val()
        row_count++;
        // catch product
        let products_data = ajax_obj.products;
        let product_list = ''
        products_data.forEach((product) => {
            product_list += `<option value="${product.id}">${product.name}</option>`
        });
        // catch categories
        let categories_data = ajax_obj.categories;
        let category_list = ''
        categories_data.forEach((category) => {
            category_list += `<option value="${category.id}">${category.name}</option>`
        });
        // 
        let row_data = `
            <tr class="discount_row" data-row-count="">
                <td>
                    <div class="select_discount">
                        <select name="options[${row_count}][select_discount]" class='discount_type'>
                            <option value="">Select Discount</option>
                            <option value="product_discount">Product discount</option>
                            <option value="category_discount">Category discount</option>
                            <option value="cart_amount_discount">Cart amount discount</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div class="select_one">
                        
                        <div class="select_product">
                            <select name="options[${row_count}][discount_item][product]" id="">
                                <option value="">Select Product</option>
                                ${product_list}
                            </select>
                        </div>
                        <!-- cls -->
                        <!-- category data here -->
                            
                        <div class="select_category">
                            <select name="options[${row_count}][discount_item][category]" id="">
                                <option value="">Select Category</option>
                                ${category_list}
                            </select>
                        </div>
                        <!-- cls -->
                        <!-- Cart amount here  -->
                        <div class="min_max_cart_amount">
                            <span>
                                <input type="text" name="options[${row_count}][discount_item][min_amount]" id="" placeholder="Min Card Amount">
                            </span>
                        </div>
                        <!-- cls -->
                    </div>
                </td>
                <td class="discount_amount_with_close_btn">
                    <div class="discount_amount">
                        <input type="text" name="options[${row_count}][discount]" id="" placeholder="Enter amount" value="">
                    </div>
                    <div class="close_btn">
                        <span>close</span>
                    </div>
                </td>
            </tr>
        `
        // row_clone.find('input').val('');
        $('#row_count').val(row_count)
        $('#discount_rules_table').append(row_data);
        $('#discount_rules_table .discount_row').eq(-1).find('.select_one').children().hide();
        // $('.discount_row').find('.select_one').children().hide();
    })

    $(document).on('click','.close_btn',function(){
        if($('.discount_row').length == 1){
            return;
        }
        $(this).closest('.discount_row').remove();
    })
    
})(jQuery)
