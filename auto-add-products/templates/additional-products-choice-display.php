<?php

namespace autoAddProducts;

use \Helper;
?>

<?php
global $post;
$helper = Helper::get_instance();
$product = $helper->_is_variable_product($post->ID);
if ($product === false) {
    $css = 'simple';
} else {
    $css = 'variable';
}
?>

<div class="exclude-variations variations--custom-addon <?php echo $css; ?>">
    <?php
    foreach ($additional_products_list as $key => $product) :
    ?>
        <div class="variation__item">
            <div class="variation__item-image">
                <?php
                if (!empty($product['product_image'])) {
                    echo wp_get_attachment_image($product['product_image'], 'full');
                }
                ?>
            </div>
            <div class="variation__item-content">
                <div class="variation__item-name label">
                    <?php
                    if (!empty($product['product_label'])) {
                        echo $product['product_label'];
                    }
                    ?>
                </div>
                <div class="variation__item-elem value">
                    <select class="additional-select-option" id="additional-select-<?php echo $key; ?>" name="<?php echo Product::FIELD_NAME; ?>[]">
                        <?php if (!empty($product['product_dropdown_options']) && !empty($product['product_dropdown_options'][0])) :

                            $options = $product['product_dropdown_options'];
                            foreach ($options as $option) :
                                $label  = $option['label'];

                                $option_discount_with_products = array();
                                $option_discount_with_products['product_ids'] = $option['products'];
                                $option_discount_with_products['discount'] = $option['product_discount'];

                                $total_price = $product_obj->get_products_price($option['products']);

                                $formatted_total_price = '';
                                if (!empty($option['products'])) {
                                    $formatted_total_price = ' (+$' . $total_price . ')';
                                }
                        ?>

                                <option data-price="<?php echo $total_price; ?>" data-discount="<?php echo $option['product_discount']; ?>" value='<?php echo json_encode($option_discount_with_products) ?>'><?php echo $label . $formatted_total_price ?></option>

                        <?php endforeach;
                        endif; ?>
                    </select>
                    <div class="desc">
                        <?php
                        if (!empty($product['product_description'])) {
                            echo $product['product_description'];
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>