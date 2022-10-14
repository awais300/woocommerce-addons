<?php

Class Product {
	public const ADMIN_IS_PAINT_PROD = '_geny_is_paint_product';

	public function __construct() {
		add_action('woocommerce_product_options_general_product_data', array($this, 'woocommerce_product_custom_fields'));
		add_action('woocommerce_admin_process_product_object', array($this, 'product_custom_fields_save'));
	}

	public function woocommerce_product_custom_fields() {
		global $product_object;

		/*if ($product_object->get_id() != 132649) {
			return;
		}*/

		echo '<div class="product_custom_field">';
		woocommerce_wp_checkbox(array(
			'id' => self::ADMIN_IS_PAINT_PROD,
			'description' => __('Check this option to let user enter custom paint code for this product', 'geny-woocommerce'),
			'label' => __('Allow this product to enter custom paint code?', 'geny-woocommerce'),
			'desc_tip' => 'true',
		));

		echo '</div>';
	}

	public function product_custom_fields_save($product) {
		$woocommerce_checkbox = isset($_POST[self::ADMIN_IS_PAINT_PROD]) ? 'yes' : 'no';
		$product->update_meta_data(self::ADMIN_IS_PAINT_PROD, $woocommerce_checkbox);
	}
}

new Product();