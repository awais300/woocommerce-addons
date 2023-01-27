jQuery(document).ready(function($) {
    var _geny_main_display_price = '';
    var _geny_wholesale_price = '';

    $(document).on('found_variation', 'form.cart', function(event, variation) {
        console.log(event);
        console.log(variation);

        let price = variation.display_price;
        let additional_price = Number(get_pirce_from_custom_dropdown());

        if(LOCAL._geny_is_dealer){
            _geny_main_display_price = Number(variation.wholesale_price);
        } else{
            _geny_main_display_price = Number(price);
        }
        

        geny_display_main_price(_geny_main_display_price, additional_price);


    });

    $(document).on('change', '.variation__item select.additional-select-option', function() {
        let additional_price = Number(get_pirce_from_custom_dropdown());
        geny_display_main_price(_geny_main_display_price, additional_price);
    });

    $(document).on('change', '.exclude-variations.simple .variation__item select.additional-select-option', function() {
        let additional_price = Number(get_pirce_from_custom_dropdown());
        geny_display_main_price_simple_product(LOCAL._geny_simple_product_price, additional_price);
    });

    function get_pirce_from_custom_dropdown() {
        $selects = $('.variation__item select.additional-select-option');

        let total = 0;
        if ($selects.length > 0) {
            $.each($selects, function(index, select) {
                option = select.options[select.selectedIndex];
                price = option.getAttribute('data-price');
                price = Number(price);
                total = total + price;
            });
        }

        return total;
    }

    function geny_display_main_price(main_price, additional_price) {
        if (!main_price) {
            return;
        }

        new_price = Number(main_price) + Number(additional_price);
        new_price = Number(new_price).toFixed(2);

        html = '<span class="woocommerce-Price-currencySymbol">$</span>' + new_price;

        if($('.woocommerce-variation-price ins').length) {
            $('.woocommerce-variation-price ins span.woocommerce-Price-amount bdi').html(html);
        } else{
             $('.woocommerce-variation-price span.woocommerce-Price-amount bdi').html(html);
        }

        
    }

    function geny_display_main_price_simple_product(main_price, additional_price) {
        if (!main_price) {
            return;
        }

        if (!additional_price) {
            new_price = LOCAL._geny_simple_product_price;
        } else {
            new_price = Number(main_price) + Number(additional_price);
            new_price = Number(new_price).toFixed(2);
        }

        html = '<span class="woocommerce-Price-currencySymbol">$</span>' + new_price;
        $('.price span.woocommerce-Price-amount bdi').html(html);
    }

});