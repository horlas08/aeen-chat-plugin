<?php
/**
 * Admin settings page HTML for ArriveWhats WA notife.
 *
 * @package arrivebotCampany
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap" id="ware-wrap">
	<div class="checkoutotp wp-tab-panels">
		<div class="notification-form english hint otp">
			<div class="hint-box">
				<label for="ware_notifications" class="hint-title">
					<?php esc_html_e( 'Checkout OTP Verification', 'ware' ); ?>
				</label>
				<p class="hint-desc">
					<?php esc_html_e( 'Verifies transactions with a one-time checkout code sent via WhatsApp.', 'ware' ); ?>
					<p><?php _e( 'Checkout shortcode', 'ware' ); ?> <code>[woocommerce_checkout]</code></p>
				</p>
			</div>
		</div>
		<div class="otp-card">
			<form action="options.php" method="post">
				<?php
				settings_fields( 'ware_options_group' );
				do_settings_sections( 'ware-settings' );
				submit_button( esc_html__( 'Save Settings', 'ware' ) );
				?>
			</form>
		</div>
	</div>
</div>

		<?php

