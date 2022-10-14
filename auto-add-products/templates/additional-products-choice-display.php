<?php

namespace autoAddProducts;
?>


<div class="exclude-variations variations--custom-addon">
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
                                $ids = implode('|', $option['products']);
                                $total_price = $product_obj->get_products_price($option['products']);

                                $formatted_total_price = '';
                                if (!empty($ids)) {
                                    $formatted_total_price = ' (+$' . $total_price . ')';
                                }
                        ?>

                                <option data-price="<?php echo $total_price; ?>" value="<?php echo $ids; ?>"><?php echo $label . $formatted_total_price ?></option>

                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>