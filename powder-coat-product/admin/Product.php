<?php

class Product
{
	public const ADMIN_IS_PAINT_PROD = '_geny_is_paint_product';

	public function __construct()
	{
		$this->loader = TemplateLoader::get_instance();
		//add_action('woocommerce_product_options_general_product_data', array($this, 'woocommerce_product_custom_fields'));
		//add_action('woocommerce_admin_process_product_object', array($this, 'product_custom_fields_save'));

		add_action('admin_menu', array($this, 'register_product_options_page'));
		add_action('admin_init', array($this, 'save_product_options_page_data'));
	}

	public function woocommerce_product_custom_fields()
	{
		echo '<div class="product_custom_field">';
		woocommerce_wp_checkbox(array(
			'id' => self::ADMIN_IS_PAINT_PROD,
			'description' => __('Check this option to let user enter custom paint code for this product', 'geny-woocommerce'),
			'label' => __('Allow this product to enter custom paint code?', 'geny-woocommerce'),
			'desc_tip' => 'true',
		));

		echo '</div>';
	}

	public function product_custom_fields_save($product)
	{
		$woocommerce_checkbox = isset($_POST[self::ADMIN_IS_PAINT_PROD]) ? 'yes' : 'no';
		$product->update_meta_data(self::ADMIN_IS_PAINT_PROD, $woocommerce_checkbox);
	}

	public function register_product_options_page()
	{
		add_submenu_page(
			'edit.php?post_type=product',
			'Product Options',
			'Product Options',
			'manage_woocommerce',
			'geny-product-options',
			array($this, 'product_options_callback')
		);
	}

	public function product_options_callback()
	{
		$data = array();
		$this->loader->get_template(
			'admin-product-options.php',
			$data,
			dirname(__DIR__) . '/templates/admin/',
			true
		);
	}

	public function save_product_options_page_data()
	{
		if (current_user_can('manage_options')) {
			if (isset($_POST['geny-update']) && $_POST['geny-update'] === 'geny-update') {
				update_option('geny_custom_default_text', stripslashes(trim($_POST['geny_custom_default_text'])));
			}
		}
	}
}

new Product();
