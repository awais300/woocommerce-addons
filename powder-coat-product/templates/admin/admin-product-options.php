<?php
if (isset($_POST['geny-update']) && $_POST['geny-update'] === 'geny-update') : ?>
	<div class="notice notice-info is-dismissible">
		<p>Options updated.</p>
	</div>
<?php endif; ?>

<h2 class=""><span>Global Options for Products</span></h2>

<div class="inside">
	<div class="main">

		<strong>Custom Color Default Text</strong>
		<form name="form1" method="post" action="">
			<p><?php
				$content = get_option('geny_custom_default_text');
				$args = array(
					'media_buttons' => false,
					'textarea_name' => 'geny_custom_default_text', // Set custom name.
					'textarea_rows' => get_option('default_post_edit_rows', 10),
					'quicktags' => true,
				);
				wp_editor(stripslashes($content), 'genyeditorcustomcolor', $args); ?></p>
			<button class="button button-primary" type="submit" name="geny-update" value="geny-update">Update Options</button>
		</form>
	</div>
</div>