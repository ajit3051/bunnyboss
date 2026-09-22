$(document).ready(function() {

    var currentPage = 1;

    // 1. Listen for changes on any filter checkbox
    $(document).on('change', '.filter-checkbox', function() {
        currentPage = 1;
        filterProducts();
    });

    // 2. Listen for clicks on pagination links
    $(document).on('click', '#pagination-container .page-link[data-page]', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = page;
            filterProducts();
            if ($('#product-list').length) {
                var targetOffset = $('#product-list').offset().top - 100;
                if (targetOffset < 0) targetOffset = 0;
                $('html, body').animate({ scrollTop: targetOffset }, 300);
            }
        }
    });

    // 3. Safely attach to the existing theme slider instance
    var priceSlider = document.getElementById('price-slider');

    if (priceSlider) {
        var attachSlider = function() {
            if (priceSlider.noUiSlider) {
                priceSlider.noUiSlider.off('change');
                priceSlider.noUiSlider.off('set');
                priceSlider.noUiSlider.on('change', function() {
                    currentPage = 1;
                    filterProducts();
                });
                priceSlider.noUiSlider.on('set', function() {
                    currentPage = 1;
                    filterProducts();
                });
            }
        };

        attachSlider();
        setTimeout(attachSlider, 300);
        setTimeout(attachSlider, 800);
    }

    filterProducts();

    // 4. Core Filter Processing Function
    function filterProducts() {
        var categories = [];
        var sizes = [];
        var colors = [];
        var brands = [];

        $('.filter-checkbox:checked').each(function() {
            var type = $(this).data('type');
            var value = $(this).val();

            if (type === 'category') categories.push(value);
            if (type === 'size')     sizes.push(value);
            if (type === 'color')    colors.push(value);
            if (type === 'brand')    brands.push(value);
        });

        var minPrice = null;
        var maxPrice = null;

        var priceSliderElem = document.getElementById('price-slider');
        if (priceSliderElem && priceSliderElem.noUiSlider) {
            var rawValues = priceSliderElem.noUiSlider.get();
            if (!Array.isArray(rawValues)) {
                rawValues = [rawValues];
            }
            if (rawValues.length >= 2) {
                var minStr = String(rawValues[0]).replace(/[^0-9.]/g, '');
                var maxStr = String(rawValues[1]).replace(/[^0-9.]/g, '');
                if (minStr !== '') minPrice = parseFloat(minStr);
                if (maxStr !== '') maxPrice = parseFloat(maxStr);
            }
        }

        if (minPrice === null || maxPrice === null || isNaN(minPrice) || isNaN(maxPrice)) {
            var priceText = $('#filter-price-range').text();
            if (priceText) {
                var cleanText = priceText.replace(/-/g, " ");
                var parts = cleanText.trim().split(/\s+/);
                var numbers = [];
                for (var p = 0; p < parts.length; p++) {
                    var numStr = parts[p].replace(/[^0-9.]/g, '');
                    if (numStr !== '') {
                        numbers.push(parseFloat(numStr));
                    }
                }
                if (numbers.length >= 2) {
                    minPrice = numbers[0];
                    maxPrice = numbers[1];
                }
            }
        }

        if (minPrice === null || isNaN(minPrice)) minPrice = 0;
        if (maxPrice === null || isNaN(maxPrice)) maxPrice = 999999;

        var $grid = $('#product-list');
        $grid.css('opacity', '0.5');

        updateActiveCount();

        // 5. Send Request via AJAX
        $.ajax({
            url: _BASEURL + 'get_filtered_products.php',
            type: 'POST',
            data: {
                categories: categories,
                sizes: sizes,
                colors: colors,
                brands: brands,
                min_price: minPrice, 
                max_price: maxPrice,
                page: currentPage
            },
            dataType: 'json',
            success: function(response) {
                $grid.css('opacity', '1');
                if (typeof response === 'object' && response !== null) {
                    $grid.html(response.grid_html || '');
                    $('#pagination-container').html(response.pagination_html || '');
                } else {
                    $grid.html(response);
                }
            },
            error: function(xhr, status, error) {
                $grid.css('opacity', '1');
                console.error("Filtering Error: " + error);
            }
        });
    }

    // ========================================================
    // Mobile Drawer Controls & Active Filter Badge Tracking
    // ========================================================

    // Open Drawer
    $(document).on('click', '#open-mobile-filter, #floating-filter-btn', function(e) {
        e.preventDefault();
        $('body').addClass('mobile-filter-open');
    });

    // Close Drawer
    $(document).on('click', '#close-mobile-filter, #mobile-filter-backdrop, #apply-mobile-filter', function(e) {
        e.preventDefault();
        $('body').removeClass('mobile-filter-open');

        // If user tapped Apply Filters, scroll to top of product list
        if ($(this).attr('id') === 'apply-mobile-filter' && $('#product-list').length) {
            var targetOffset = $('#product-list').offset().top - 90;
            if (targetOffset < 0) targetOffset = 0;
            $('html, body').animate({ scrollTop: targetOffset }, 300);
        }
    });

    // Clear All Filters
    $(document).on('click', '#mobile-clear-all', function(e) {
        e.preventDefault();
        $('.filter-checkbox').prop('checked', false);

        var priceSliderElem = document.getElementById('price-slider');
        if (priceSliderElem && priceSliderElem.noUiSlider) {
            var minVal = parseFloat($(priceSliderElem).data('min')) || 0;
            var maxVal = parseFloat($(priceSliderElem).data('max')) || 9999;
            priceSliderElem.noUiSlider.set([minVal, maxVal]);
        }

        currentPage = 1;
        updateActiveCount();
        filterProducts();
    });

    // Calculate and update active filter count badge
    function updateActiveCount() {
        var count = $('.filter-checkbox:checked').length;
        var priceSliderElem = document.getElementById('price-slider');
        if (priceSliderElem && priceSliderElem.noUiSlider) {
            var raw = priceSliderElem.noUiSlider.get();
            var minInit = parseFloat($(priceSliderElem).data('min')) || 0;
            var maxInit = parseFloat($(priceSliderElem).data('max')) || 9999;
            if (Array.isArray(raw) && raw.length >= 2) {
                var currentMin = parseFloat(String(raw[0]).replace(/[^0-9.]/g, '')) || minInit;
                var currentMax = parseFloat(String(raw[1]).replace(/[^0-9.]/g, '')) || maxInit;
                if (Math.abs(currentMin - minInit) > 1 || Math.abs(currentMax - maxInit) > 1) {
                    count += 1;
                }
            }
        }

        if (count > 0) {
            $('#active-filter-count, #floating-filter-count').text(count).show();
            $('#open-mobile-filter').addClass('btn-primary text-white').removeClass('btn-outline-dark');
        } else {
            $('#active-filter-count, #floating-filter-count').text('0').hide();
            $('#open-mobile-filter').removeClass('btn-primary text-white').addClass('btn-outline-dark');
        }
    }

    // Scroll listener for sticky floating filter button on mobile
    $(window).on('scroll', function() {
        if ($(window).width() <= 991) {
            var $grid = $('#product-list');
            if ($grid.length) {
                var gridTop = $grid.offset().top - 120;
                var gridBottom = gridTop + $grid.outerHeight() - 200;
                var scrollPos = $(window).scrollTop();
                if (scrollPos > gridTop && scrollPos < gridBottom) {
                    $('#mobile-floating-filter-wrap').addClass('visible');
                } else {
                    $('#mobile-floating-filter-wrap').removeClass('visible');
                }
            }
        }
    });

    // Initial count check
    setTimeout(updateActiveCount, 400);
});