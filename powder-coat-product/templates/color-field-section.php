<?php
$color_field_setting = get_custom_color_settings( $product_id );
$color_field_label = ! empty( $color_field_setting['label'] ) ? $color_field_setting['label'] : 'Custom Color Option';
$color_field_image = ! empty( $color_field_setting['image'] ) ? $color_field_setting['image'] : false;
?>
<div class="options_group paint-color">
    <div class="variations variations--custom-addon">
        <div class="variation__item">
            <div class="variation__item-image">
            <?php
                if ( $color_field_image != false ) :
            ?>
                <img src="<?php echo $color_field_image;?>" alt="" />
            <?php
                endif;
            ?>
            </div>
            <div class="variation__item-content">
                <div class="variation__item-name label">
                    <?php echo $color_field_label;?> <span>(+$<?php echo $additional_price; ?>)</span>
                </div>
                <div class="variation__item-elem value">
                    <div class="custom-color-form-elem">
                        <input type="text" class="short" name="<?php echo $color_field_name; ?>" id="<?php echo $color_field_name; ?>" value="<?php echo $input_value; ?>" placeholder="Enter color code here (ex: PTB-10054)">
                        <div>
                            <div class="ctop" style="color:#6c6c6c;">Get color code from</div>
                            <span><a target="_blank" href="https://www.prismaticpowders.com/">PRISMATICPOWDERS<i class="fa fa-external-link"></i></a></span>
                        </div>
                    </div>
                    <div class="paint-notes">
                        <?php if(empty($color_settings['color_description'])) :?>
                        <?php echo get_option('geny_custom_default_text'); ?>
                    <?php else: ?>
                        <?php echo $color_settings['color_description']; ?>
                    <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>