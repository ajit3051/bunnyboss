$(document).ready(function () {

    $(document).on('click', '.remove-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');

        $.ajax({
            url: _BASEURL + 'get_ajaxdata.php',
            method: 'POST',
            data: { id: id, action: 'remove_from_cart' },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    if (response.dropdown_html) {
                        $('#header-cart-dropdown-menu').html(response.dropdown_html);
                    }
                    if (response.cart_count !== undefined) {
                        $('.cart-count').text(response.cart_count);
                    }
                    btn.closest('tr').fadeOut(300, function () { 
                        $(this).remove(); // 1. Item removed from DOM completely
                        updateCartTotals(); // 2. Now recalculate totals and cart badge!
                        if ($('table.table-cart tbody tr').length === 0 || parseInt(response.cart_count || 0) <= 0) {
                            window.location.href = _BASEURL + 'index.php';
                        }
                    });
                }
            }
        });
    });
    
    // Function to calculate and update Subtotal and Grand Total
    function updateCartTotals() {
        var subtotal = 0;
        var totalQty = 0;

        // 1. Calculate Subtotal
        $('.total-col').each(function() {
            var totalText = $(this).text();
            var val = parseFloat(totalText.replace(/[^0-9.-]+/g, "")) || 0;
            subtotal += val;
        });

        // Update Subtotal Display
        $('.summary-subtotal.cart-subtotal td:last-child').text('₹' + subtotal.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // 2. Calculate Total Cumulative Quantity Safely
        $('table.table-cart tbody tr').each(function() {
            var $row = $(this);
            var $qtyContainer = $row.find('.cart-product-quantity');
            
            if ($qtyContainer.length > 0) {
                var hiddenQty = parseInt($qtyContainer.find('input[type="number"]').val());
                var visibleQty = parseInt($qtyContainer.find('input[type="text"]').val());
                var rowQty = hiddenQty || visibleQty || 1;
                totalQty += rowQty;
            }
        });

        // Update the Header Badge Count
        $('.cart-count').text(totalQty);

        // 3. Calculate Shipping
        var baseShipping = typeof window.SHIPPING_CHARGE !== 'undefined' ? parseFloat(window.SHIPPING_CHARGE) : 150;
        var perItemShipping = typeof window.SHIPPING_CHARGE_PER_ITEM !== 'undefined' ? parseFloat(window.SHIPPING_CHARGE_PER_ITEM) : 100;
        var shipping = 0;
        
        if (totalQty > 0) {
            shipping = baseShipping + ((totalQty - 1) * perItemShipping);
        }

        var $selectedShipping = $('input[name="shipping"]:checked');
        if ($selectedShipping.length > 0) {
            $selectedShipping.closest('tr').find('td:last-child').text('₹' + shipping.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        // 4. Calculate Total (Subtotal + Shipping)
        var subtotalPlusShipping = subtotal + shipping;
        $('.cart-total-before-gst td:last-child').text('₹' + subtotalPlusShipping.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // 5. Calculate GST on (Subtotal + Shipping)
        var gstPercent = typeof window.GST_PERCENT !== 'undefined' ? parseFloat(window.GST_PERCENT) : 5;
        var gst = subtotalPlusShipping * (gstPercent / 100);
        
        $('.cart-gst td:first-child').text('GST (' + gstPercent + '%):');
        $('.cart-gst td:last-child').text('₹' + gst.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // 6. Calculate Grand Total = (Subtotal + Shipping) + GST
        var grandTotal = subtotalPlusShipping + gst;
        $('.cart-grand-total td:first-child').text('Total (GST included):');
        $('.cart-grand-total td:last-child').text('₹' + grandTotal.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
    }

    // --- EVENT LISTENERS ---

    // Listen for quantity updates (your working code expanded)
    $(document).on('change input', '.cart-product-quantity input', function() {
        var $row = $(this).closest('tr');
        var priceText = $row.find('.price-col').text();
        var price = parseFloat(priceText.replace(/[^0-9.-]+/g, "")); 
        var qty = parseInt($(this).val()) || 1;

        var total = price * qty;
        var formattedTotal = '₹' + total.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        $row.find('.total-col').text(formattedTotal);

        // Trigger the cart summary update!
        updateCartTotals();

        calculateRowPrice($(this));
    });


    // Listen for shipping radio selection changes
    $('input[name="shipping"]').on('change', function() {
        updateCartTotals();
    });

    // Run once on page load to ensure initial numbers sync correctly
    // Make sure one shipping radio option has the 'checked' attribute in your HTML
    updateCartTotals();

    $('#checkout-btn').on('click', function(e) {
        e.preventDefault(); // Stop the page from redirecting instantly
        
        var targetUrl = $(this).attr('href');
        var cartItems = [];

        // 1. Collect data for every single product in the table
        $('table.table-cart tbody tr').each(function() {
            var $row = $(this);
            var productId = $row.data('product-id') || $row.data('id');
            
            if (productId) {
                var priceText = $row.find('.price-col').text();
                var totalText = $row.find('.total-col').text();
                var productTitle = $row.find('.product-title a').text().trim();
                
                var price = parseFloat(priceText.replace(/[^0-9.-]+/g, "")) || 0;
                var total = parseFloat(totalText.replace(/[^0-9.-]+/g, "")) || 0;
                var rawSize = $row.find('.size-value').text().trim();
                var parsedNum = parseInt(rawSize);
                var size = isNaN(parsedNum) ? rawSize : parsedNum;
                var qty = parseInt($row.find('.cart-product-quantity input').val()) || 1;

                cartItems.push({
                    product_id: productId,
                    product_title: productTitle,
                    unit_price: price,
                    quantity: qty,
                    size: size,
                    row_total: total
                });
            }
        });

        // 2. Collect summary charges
        var subtotalText = $('.cart-subtotal td:last-child').text();
        var subtotal = parseFloat(subtotalText.replace(/[^0-9.-]+/g, "")) || 0;

        var gstText = $('.cart-gst td:last-child').text();
        var gst = parseFloat(gstText.replace(/[^0-9.-]+/g, "")) || 0;

        var totalText = $('.cart-grand-total td:last-child').text();
        var total = parseFloat(totalText.replace(/[^0-9.-]+/g, "")) || 0;

        var shipping = 0;
        var $selectedShipping = $('input[name="shipping"]:checked');
        if ($selectedShipping.length > 0) {
            var shippingText = $selectedShipping.closest('tr').find('td:last-child').text();
            shipping = parseFloat(shippingText.replace(/[^0-9.-]+/g, "")) || 0;
        }

        // 3. Send data to PHP via AJAX
        $.ajax({
            url: _BASEURL + 'get_ajaxdata.php',
            type: 'POST',
            data: {
                products: cartItems,
                subtotal: subtotal,
                gst: gst,
                shipping: shipping,
                grand_total: total,
                action: 'save_cart'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Now redirect to checkout page safely
                    window.location.href = targetUrl;
                } else {
                    alert('Something went wrong. Please try again.');
                }
            },
            error: function() {
                alert('Server error. Could not proceed to checkout.');
            }
        });
    });

    function calculateRowPrice($input) {
        var $row = $input.closest('tr');
        var qty = parseInt($input.val()) || 1;
        var cartItemId = $row.data('id') || $row.find('.remove-btn').data('id'); // Get database row ID

        // Calculate row visual total locally first for instant feedback
        var priceText = $row.find('.price-col').text(); 
        var unitPrice = parseFloat(priceText.replace(/[^0-9.-]+/g, "")) || 0;
        var rowTotal = unitPrice * qty;

        $row.find('.total-col').text('₹' + rowTotal.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // Update frontend totals layout
        updateCartTotals($input);

        // --- NEW: Sync to DB via AJAX ---
        if (cartItemId) {
            $.ajax({
                url: _BASEURL + 'get_ajaxdata.php',
                type: 'POST',
                data: {
                    id: cartItemId,
                    qty: qty,
                    action: 'update_cart_quantity'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.dropdown_html) {
                            $('#header-cart-dropdown-menu').html(response.dropdown_html);
                        }
                    } else {
                        console.error("Database update failed: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error updating quantity: " + error);
                }
            });
        }
    }

});