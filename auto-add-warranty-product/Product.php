<?php

namespace autoAddWarrantyProduct;

use \TemplateLoader;

class Product
{

	public const WARRANTY_ALLOWED = 'allow_warranty_for_this_product';
	public const WARRANTY_LABEL_DISPLAY = 'This product has opted for warranty';
	private const WARRANTY_PRODUCT_ID = 4758;


	public function __construct()
	{
		$this->loader = TemplateLoader::get_instance();
		add_action('woocommerce_before_add_to_cart_button', array($this, 'add_warranty_field_option'), 10, 0);

		add_filter('woocommerce_add_cart_item_data', array($this, 'product_add_on_cart_item_data'), 10, 2);
		add_filter('woocommerce_get_item_data', array($this, 'product_add_on_display_cart'), 10, 2);

		add_action('woocommerce_add_order_item_meta', array($this, 'product_add_on_order_item_meta'), 10, 2);
		add_action('woocommerce_before_calculate_totals', array($this, 'update_warrany_product'), 20, 1);

		//add_action('wp_head', array($this, 'add_css'));
	}

	public function update_warrany_product($cart)
	{
		if (is_cart() || is_checkout()) {;
		} else {
			return;
		}

		// check for thank you page.
		if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
			return;
		}

		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}

		if (did_action('woocommerce_before_calculate_totals') >= 2) {
			return;
		}

		// Checking cart items
		$warranty_quantity = 0;
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			if (isset($cart_item[self::WARRANTY_ALLOWED]) && $cart_item[self::WARRANTY_ALLOWED] === 'Yes') {
				$warranty_quantity = $warranty_quantity + 1;
			}
		}


		$exist = $this->_is_product_exist_in_cart(self::WARRANTY_PRODUCT_ID);
		if ($warranty_quantity > 0 && $exist === false) {
			WC()->cart->add_to_cart(self::WARRANTY_PRODUCT_ID);
		}


		// Checking cart items
		$specific_ids = array(self::WARRANTY_PRODUCT_ID);
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			$product_id = $cart_item['data']->get_id();

			// Check for specific product IDs and change quantity
			if (in_array($product_id, $specific_ids)) {
				$cart->set_quantity($cart_item_key, $warranty_quantity); // Change quantity
			}
		}
	}

	/**
	 * Display warranty on single product page
	 */
	public function add_warranty_field_option()
	{
		global $post;
		$product_id = $post->ID;
		$allowed = get_field(self::WARRANTY_ALLOWED, $product_id);

		if (!$allowed) {
			return;
		}

		$data = array();

		$this->loader->get_template(
			'warranty-option-display.php',
			$data,
			__DIR__ . '/templates/',
			true
		);
	}

	public function product_add_on_cart_item_data($cart_item, $product_id)
	{
		if ($product_id == self::WARRANTY_PRODUCT_ID) {
			return $cart_item;
		}

		if (isset($_POST[self::WARRANTY_ALLOWED]) && $_POST[self::WARRANTY_ALLOWED] === 'Yes') {
			$cart_item[self::WARRANTY_ALLOWED] = sanitize_text_field($_POST[self::WARRANTY_ALLOWED]);
		}

		return $cart_item;
	}

	public function product_add_on_display_cart($data, $cart_item)
	{
		if (isset($cart_item[self::WARRANTY_ALLOWED]) && $cart_item[self::WARRANTY_ALLOWED] === 'Yes') {
			$data[] = array(
				'key' => __(self::WARRANTY_LABEL_DISPLAY, 'geny-woocommerce'),
				'value' => sanitize_text_field($cart_item[self::WARRANTY_ALLOWED]),
			);
		}

		return $data;
	}

	public function product_add_on_order_item_meta($item_id, $values)
	{
		if (isset($values[self::WARRANTY_ALLOWED]) && $values[self::WARRANTY_ALLOWED] === 'Yes') {
			wc_add_order_item_meta($item_id, self::WARRANTY_LABEL_DISPLAY, $values[self::WARRANTY_ALLOWED], true);
		}
	}

	public function add_css()
	{
		if (is_checkout()) {
			echo '<style>
			 .wfacp_mini_cart_item_title dl.variation{
			 	display: flex;
			 }
			</style>';
		}
	}

	public function _is_product_exist_in_cart($product_id)
	{
		$product_cart_id = WC()->cart->generate_cart_id($product_id);
		$cart_item_key = WC()->cart->find_product_in_cart($product_cart_id);

		if (!empty($cart_item_key)) {
			return true; //exist
		} else {
			return false;
		}
	}
}


new Product();
