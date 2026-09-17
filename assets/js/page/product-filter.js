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
});