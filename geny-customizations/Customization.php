<?php

namespace Customization;

/**
 * This addon should be use for samll customizations and fixes for the site.
 */

class Customization
{
	public function __construct()
	{
		add_action('wp_footer', array($this, 'action_wp_footer'));
		add_action('woocommerce_review_order_before_shipping', array($this, 'custom_content_before_shipping'));
	}

	public function action_wp_footer()
	{
		if (!is_checkout()) {
			return;
		}
?>

		<script>
			jQuery(document).ready(function($) {
				$(document.body).on(
					"updated_checkout",
					function(e) {
						var text = $('.alg_wc_left_to_free_shipping_ajax').text();
						var notice = $('div.wfacp-message.wfacp-notice');
						if (is_free_deliver_text_found(text)) {
							notice_text = notice.text();
							if (notice_text.search('left for free shipping') >= 0) {
								notice.hide();
							}
						} else {
							notice.show();
						}
					}
				)

				function is_free_deliver_text_found(text) {
					if (!text) {
						return false;
					}

					if (text.search('You have free delivery') >= 0) {
						// the string matches.
						return true;
					} else {
						return false;
					}
				}
			});
		</script>
	<?php
	}


	public function custom_content_before_shipping()
	{ ?>
		<style>
			.custom-content p {
				color: #737373;
				padding: 0;
				margin-top: 0;
				line-height: 0px;
				margin-bottom: 20px;
				font-size: 13px;
				color: red;
			}
		</style>

		<div class="custom-content">
			<p>Time stated is shipping transit time, not timeline when item will be received.</p>
		</div>

<?php
	}
}

new Customization();
