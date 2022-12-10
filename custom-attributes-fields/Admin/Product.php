<?php

namespace customAttributeFields\Admin;

class Product
{
	public function __construct()
	{ 
		add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'add_content_after_dropdown'), 10, 2);
		add_action('woocommerce_product_options_attributes', array($this, 'add_fields_under_attribute_tab'));
		add_action('woocommerce_process_product_meta', array($this, 'save_attribute_data'));
	}

	/**
	 * Frontend display on single product page.
	 * @param string $html
	 * @param array $args
	 */
	public function add_content_after_dropdown($html, $args)
	{
		$post_id = get_the_ID();
		$product = get_post($post_id);
		$meta_key = $args['attribute'] . '_' . $post_id;
		$text = get_post_meta($post_id, $meta_key, true);

		return $html . $text;
	}

	public function add_fields_under_attribute_tab()
	{

		$post_id = 0;
		if (isset($_GET['post']) && !empty($_GET['post'])) {
			$post_id = $_GET['post'];
		}

		if (empty($post_id)) {
			return;
		}


		$attrib_labels = $this->get_product_attribute_data($post_id);


		if ($attrib_labels) {
			foreach ($attrib_labels as $label) {
				woocommerce_wp_textarea_input([
					'id' => $label['custom_attr_name'],
					'label' => $label['name'] . ' Description',
				]);
			}
		}
	}


	public function save_attribute_data($post_id)
	{
		$product = wc_get_product($post_id);
		$attrib_fields = $this->get_product_attribute_data($post_id);

		if ($attrib_fields) {
			foreach ($attrib_fields as $field) {
				$field_value = isset($_POST[$field['custom_attr_name']]) ? $_POST[$field['custom_attr_name']] : '';
				$product->update_meta_data($field['custom_attr_name'], sanitize_textarea_field($field_value));
				$product->save();
			}
		}
	}


	public function get_product_attribute_data($post_id)
	{
		$post_id = absint($post_id);
		$product = wc_get_product($post_id);
		$attrs = $product->get_attributes();

		if (empty($attrs)) {
			return false;
		}

		$attrib_data = array();
		$i = 0;

		foreach ($attrs as $attr_obj) {
			$attrib_data[$i]['id'] = wc_attribute_label($attr_obj->get_id());
			$attrib_data[$i]['name'] = wc_attribute_label($attr_obj->get_name());
			$attrib_data[$i]['raw_name'] = $attr_obj->get_name();
			$attrib_data[$i]['custom_attr_name'] = $attr_obj->get_name() . '_' . $post_id;
			$i++;
		}

		return $attrib_data;
	}
}

new Product();
