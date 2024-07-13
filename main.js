;(function($){
    console.log(ajax_obj.discount_type);
    $.each( $('#discount_rules_table tr'), function(key, row) {
        if( $(row).find('th').length == 0 ) {
            let item_inputs = $(row).find('td').eq(1)
            $.each(item_inputs.find('select, input'), function( key, input ) {
                if( $(input).val() == "" ) {
                    $(input).closest('div').hide()
                }
            })
        }
    } )
    
    $(document).on('change','.discount_type',function(){
        let discount_type = $(this).val();
        if(discount_type == ''){
            $(this).closest('tr').find('td').eq(1).find('.select_one').children().hide();
        }else{
            $.each( $(this).closest('tr').find('td').eq(1).find('select,input'), function(key, input) {
                if( $(input).attr('name').indexOf(discount_type) != -1 ) {
                    $(input).closest('div').show()
                } else {
                    $(input).closest('div').hide()
                    $(input).val("")
                }
            })
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
            <tr class="discount_row" data-row-count="${row_count}">
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
                            <select name="options[${row_count}][discount_item][product_discount]" id="">
                                <option value="">Select Product</option>
                                ${product_list}
                            </select>
                        </div>
                        <!-- cls -->
                        <!-- category data here -->
                            
                        <div class="select_category">
                            <select name="options[${row_count}][discount_item][category_discount]" id="">
                                <option value="">Select Category</option>
                                ${category_list}
                            </select>
                        </div>
                        <!-- cls -->
                        <!-- Cart amount here  -->
                        <div class="min_max_cart_amount">
                            <span>
                                <input type="text" name="options[${row_count}][discount_item][cart_amount_discount]" id="" placeholder="Min Card Amount">
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
    })

    $(document).on('click','.close_btn',function(){
        if($('.discount_row').length == 1){
            return;
        }
        $(this).closest('.discount_row').remove();
    })
    
})(jQuery)
