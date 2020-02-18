<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/bianqui149
 * @since      1.0.0
 *
 * @package    Aws_Integration
 * @subpackage Aws_Integration/admin/partials
 */
?>
<div class="wrap">
	<h1>AWS SETUP</h1>
	<form method="post" action="options.php">
		<?php settings_fields('aws_credentials'); ?>
		<?php do_settings_sections('aws_credentials'); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Key</th>
				<td><input type="password" name="aws_key_credential" value="<?php echo esc_attr(get_option('aws_key_credential')); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Secret</th>
				<td><input type="password" name="aws_password_credential" value="<?php echo esc_attr(get_option('aws_password_credential')); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Bucket</th>
				<td><input type="text" name="aws_bucket_credential" value="<?php echo esc_attr(get_option('aws_bucket_credential')); ?>" /></td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->