<?php

namespace autoAddProducts\Admin;

use \Singleton;

class ACF extends Singleton
{

	/* ACF and form field name */
	public const FIELD_NAME = 'products';
	public const SEARCH_KEY = 'geny_custom_search';


	public function __construct()
	{
		add_filter('acf/fields/relationship/query/name=' . self::FIELD_NAME, array($this, 'product_option_relationship_query'), 10, 3);
		add_filter('get_meta_sql', array($this, 'alter_meta_query_to_allow_skus'), 10, 6);
		add_filter('acf/fields/relationship/result/name=' . self::FIELD_NAME, array($this, 'add_text_to_product_names'), 10, 4);
	}

	/**
	 * Update query to search proudcts by SKUs as well.
	 * 
	 */
	function product_option_relationship_query($args, $field, $post)
	{
		if (!is_admin()) {
			return $args;
		}

		$search_term = $args['s'];
		$search_term = esc_sql($search_term);

		$args[self::SEARCH_KEY] = true;
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => '_sku',
				'value'   => $search_term,
				'compare' => 'LIKE',
			),
		);

		return $args;
	}



	/**
	 * Adding OR clause so that SKU can be fetched. (WP default was adding AND).
	 * Also setting post_stauts to publish before meta query, Adding after the meta query
	 * would bring some posts that are not publish.
	 * 
	 */
	function alter_meta_query_to_allow_skus($sql, $queries, $type, $primary_table, $primary_id_column, $wp_query)
	{

		if (!is_admin()) {
			return $sql;
		}

		if (isset($wp_query->query[self::SEARCH_KEY]) && $wp_query->is_search) {
			global $wpdb;
			$posts_table = $wpdb->prefix . 'posts';

			$where = $sql['where'];
			$where = ltrim($where);
			$where = ltrim($where, 'AND');
			$where = " OR" . $where;

			// Below line can be removed if we wish not to set the post_status.
			$where = "  AND (({$posts_table}.post_status = 'publish'))" . $where;

			$sql['where'] = $where;
		}

		return $sql;
	}


	/**
	 *  Update the display text of product name. 
	 * Contact SKU with proudct name.
	 *  
	 **/
	function add_text_to_product_names($text, $post, $field, $post_id)
	{
		if (!is_admin()) {
			return $text;
		}

		$sku_text = '';
		$sku = $post->_sku;

		if (!empty($sku)) {
			$sku_text = $sku;
			$sku_text = $sku_text . ' - ';
		}

		if ($this->_is_variable_product($post) !== false) {
			$variation_text = $this->_get_formatted_variation_name($post);
			return $sku_text . $variation_text;
		}

		return $sku_text . $text;
	}

	/**
	 * Get variable product if its a variable product.
	 * 
	 * @param  int|WP_Post $post
	 * @return WC_Product_Variation|false
	 */
	public function _is_variable_product($post)
	{
		$post_id = $post;
		if (is_object($post)) {
			$post_id = $post->ID;
		}

		$product = wc_get_product($post_id);
		if ($product->is_type('variation')) {
			return $product;
		} else {
			return false;
		}
	}

	public function _get_formatted_variation_name($post)
	{
		$post_id = $post;
		if (is_object($post)) {
			$post_id = $post->ID;
		}

		$product = wc_get_product($post_id);
		$formatted_name = '';
		if ($product->is_type('variation')) {
			$variation_names = array();
			$attributes = $product->get_attributes();
			if ($attributes) {
				foreach ($attributes as $key => $value) {
					//$variation_key =  end(explode('-', $key));
					$variation_names[] = ucfirst($value);
				}
			}

			$formatted_name = implode(' ', $variation_names);
		}

		return trim($formatted_name);
	}
}

new ACF();
