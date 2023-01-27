<?php

namespace ClearCart;

use \Helper;

class Checkout
{

	public function __construct()
	{
		add_action('wp_head', array($this, 'woocommerce_clear_cart_url'));
		add_action('wp_head', array($this, 'add_css'));
		add_action('wfacp_before_sidebar_content_section', array($this, 'filter_wfacp_form_section'), 11);
		add_filter('wfacp_before_order_summary_html', array($this, 'filter_fields'), 11, 1);
	}

	public function add_css()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}

		if (is_checkout()) {
?>
			<style>
				.clear-cart {
					display: flex;
				}

				div button.clear-cart-button {
					padding: 4px 13px !important;
				}
			</style>
		<?php
		}
	}

	public function filter_fields($field)
	{
		$field['label'] = '';
		return $field;
	}

	public function woocommerce_clear_cart_url()
	{
		if (isset($_GET['clear-cart'])) {
			WC()->cart->empty_cart(true);
			/*wp_redirect(wc_get_checkout_url());
			exit;*/
		}
	}


	public function filter_wfacp_form_section()
	{
		$show_cart_clear_button = true;
		if (WC()->cart->get_cart_contents_count() == 0) {
			$show_cart_clear_button = false;
		}

		$helper = Helper::get_instance();
		if ($helper->is_dealer() == false) {
			$show_cart_clear_button = false;
		}

		?>
		<div class="clear-cart woocommerce wfacp_order_sec wfacp_order_summary_layout_9">
			<h2 class="wfacp-list-title wfacp_section_title wfacp-text-left wfacp-normal">Your Cart </h2>
			<?php if ($show_cart_clear_button == true) : ?>
				<a onclick="return confirm('Are you sure you want to clear your cart?');" href="<?php echo wc_get_checkout_url(); ?>?clear-cart">
					<button class="clear-cart-button button button-primary wfacp_next_page_button cont-shop" type="button">Clear Cart</button>
				</a>
			<?php endif; ?>
		</div>

<?php
	}
}


new Checkout();
