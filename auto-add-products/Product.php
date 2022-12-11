<?php

namespace autoAddProducts;

use \TemplateLoader;
use \Helper;
use autoAddProducts\Admin\ACF;


class Product
{

	/* ACF and form field name */
	public const VERSION = 1.1;
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
		//add_action('woocommerce_cart_calculate_fees', array($this, 'add_additional_product_discount'));
		add_filter('woocommerce_get_cart_item_from_session', array($this, 'get_user_custom_data_session'), 1, 3 );

	}

	public function product_enqueue_scripts()
	{
		global $post;
		$product_id = $post->ID;
		$additional_products_list = get_field(self::FIELD_NAME, $product_id);
		if (empty($additional_products_list) || empty($additional_products_list[0])) {
			return;
		}

		wp_enqueue_script('custom-script', get_stylesheet_directory_uri()  . '/woocommerce-addons/auto-add-products/assets/js/product-v4.js?=ver' . self::VERSION, array('jquery'));

		if (is_product()) {
			global $post;

			$helper = Helper::get_instance();
			if ($helper->_is_variable_product($post->ID)) {
				$price = '';
			} else {
				$product = wc_get_product($post->ID);
				$price = $product->get_price();
			}
		}

		$inline = array(
			'_geny_simple_product_price' => $price,
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
			$sess_data = stripslashes_deep($_POST[self::FIELD_NAME]);
			WC()->session->set(self::SESS_ADDITIONAL_DATA, $sess_data);
		} else {
			WC()->session->set(self::SESS_ADDITIONAL_DATA, array());
		}

		return $cart_item;
	}


	public function get_user_custom_data_session($item, $values, $key)
	{

		$sess_data = WC()->session->get(self::SESS_ADDITIONAL_DATA);
		if (!empty($sess_data)) {
			$item['custom_data'] = $sess_data;
		}
		return $item;
	}

	public function update_products($cart)
	{
		dd($cart);
		exit;
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
			WC()->session->set(self::SESS_ADDITIONAL_DATA, array());
			return;
		}

		error_log('awaitest');
		error_log(print_r($sess_data, true));

		if (is_array($sess_data)) {
			foreach ($sess_data as $key => $json_str) {
				$data_array = json_decode($json_str, true);
				$product_ids = $data_array['product_ids'];

				if (!empty($product_ids)) {
					foreach ($product_ids as $id) {
						if (!empty($id)) {
							$this->add_product_to_cart($id);
						}
					}
				}
			}
		}

		// Apply additional proudct discount.
		$this->add_additional_product_discount($cart, $sess_data);

		// empty the session variable.
		//WC()->session->set(self::SESS_ADDITIONAL_DATA, array());
	}


	public function add_product_to_cart($id)
	{
		$helper = Helper::get_instance();
		$product = $helper->_is_variable_product($id);

		if ($product !== false) {
			error_log('adding variable');
			WC()->cart->add_to_cart($product->get_parent_id(), 1, $id);
		} else {
			error_log('adding simple');
			WC()->cart->add_to_cart($id);
		}
	}

	public function add_additional_product_discount($cart, $sess_data)
	{

		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}

		error_log('dimitri');

		//$discounts = wp_list_pluck($data_array, 'discount');
		$minus_the_fee = 0;

		if (empty($sess_data)) {
			return;
		}

		error_log('dimitri2');

		if (is_array($sess_data)) {
			foreach ($sess_data as $key => $json_str) {
				$data_array = json_decode($json_str, true);
				$discount = $data_array['discount'];

				if (!empty($discount)) {
					$minus_the_fee = $minus_the_fee + $discount;
				}
			}
		}

		// Makit it in minus to subtract fee from total.
		$minus_the_fee = absint($minus_the_fee);
		$minus_the_fee = $minus_the_fee * -1;

		if ($minus_the_fee !== 0) {
			$cart->add_fee(__('Discounts applied', 'geny-woocommerce'), $minus_the_fee, false);
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
