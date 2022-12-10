<?php

namespace BOGO;

use \TemplateLoader;
use \Helper;


class BogoPopup
{

	public const VERSION = 1.1;

	public function __construct()
	{
		$this->loader = TemplateLoader::get_instance();
		add_action('wp_head', array($this, 'enqueue_scripts'));
		add_action('woocommerce_after_checkout_form', array($this, 'load_popup'));
	}

	public function enqueue_scripts()
	{
		if (!is_checkout()) {
			return;
		}

		$url = get_stylesheet_directory_uri()  . '/woocommerce-addons/bogo-popup/assets/css/w3.css';
		echo "<link rel='stylesheet' id='bogo-css'  href='" . $url . "' media='all' />";
	}

	public function check_bogo_tag_in_cart()
	{
		// Set $tag_in_cart to false.
		$tag_in_cart = false;
		$found_count = 0;

		// Loop through all products in the cart.
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

			// If Cart has category/tag "bogo", set $tag_in_cart to true
			if (has_term('bogo', 'product_tag', $cart_item['product_id'])) {
				$tag_in_cart = true;
				$found_count = $found_count + 1;
				//break;
			}
		}

		$helper = Helper::get_instance();

		// BOGO product found in cart.
		if ($tag_in_cart && $helper->is_odd($found_count) === true) {
			return $tag_in_cart;
		}

		return false;
	}


	public function load_popup()
	{
		/*WC()->cart->empty_cart();
		exit;*/

		// thank you page.
		if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
			return;
		}

		if (!is_checkout()) {
			return;
		}

		if (!$this->check_bogo_tag_in_cart()) {
			return;
		}

		$data = array();
		$this->loader->get_template(
			'bogo-popup.php',
			$data,
			__DIR__ . '/templates/',
			true
		);
	}
}


new BogoPopup();
