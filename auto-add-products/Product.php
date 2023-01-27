<?php

namespace autoAddProducts;

use \TemplateLoader;
use \Helper;
use autoAddProducts\Admin\ACF;


class Product
{

	/* ACF and form field name */
	public const VERSION = 1.2;
	public const FIELD_NAME = 'additional_product_lists';
	public const SESS_ADDITIONAL_DATA = 'sess_additional_product_data';


	public function __construct()
	{
		$this->loader = TemplateLoader::get_instance();
		add_action('woocommerce_before_single_variation', array($this, 'add_custom_field_option'), 10, 0);
		add_action('woocommerce_before_add_to_cart_button', array($this, 'add_custom_field_option'), 10, 0);
		add_filter('woocommerce_add_cart_item_data', array($this, 'product_add_on_cart_item_data'), 10, 2);
		add_action('woocommerce_before_calculate_totals', array($this, 'update_products'), 20, 1);
		add_action('wp_head', array($this, 'add_css_or_js'), 1);
		add_action('wp_enqueue_scripts', array($this, 'product_enqueue_scripts'));
	}

	public function product_enqueue_scripts()
	{
		global $post;
		$product_id = $post->ID;
		$additional_products_list = get_field(self::FIELD_NAME, $product_id);
		if (empty($additional_products_list) || empty($additional_products_list[0])) {
			return;
		}

		wp_enqueue_script('custom-script', get_stylesheet_directory_uri()  . '/woocommerce-addons/auto-add-products/assets/js/product-v7.js', array('jquery'), self::VERSION);

		$price = '';
		if (is_product()) {
			global $post;

			$helper = Helper::get_instance();
			if ($helper->_is_variable_product($post->ID)) {
				$price = '';
			} else {
				$product = wc_get_product($post->ID);
				$price = $product->get_price();
			}

			if ($helper->is_dealer()) {
				$is_dealer = 1;
			} else {
				$is_dealer = 0;
			}
		}

		$inline = array(
			'_geny_simple_product_price' => $price,
			'_geny_is_dealer' => $is_dealer,
		);

		wp_add_inline_script('custom-script', 'const LOCAL = ' . json_encode($inline), 'before');
	}

	/**
	 * Display choice on single product page
	 */
	public function add_custom_field_option()
	{
		if (isset($_GET['genytest'])) {
			WC()->cart->empty_cart();
			exit;
		}

		global $post;
		$product_id = $post->ID;

		$helper = Helper::get_instance();
		$product = $helper->_is_variable_product($product_id);
		if ($product !== false && current_filter() == 'woocommerce_before_add_to_cart_button') {
			return;
		}

		$additional_products_list = get_field(self::FIELD_NAME, $product_id);
		if (empty($additional_products_list) || empty($additional_products_list[0])) {
			return;
		}

		$data = array(
			'additional_products_list' => $additional_products_list,
			'product_obj' => $this,
		);

		$this->loader->get_template(
			'additional-products-choice-display.php',
			$data,
			__DIR__ . '/templates/',
			true
		);
	}

	public function product_add_on_cart_item_data($cart_item, $product_id)
	{

		if (isset($_POST[self::FIELD_NAME]) && !empty(array_filter($_POST[self::FIELD_NAME]))) {
			$sess_data = $_POST[self::FIELD_NAME];
			WC()->session->set(self::SESS_ADDITIONAL_DATA, $sess_data);
		} else {
			WC()->session->set(self::SESS_ADDITIONAL_DATA, array());
		}

		return $cart_item;
	}

	public function update_products($cart)
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


		$sess_data = WC()->session->get(self::SESS_ADDITIONAL_DATA);
		if (empty($sess_data)) {
			WC()->session->get(self::SESS_ADDITIONAL_DATA, array());
			return;
		}

		if (is_array($sess_data)) {
			foreach ($sess_data as $ids) {
				$product_ids = explode('|', $ids);
				if (!empty($product_ids)) {
					foreach ($product_ids as $id) {
						if (!empty($id)) {
							$this->add_product_to_cart($id);
						}
					}
				}
			}
		}

		// empty the session variable.
		WC()->session->get(self::SESS_ADDITIONAL_DATA, array());
	}


	public function add_product_to_cart($id)
	{
		$helper = Helper::get_instance();
		$product = $helper->_is_variable_product($id);

		if ($product !== false) {
			WC()->cart->add_to_cart($product->get_parent_id(), 1, $id);
		} else {
			WC()->cart->add_to_cart($id);
		}
	}

	public function add_css_or_js()
	{
		$data = array();
		if (is_product()) {
			$this->loader->get_template(
				'styles.css.php',
				$data,
				__DIR__ . '/templates/',
				true
			);
		}
	}

	public function get_products_price($product_ids)
	{
		$total_price = 0.00;

		if (empty($product_ids)) {
			return $total_price;
		}

		$total_price = 0.00;
		if (is_array($product_ids)) {
			foreach ($product_ids as $id) {
				$product = wc_get_product($id);
				$total_price = $total_price + $product->get_price();
			}
		}

		$total_price = number_format((float)$total_price, 2, '.', '');
		return $total_price;
	}
}


new Product();
