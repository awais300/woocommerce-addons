<?php

defined('ABSPATH') || exit;

/**
 * Class TemplateLoader
 */

class Helper extends Singleton
{
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

        if ($product instanceof \WC_Product_Variable) {
            return $product;
        } else {
            return false;
        }
    }

    public function is_odd($number)
    {
        error_log('number to check for odd is: '. $number);
        if (empty($number) || !is_numeric($number)) {
            throw new \Exception('Invalid number');
        }

        if ($number % 2 == 0) {
            error_log('number is even');
            return false; // Even.
        } else {
            error_log('number is odd');
            return true; // Odd.
        }
    }
}
