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
                        <i>Applies ONLY to the drop hitch, not the ball mount and pintle lock.</i>
                        <br />
                        <i>Lead time 6-8 weeks for custom colors.</i>
                        <br />
                        <i>Please call <a class="a-exclude" href="(574) 218-6363">(574) 218-6363</a> if you want an attachment powder coated.</i>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>