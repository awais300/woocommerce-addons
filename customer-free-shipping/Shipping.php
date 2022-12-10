<?php

namespace FreeShipping;

class Shipping
{
	public const ACF_USER_SHIPPING_ALLOWED_FIELD = 'user_free_shipping_allowed';

	public function __construct()
	{
		add_filter('init', array($this, 'load_acf_fields'), 10);
		add_filter('woocommerce_cart_ready_to_calc_shipping', array($this, 'disable_shipping_calc_on_cart'), 99);
		add_action('wp_head', array($this, 'add_css'));
	}

	public function add_css()
	{
		if ($this->is_free_shipping_allowed_for_user() === true) {


?>
			<style>
				#wc_checkout_add_ons,
				.form_section_two_step_0_layout_9 h2,
				div.elementor-element-4b5ff940,
				div.wfacp-message.wfacp-notice {
					display: none !important;
				}
			</style>
<?php
		}
	}

	public function is_free_shipping_allowed_for_user()
	{
		$user_id = get_current_user_id();
		$allowed = get_field(self::ACF_USER_SHIPPING_ALLOWED_FIELD, 'user_' . $user_id);
		if ($allowed) {
			return true;
		} else {
			return false;
		}
	}

	public function disable_shipping_calc_on_cart($show_shipping)
	{
		error_log('Defualt shipping: ' . $show_shipping);
		if (is_checkout() && $this->is_free_shipping_allowed_for_user() === true) {
			error_log('User got free shipping: User ID: ' . get_current_user_id());
			return false;
		}

		return $show_shipping;
	}


	public function load_acf_fields()
	{
		if (function_exists('acf_add_local_field_group')) {

			acf_add_local_field_group(array(
				'key' => 'group_638e32504e3a2',
				'title' => 'Free Shipping Settings',
				'fields' => array(
					array(
						'key' => 'field_638e3251945df',
						'label' => 'Free Shipping Allowed?',
						'name' => 'user_free_shipping_allowed',
						'type' => 'true_false',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => 'If checked this user will qualify for free shipping e.g. No shipping option will be shown on checkout page. No matter what user role is.',
						'default_value' => 0,
						'ui' => 0,
						'ui_on_text' => '',
						'ui_off_text' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'user_form',
							'operator' => '==',
							'value' => 'edit',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 0,
			));
		}
	}
}

new Shipping();
