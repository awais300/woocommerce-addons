<?php
namespace LocalPickup;

class Checkout {
	Private const VERSION = 1.1;
	private const DISTANCE_RANGE = 50.00;
	private const GOOGLE_API_KEY = 'AIzaSyBbIS3jSmSJRQmqwP456YvsqVuVfm30Ik0';
	private const STORE_POSTCODE = 46550;

	public function __construct() {
		//add_filter('woocommerce_cart_shipping_packages', array($this, 'woocommerce_shipping_rate_cache_invalidation'), 10);
		add_filter('woocommerce_package_rates', array($this, 'enable_local_pickup_shipping'), 10, 2);
	}

	/*Invalidate cache so that hook "woocommerce_package_rates" can trigger every time*/
	public function woocommerce_shipping_rate_cache_invalidation($packages) {
		foreach ($packages as &$package) {
			$package['rate_cache'] = self::VERSION;
		}
		return $packages;
	}

	public function enable_local_pickup_shipping($rates, $package) {
		/*if ($this->is_dealer() == false) {
			return $rates;
		}*/

		$allow_local_pickup = false;
		$distance = $this->calculate_distance($package);

		if ($distance === false) {
			$allow_local_pickup = false;
		}

		if ($distance <= self::DISTANCE_RANGE && $this->is_dealer() ===  true) {
			$allow_local_pickup = true;
		}

		if ($allow_local_pickup === false) {
			unset($rates['local_pickup:24']);
		}

		return $rates;
	}

	public function calculate_distance($package) {
		$store_postcode = get_option('woocommerce_store_postcode');
		if (empty($store_postcode)) {
			$store_postcode = self::STORE_POSTCODE;
		}

		$dest_zipcode = WC()->customer->get_shipping_postcode();
		$country_code = WC()->customer->get_shipping_country();

		// Only for USA
		if (strtolower($country_code) != 'us') {
			return false;
		}

		$google_api_key = self::GOOGLE_API_KEY;
		$distance_data = wp_remote_get("https://maps.googleapis.com/maps/api/distancematrix/json?&units=imperial&origins={$store_postcode}&destinations={$dest_zipcode}&key={$google_api_key}");

		if (is_wp_error($distance_data)) {
			return false;
		}

		$json = wp_remote_retrieve_body($distance_data);
		$data = json_decode($json, true);

		if($data['status'] != 'OK') {
			return false;
		}

		$distance_data = $data['rows'][0]['elements'][0];
		if ($distance_data['status'] != 'OK') {
			return false;
		} else {
			$distance = $distance_data['distance']['value'] / 1609; //Converting meters to miles
			$distance = number_format((float) $distance, 2, '.', '');
			error_log('Distance: ' . $distance . ' Zipcode: ' . $dest_zipcode);
			return $distance;
		}
	}

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
new Checkout();

