<?php

Class SingleProduct {
	private const COLOR_FIELD_NAME = 'geny_paint_color_code';
	private const COLOR_DISPLAY_NAME = 'Paint Color Code';
	private const ADDITIONAL_PRICE_TEXT = 'Custom Color Charge';
	private const ADDITIONAL_PRICE = '300';

	public function __construct() {
		$this->loader = TemplateLoader::get_instance();
		add_action('woocommerce_before_single_variation', array($this, 'add_custom_field_option'), 10, 0);
		//add_filter('woocommerce_add_to_cart_validation', array($this, 'add_to_cart_validation'), 15, 3);

		add_filter('woocommerce_add_cart_item_data', array($this, 'product_add_on_cart_item_data'), 10, 2);
		add_filter('woocommerce_get_item_data', array($this, 'product_add_on_display_cart'), 10, 2);

		add_action('woocommerce_add_order_item_meta', array($this, 'product_add_on_order_item_meta'), 10, 2);

		add_filter('woocommerce_order_item_product', array($this, 'product_add_on_display_order'), 10, 2);
		add_action('woocommerce_cart_calculate_fees', array($this, 'add_total_color_code_fee'));

		//add_action('woocommerce_before_calculate_totals', array($this, 'add_custom_price'));
	}
	/**
	 * Display input on single product page
	 */
	public function add_custom_field_option() {
		global $product;
		$allowed = $product->get_meta(Product::ADMIN_IS_PAINT_PROD);

		if ($allowed != 'yes') {
			return;
		}

		$input_value = isset($_POST[self::COLOR_FIELD_NAME]) ? sanitize_text_field($_POST[self::COLOR_FIELD_NAME]) : '';

		$data = array(
			'product_id' => absint( $product->get_id() ),
			'input_value' => $input_value,
			'additional_price' => self::ADDITIONAL_PRICE,
			'color_field_name' => self::COLOR_FIELD_NAME,
		);

		$this->loader->get_template(
			'color-field-section.php',
			$data,
			__DIR__ . '/templates/',
			true
		);

	}

	/**
	 * Validate when adding to cart
	 *
	 * @param bool $passed
	 * @param int $product_id
	 * @param int $quantity
	 * @return bool
	 */
	public function add_to_cart_validation($passed, $product_id, $qty) {
		if (isset($_POST[self::COLOR_FIELD_NAME]) && sanitize_text_field($_POST[self::COLOR_FIELD_NAME]) == '') {
			wc_add_notice(__('Paint color is missing.', 'geny-woocommerce'), 'error');
			$passed = false;
		}

		return $passed;
	}

	public function product_add_on_cart_item_data($cart_item, $product_id) {
		if (isset($_POST[self::COLOR_FIELD_NAME])) {
			$cart_item[self::COLOR_FIELD_NAME] = sanitize_text_field($_POST[self::COLOR_FIELD_NAME]);
		}

		return $cart_item;
	}

	public function product_add_on_display_cart($data, $cart_item) {
		if (isset($cart_item[self::COLOR_FIELD_NAME]) && !empty($cart_item[self::COLOR_FIELD_NAME])) {
			$data[] = array(
				'key' => __(self::COLOR_DISPLAY_NAME, 'geny-woocommerce'),
				'value' => sanitize_text_field($cart_item[self::COLOR_FIELD_NAME]),
			);

			$data[] = array(
				'key' => __(self::ADDITIONAL_PRICE_TEXT, 'geny-woocommerce'),
				'value' => '$' . self::ADDITIONAL_PRICE,
			);
		}

		return $data;
	}

	public function product_add_on_order_item_meta($item_id, $values) {
		if (isset($values[self::COLOR_FIELD_NAME]) && !empty($values[self::COLOR_FIELD_NAME])) {
			wc_add_order_item_meta($item_id, self::COLOR_DISPLAY_NAME, $values[self::COLOR_FIELD_NAME], true);
			wc_add_order_item_meta($item_id, self::ADDITIONAL_PRICE_TEXT, '$' . self::ADDITIONAL_PRICE, true);
		}
	}

	/**
	 * Restore custom field to product meta when product retrieved for order item.
	 * Meta fields will be automatically displayed if not prefixed with _
	 *
	 * @param WC_Product $product
	 * @param WC_Order_Item_Product $order_item
	 * @return array
	 */
	public function product_add_on_display_order($product, $order_item) {

		if ($order_item->get_meta(self::COLOR_DISPLAY_NAME)) {
			$product->add_meta_data(self::COLOR_DISPLAY_NAME, $order_item->get_meta(self::COLOR_DISPLAY_NAME), true);
			$product->add_meta_data(self::ADDITIONAL_PRICE_TEXT, $order_item->get_meta(self::ADDITIONAL_PRICE_TEXT), true);
		}

		return $product;
	}

	public function add_total_color_code_fee($cart) {
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}

		$fee = 0;
		foreach (WC()->cart->get_cart() as $item) {
			if (isset($item[self::COLOR_FIELD_NAME]) && !empty($item[self::COLOR_FIELD_NAME])) {
				$fee = $fee + self::ADDITIONAL_PRICE;
			}
		}

		if ($fee !== 0) {
			$cart->add_fee(__('Total custom color code fee', 'geny-woocommerce'), $fee, false);
		}
	}

	function add_custom_price($cart_object) {
		if (did_action('woocommerce_before_calculate_totals') >= 2) {
			return;
		}

		$custom_price = self::ADDITIONAL_PRICE;
		foreach ($cart_object->cart_contents as $key => $cart_item) {
			if (isset($cart_item[self::COLOR_FIELD_NAME]) && !empty($cart_item[self::COLOR_FIELD_NAME])) {
				$original_price = $cart_item['data']->get_price();
				$new_price = $original_price + $custom_price;
				$cart_item['data']->set_price($new_price);
			}
		}
	}
}
new SingleProduct();