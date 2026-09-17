$(document).on('click', '.product-detail-btn-cart', function (e) {
    e.preventDefault();
    var btn = $(this);
    var productId = btn.data('id');
    var qty = parseInt($('#qty').val()) || 1;
    var size = $('.product-size-select').find('.size-option.active').data('size') || btn.attr('data-size');

    if (!size) {
        showToast('Please select a size first', true);
        return;
    }

    if (!productId) {
        alert('Product ID missing.');
        return;
    }

    btn.prop('disabled', true); // prevent double-click

    $.ajax({
        url: _BASEURL + 'get_ajaxdata.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: qty,
            size: size,
            action: 'add_to_cart'
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
            btn.prop('disabled', false);
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

$(document).on('click', '.product-detail-btn-order-now', function (e) {
    e.preventDefault();
    var btn = $(this);
    var productId = btn.data('id');
    var qty = parseInt($('#qty').val()) || 1;
    var size = $('.product-size-select').find('.size-option.active').data('size') || btn.attr('data-size');

    if (!size) {
        showToast('Please select a size first', true);
        return;
    }

    if (!productId) {
        alert('Product ID missing.');
        return;
    }

    btn.prop('disabled', true);

    $.ajax({
        url: _BASEURL + 'get_ajaxdata.php',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: qty,
            size: size,
            action: 'add_to_cart'
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                window.location.href = _BASEURL + 'checkout.php';
            } else {
                showToast(response.message, true);
                btn.prop('disabled', false);
            }
        },
        error: function () {
            showToast('Something went wrong. Please try again.', true);
            btn.prop('disabled', false);
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

// Remove elevateZoom overlay containers & initialize smooth zoom-in on hover
$(document).ready(function () {
    $('.zoomContainer').remove();
    setTimeout(function () {
        $('.zoomContainer').remove();
    }, 400);

    var $mainImageContainer = $('.product-main-image');
    var $mainImage = $('#product-zoom');

    if ($mainImageContainer.length && $mainImage.length) {
        $mainImageContainer.on('mouseenter', function () {
            $mainImage.css({
                'transform': 'scale(2.2)',
                'cursor': 'zoom-in'
            });
        });

        $mainImageContainer.on('mousemove', function (e) {
            var rect = this.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;

            x = Math.max(0, Math.min(100, x));
            y = Math.max(0, Math.min(100, y));

            $mainImage.css('transform-origin', x + '% ' + y + '%');
        });

        $mainImageContainer.on('mouseleave', function () {
            $mainImage.css({
                'transform': 'scale(1)',
                'transform-origin': 'center center'
            });
        });
    }
});

// Handle Product Gallery Thumbnail Click & Image Swap
$(document).on('click', '.product-gallery-item', function (e) {
    e.preventDefault();
    var $this = $(this);
    var newImgSrc = $this.attr('data-image') || $this.find('img').attr('src');
    var newZoomSrc = $this.attr('data-zoom-image') || newImgSrc;

    if (!newImgSrc) return;

    $('#product-zoom-gallery').find('.product-gallery-item').removeClass('active');
    $this.addClass('active');

    var $mainImg = $('#product-zoom');
    if ($mainImg.length) {
        $mainImg.attr('src', newImgSrc);
        $mainImg.attr('data-zoom-image', newZoomSrc);

        // Reset zoom transform scale on image swap
        $mainImg.css({
            'transform': 'scale(1)',
            'transform-origin': 'center center'
        });
    }
});

// Fullscreen / Lightbox Gallery Button Icon Click Handler
$(document).on('click', '#btn-product-gallery', function (e) {
    e.preventDefault();
    if (!$.fn.magnificPopup) return;

    var galleryItems = [];
    $('#product-zoom-gallery .product-gallery-item').each(function () {
        var src = $(this).attr('data-zoom-image') || $(this).attr('data-image') || $(this).find('img').attr('src');
        if (src) {
            galleryItems.push({
                src: src,
                type: 'image'
            });
        }
    });

    if (galleryItems.length === 0) {
        var mainSrc = $('#product-zoom').attr('data-zoom-image') || $('#product-zoom').attr('src');
        if (mainSrc) {
            galleryItems.push({ src: mainSrc, type: 'image' });
        }
    }

    var activeIdx = $('#product-zoom-gallery .product-gallery-item.active').index();
    if (activeIdx < 0) activeIdx = 0;

    $.magnificPopup.open({
        items: galleryItems,
        type: 'image',
        gallery: {
            enabled: true
        },
        fixedContentPos: false,
        removalDelay: 600,
        closeBtnInside: false
    }, activeIdx);
});