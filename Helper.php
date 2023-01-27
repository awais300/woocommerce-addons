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

    /**
     * Check if number is odd.
     * @param  int $number
     * @return boolean
     */
    public function is_odd($number)
    {
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

    /**
     * Check if current user belong to dealer role.
     * @return boolean
     */
    public function is_dealer() {
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
