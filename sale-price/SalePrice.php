<?php

use Helper;

class SalePrice
{

	public function __construct()
	{
		add_filter('woocommerce_product_get_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_product_variation_get_price', array($this, 'hanlde_custom_price'), 10, 2);

		add_filter('woocommerce_product_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_product_variation_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);

		add_filter('woocommerce_variation_prices_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_variation_prices_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
	}



	public function hanlde_custom_price($price, $product)
	{
		if (is_admin()) {
			return $price;
		}

		$is_dealer = (Helper::get_instance())->is_dealer();
		if ($is_dealer) {
			// Returning regular price will block the sale price to display. 
			// In this case we return wholesale price set by wholeSlae price plugin for dealers.
			$wholesale_price = $this->geny_get_wholesale_price($product->get_id());
			if (!empty($wholesale_price)) {
				return $wholesale_price;
			} else {
				return $product->get_regular_price();
			}
		}

		return $price;
	}

	public function geny_get_wholesale_price($product_id)
	{
		$wwp = \WWP_Wholesale_Roles::getInstance();
		$roles = $wwp->getUserWholesaleRole();

		// To avoid infinite loop.
		remove_filter('woocommerce_product_get_price', array($this, 'hanlde_custom_price'), 10, 2);
		remove_filter('woocommerce_product_variation_get_price', array($this, 'hanlde_custom_price'), 10, 2);
		remove_filter('woocommerce_product_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
		remove_filter('woocommerce_product_variation_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
		remove_filter('woocommerce_variation_prices_price', array($this, 'hanlde_custom_price'), 10, 2);
		remove_filter('woocommerce_variation_prices_sale_price', array($this, 'hanlde_custom_price'), 10, 2);


		$price = \WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3($product_id, $roles);

		/*add_filter('woocommerce_product_get_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_product_variation_get_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_product_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_product_variation_get_sale_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_variation_prices_price', array($this, 'hanlde_custom_price'), 10, 2);
		add_filter('woocommerce_variation_prices_sale_price', array($this, 'hanlde_custom_price'), 10, 2);*/

		return $price['wholesale_price'];
	}
}

new SalePrice();
