<?php

class Checkout
{
	private const AGREE_ORDER_FINAL = 'agree_order_final';

	public function __construct()
	{

		add_action('wfacp_checkout_after_order_review', array($this, 'add_checkbox_field'));
		add_action('woocommerce_checkout_process', array($this, 'validate_checkbox_field'));
		add_action('woocommerce_checkout_update_order_meta', array($this, 'save_checkbox_filed_order_meta'));
	}

	public function add_checkbox_field()
	{
		if ($this->is_dealer()) {
			return;
		}

		$checked = (isset($_POST[self::AGREE_ORDER_FINAL])) ? $_POST[self::AGREE_ORDER_FINAL] : 'no';

		woocommerce_form_field(self::AGREE_ORDER_FINAL, array(
			'type' => 'checkbox',
			'class' => array('input-checkbox'),
			'label' => __('I acknowledge that my order is final and would like to complete my purchase. I understand that I will not be able to make changes to my order once it is placed.'),
			'required' => true,
		), $checked);
	}

	public function validate_checkbox_field()
	{
		if ($this->is_dealer()) {
			return;
		}

		if (!isset($_POST[self::AGREE_ORDER_FINAL])) {
			wc_add_notice('Please acknowledge that your order is final.', 'error');
		}
	}

	public function save_checkbox_filed_order_meta($order_id)
	{
		if (isset($_POST[self::AGREE_ORDER_FINAL])) {
			update_post_meta($order_id, self::AGREE_ORDER_FINAL, esc_attr($_POST[self::AGREE_ORDER_FINAL]));
		}
	}

	public function is_dealer()
	{
		$dealer_roles = array(
			'dealer', // 25% Dealer.
			'dealer_30', // 30% Dealer.
			'master_dealer', // 35% Master Dealer.
			'distributor_dealer', // DistributerDealer.
			'iron_bear', // 42.5% Dealer.
		);

		if (get_current_user_id() == 0) {
			return false;
		}

		$user = wp_get_current_user();
		$roles = (array) $user->roles;

		$found = false;
		foreach ($roles as $key => $role) {
			$found = array_search($role, $dealer_roles);
			if ($found !== false) {
				return true;
			}
		}

		return $found;
	}
}
new Checkout();
