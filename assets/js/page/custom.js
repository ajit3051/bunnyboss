$(document).ready(function () {
    $(document).on('click', '.add-cart-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.data('id');
        var qty = btn.siblings('.qty-input').val() || 1;
        var size = btn.closest('.product').find('.size-option.active').data('size') || btn.attr('data-size');

        if (!size) {
            showToast('Please select a size first', true);
            return;
        }

        btn.prop('disabled', true).html('<i class="icon-shopping-cart"></i><span>Adding...</span>');

        $.ajax({
            url: _BASEURL + 'get_ajaxdata.php',
            method: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: productId,
                quantity: qty,
                size: size
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('.cart-count').text(response.cart_count);
                    if (response.cart_total !== undefined) {
                        $('.cart-total-price').text('₹' + response.cart_total);
                    }
                    if (response.dropdown_html) {
                        $('#header-cart-dropdown-menu').html(response.dropdown_html);
                    }
                    showToast(response.message);
                } else {
                    showToast(response.message, true);
                }
            },
            error: function () {
                showToast('Something went wrong. Please try again.', true);
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="icon-shopping-cart"></i><span>Add to Cart</span>');
            }
        });
    });

    $(document).on('click', '.order-now-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        var productId = btn.data('id');
        var qty = btn.siblings('.qty-input').val() || 1;
        var size = btn.closest('.product').find('.size-option.active').data('size') || btn.attr('data-size');

        if (!size) {
            showToast('Please select a size first', true);
            return;
        }

        btn.prop('disabled', true).html('<i class="icon-rocket"></i><span>Ordering...</span>');

        $.ajax({
            url: _BASEURL + 'get_ajaxdata.php',
            method: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: productId,
                quantity: qty,
                size: size
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    window.location.href = _BASEURL + 'checkout.php';
                } else {
                    showToast(response.message, true);
                    btn.prop('disabled', false).html('<i class="icon-rocket"></i><span>Order Now</span>');
                }
            },
            error: function () {
                showToast('Something went wrong. Please try again.', true);
                btn.prop('disabled', false).html('<i class="icon-rocket"></i><span>Order Now</span>');
            }
        });
    });

    $(document).on('click', '.header-cart-remove', function (e) {
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
                    $('.cart-count').text(response.cart_count);
                    if (response.cart_total !== undefined) {
                        $('.cart-total-price').text('₹' + response.cart_total);
                    }
                    if (response.dropdown_html) {
                        $('#header-cart-dropdown-menu').html(response.dropdown_html);
                    }
                    $('tr[data-id="' + id + '"]').fadeOut(300, function () {
                        $(this).remove();
                        if (typeof updateCartTotals === 'function') {
                            updateCartTotals();
                        }
                    });
                    showToast('Item removed from cart');
                }
            }
        });
    });

    $(document).on('click', '.product-size-select .size-option', function (e) {
        if ($(this).hasClass('disabled') || parseInt($(this).attr('data-qty') || $(this).data('qty')) <= 0) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        var sizeBox = $(this).closest('.product-size-select');
        sizeBox.find('.size-option').removeClass('active');
        $(this).addClass('active');

        var card = $(this).closest('.product');
        var selectedSize = $(this).data('size');
        card.find('.add-cart-btn, .order-now-btn').attr('data-size', selectedSize);

        // Dynamic Selling Price update
        var rawPrice = $(this).attr('data-price') !== undefined ? $(this).attr('data-price') : $(this).data('price');
        if (rawPrice !== undefined && rawPrice !== null && rawPrice !== '') {
            var numPrice = parseFloat(rawPrice);
            var formattedPrice = isNaN(numPrice) ? rawPrice : numPrice.toFixed(2);
            var $priceTarget = card.length > 0 ? card.find('.product-price') : $('.product-details .product-price, .product-details-top .product-price');
            if ($priceTarget.length > 0) {
                $priceTarget.text('₹' + formattedPrice);
            }
        }

        // Dynamic Stock Quantity update
        var newQty = $(this).data('qty');
        if (newQty !== undefined && newQty !== null) {
            var qtyNum = parseInt(newQty);
            
            // 1) Product card action update (for catalog / product list cards)
            if (card.length > 0) {
                var cardAction = card.find('.product-action');
                if (cardAction.length > 0) {
                    var cardProdId = cardAction.data('id') || card.find('.add-cart-btn, .order-now-btn').data('id') || card.find('[data-id]').data('id');
                    if (cardProdId) {
                        cardAction.data('id', cardProdId);
                    }
                    if (qtyNum <= 0) {
                        cardAction.html('<a class="btn-product btn-out-of-stock disabled" role="button" style="width: 100%; background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none;"><i class="icon-ban"></i><span>Out of Stock</span></a>');
                    } else {
                        cardAction.html('<a class="btn-product btn-cart add-cart-btn" role="button" data-id="' + cardProdId + '" data-size="' + selectedSize + '"><i class="icon-shopping-cart"></i><span>Add to Cart</span></a><a class="btn-product btn-order-now order-now-btn" role="button" data-id="' + cardProdId + '" data-size="' + selectedSize + '"><i class="icon-rocket"></i><span>Order Now</span></a>');
                    }
                }
            }

            // 2) Detail page action wrapper
            var actionWrapper = $('#product-action-wrapper');
            if (actionWrapper.length > 0) {
                var detailProdId = actionWrapper.data('id') || $('.product-detail-btn-cart').data('id') || $('.product-detail-btn-order-now').data('id');
                if (detailProdId) {
                    actionWrapper.data('id', detailProdId);
                }
                if (qtyNum <= 0) {
                    actionWrapper.html('<div class="product-details-action-split d-flex mb-3"><a href="JavaScript:void(0);" class="btn-product btn-out-of-stock disabled flex-fill" style="background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none; padding: 1.2rem; text-align: center; font-weight: 600;"><i class="icon-ban"></i><span>Out of Stock</span></a></div>');
                } else {
                    actionWrapper.html('<div class="product-details-action-split d-flex mb-3"><a href="JavaScript:void(0);" class="btn-product btn-cart product-detail-btn-cart flex-fill mr-2" data-id="' + detailProdId + '"><i class="icon-shopping-cart"></i><span>add to cart</span></a><a href="JavaScript:void(0);" class="btn-product btn-order-now product-detail-btn-order-now flex-fill ml-2" data-id="' + detailProdId + '"><i class="icon-rocket"></i><span>order now</span></a></div>');
                }
            }
        }
    });

    $(document).on('click', '.btn-share', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var shareUrl = $(this).attr('data-url') || $(this).data('url') || window.location.href;
        var shareTitle = $(this).attr('data-title') || $(this).data('title') || document.title;

        if (navigator.share) {
            navigator.share({
                title: shareTitle,
                text: shareTitle,
                url: shareUrl
            }).catch(function (err) {
                if (err && err.name !== 'AbortError') {
                    copyToClipboard(shareUrl);
                }
            });
        } else {
            copyToClipboard(shareUrl);
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function () {
                    showToast('Product link copied to clipboard!');
                }).catch(function () {
                    copyFallback(text);
                });
            } else {
                copyFallback(text);
            }
        }

        function copyFallback(text) {
            var dummy = document.createElement('textarea');
            dummy.style.position = 'fixed';
            dummy.style.opacity = '0';
            dummy.value = text;
            document.body.appendChild(dummy);
            dummy.select();
            dummy.setSelectionRange(0, 99999);
            try {
                document.execCommand('copy');
                showToast('Product link copied to clipboard!');
            } catch (err) {
                showToast('Product link copied!');
            }
            document.body.removeChild(dummy);
        }
    });

    function showToast(message, isError) {
        var toast = $('#toast');
        if (toast.length === 0) {
            toast = $('<div id="toast" style="display:none; position:fixed; bottom:30px; left:50%; transform:translateX(-50%); z-index:999999; color:#fff; padding:12px 24px; border-radius:30px; font-size:14px; font-weight:600; box-shadow:0 6px 20px rgba(0,0,0,0.25); text-align:center;"></div>');
            $('body').append(toast);
        }
        toast.text(message);
        toast.css('background', isError ? '#dc3545' : '#333');
        toast.stop(true, true).fadeIn(200);
        setTimeout(function () {
            toast.fadeOut(300);
        }, 2500);
    }
});
