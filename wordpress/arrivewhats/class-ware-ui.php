<?php
/**
 * ArriveWhats WA notife.
 *
 * @package arrivebotCampany.
 */


class ware_UI {
    public $notif;    
    public $instances; 

	public function __construct() {
		$this->notif     = get_option( 'ware_notifications' );
		$this->instances = get_option( 'ware_instances' );
	}

	public function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}

	public function admin_page() {
		?>
		<div class="wrap" id="ware-wrap">
			<div class="form-wrapper">
				<div class="ware-tab-wrapper">
					<ul class="nav-tab-wrapper woo-nav-tab-wrapper">
						<li name="ware_notifications[notification-message]" class="nav-tab nav-tab-active"><a href="#notification"><?php esc_html_e( 'Customer notifications', 'ware' ); ?></a></li>
					
						<li name="ware_admin_notifications[admin_notification-message]" class="nav-tab"><a href="#admin-notification"><?php esc_html_e( 'Admin notifications', 'ware' ); ?></a></li>
																
						
						<li class="nav-tab"><a href="#followup"><?php esc_html_e( 'Follow up', 'ware' ); ?></a></li>
						
						<li class="nav-tab"><a href="#abandoned-cart"><?php esc_html_e( 'Abandoned cart', 'ware' ); ?></a></li>

						<li class="nav-tab"><a href="#help"><?php esc_html_e( 'Quick message', 'ware' ); ?></a></li>
						
					</ul>
					<form method="post" action="options.php">
						<div class="wp-tab-panels" id="notification">
								<?php
									$this->notification_settings();
								?>
						</div>
						<div class="wp-tab-panels" id="admin-notification" style="display: none;">
								<?php
									$this->admin_notification_settings();
								?>
						</div>
						<div class="wp-tab-panels" id="followup" style="display: none;">
								<?php
									$this->followup_settings();
								?>
						</div>
						<div class="wp-tab-panels" id="abandoned-cart" style="display: none;">
								<?php
									$this->abandoned_cart_settings();
								?>
						</div>
						<div class="wp-tab-panels" id="other" style="display: none;">
								<?php
									$this->other_settings();
								?>
						</div>
					<div class="wp-tab-panels" id="help" style="display: none;">
					</form>
			<?php
			$this->help_info();
			?>
					</div>
				</div>                
				<div class="info" style="display: none;">
			<?php
			$this->setup_info();
			?>
											</div>
			</div>
		</div>
		<?php
	}

	public function notification_settings() {
		if ( $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$status_list      = wc_get_order_statuses();
			$status_list_temp = array();
			$original_status  = array(
				'pending',
				'failed',
				'on-hold',
				'processing',
				'completed',
				'refunded',
				'cancelled',
			);
			foreach ( $status_list as $key => $status ) {
				$status_name = str_replace( 'wc-', '', $key );
				if ( ! in_array( $status_name, $original_status ) ) {
					$status_list_temp[ $status ] = $status_name;
				}
			}
			$status_list = $status_list_temp;
		}
		?>
		
		<?php settings_fields( 'ware_storage_notifications' ); ?>

			<div class="info-banner">
				<p class="banner-text"><?php esc_html_e( 'Send real-time WhatsApp messages to your customers based on order status changes or new order creations.', 'ware' ); ?></p>
				<input type="submit" class="button-primarywa saveit top" value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
			</div>
			<hr class="line">

			
			
				<div class="notification-form" style="display: none;">
				<div class="heading-bar">
				<label for="ware_notifications[default_country]" class="notification-title"><?php esc_html_e( 'Default Country Code:', 'ware' ); ?></label>
				</div>
				<p class="deactive-hint"><em><?php echo esc_html__( 'Add your country code without any 00 or + ex: 2 for EG or 966 for SA  ', 'ware' ); ?></em></p>
				<br>
				<div class="notification">
						<div class="phone-field">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="phone-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm7.931 9h-2.764a14.67 14.67 0 0 0-1.792-6.243A8.013 8.013 0 0 1 19.931 11zM12.53 4.027c1.035 1.364 2.427 3.78 2.627 6.973H9.03c.139-2.596.994-5.028 2.451-6.974.172-.01.344-.026.519-.026.179 0 .354.016.53.027zm-3.842.7C7.704 6.618 7.136 8.762 7.03 11H4.069a8.013 8.013 0 0 1 4.619-6.273zM4.069 13h2.974c.136 2.379.665 4.478 1.556 6.23A8.01 8.01 0 0 1 4.069 13zm7.381 6.973C10.049 18.275 9.222 15.896 9.041 13h6.113c-.208 2.773-1.117 5.196-2.603 6.972-.182.012-.364.028-.551.028-.186 0-.367-.016-.55-.027zm4.011-.772c.955-1.794 1.538-3.901 1.691-6.201h2.778a8.005 8.005 0 0 1-4.469 6.201z"></path></svg>
							<input type="text" name="ware_notifications[default_country]" placeholder="<?php echo esc_attr__( 'Your country code', 'ware' ); ?>" class="admin_number regular-text admin_number upload-text" value="<?php echo esc_attr( isset( $this->notif['default_country'] ) ? $this->notif['default_country'] : '' ); ?>">
						</div>
				</div>
				<p class="deactive-hint"><em><?php echo esc_html__( 'Insert country code only if your customer is from a single country. This will remove the country detection library on the old checkout page. Leave blank if your customer is from many countries.', 'ware' ); ?></em></p>
			</div>



    <!-- Add tabs for Arabic and English editors -->
    <div class="editor-tabs">
    	<h3 class="editor-title"><?php _e( 'Customer language:', 'ware' ); ?></h3>
    	<div class="editor-tab" data-lang="english"><?php _e( 'Default', 'ware' ); ?></div>
    	<div class="editor-tab" data-lang="arabic"><?php _e( 'Arabic', 'ware' ); ?></div>
    </div>
    
    <div class="editor-content layout-en" data-lang="english">
    		<?php
    		$blank           = __( 'leave blank to deactivate', 'ware' );
    		$txt_placeholder = __( 'Write your message...', 'ware' );
    		$img_format      = __( 'Accepts .png, .jpg, .jpeg', 'ware' );
    		$upload_btn      = __( 'Upload image', 'ware' );
    		?>
    
    	
    	<div class="notification-form english hint">
    		<div class="hint-box">
    			<label for="ware_notifications" class="hint-title"><?php esc_html_e( 'Order status notifications', 'ware' ); ?></label>
    			 
    			<p class="hint-desc"><?php esc_html_e( 'Automatically send notification messages based on the primary language of the user’s WordPress account, including English, French, Italian, German, Spanish, Hindi, or any LTR language.', 'ware' ); ?></p>
    		</div>
    	</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[registration_message]" class="notification-title">
            <?php esc_html_e('User Registration', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when a user registers on WordPress.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 New</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[registration_message_active]" value="1" <?php checked( isset( $this->notif['registration_message_active'] ) && $this->notif['registration_message_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-registration-english">
        <div class="form">
             <br>
            <hr class="line">
            <div class="placeholder-containerlogin"></div>
            <textarea id="ware_notifications[registration_message]" name="ware_notifications[registration_message]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['registration_message']) ? esc_textarea($this->notif['registration_message']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[registration_message_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text registration_message_img upload-text" value="<?php echo esc_attr(isset($this->notif['registration_message_img']) ? $this->notif['registration_message_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="registration_message_img">
            </div>
           
        </div>
    </div>
</div>
    	
    	<div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[login_message]" class="notification-title">
            <?php esc_html_e('User Login', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when a user logs in to WordPress.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 New</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[login_message_active]" value="1" <?php checked( isset( $this->notif['login_message_active'] ) && $this->notif['login_message_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-login">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-containerlogin"></div>
            <textarea id="ware_notifications[login_message]" name="ware_notifications[login_message]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['login_message']) ? esc_textarea($this->notif['login_message']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[login_message_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text login_message_img upload-text" value="<?php echo esc_attr(isset($this->notif['login_message_img']) ? $this->notif['login_message_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="login_message_img">
            </div>
        </div>
    </div>
</div>

    	<div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_onhold]" class="notification-title">
            <?php esc_html_e('Order On-Hold', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is placed on hold.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_onhold_active]" value="1" <?php checked( isset( $this->notif['order_onhold_active'] ) && $this->notif['order_onhold_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-onhold-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_onhold]" name="ware_notifications[order_onhold]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_onhold']) ? esc_textarea($this->notif['order_onhold']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_onhold_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_onhold_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_onhold_img']) ? $this->notif['order_onhold_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_onhold_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_pending]" class="notification-title">
            <?php esc_html_e('Order Pending Payment', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is pending payment.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_pending_active]" value="1" <?php checked( isset( $this->notif['order_pending_active'] ) && $this->notif['order_pending_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-pending-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_pending]" name="ware_notifications[order_pending]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_pending']) ? esc_textarea($this->notif['order_pending']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_pending_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_pending_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_pending_img']) ? $this->notif['order_pending_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_pending_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_processing]" class="notification-title">
            <?php esc_html_e('Order Processing', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is being processed.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_processing_active]" value="1" <?php checked( isset( $this->notif['order_processing_active'] ) && $this->notif['order_processing_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-processing-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_processing]" name="ware_notifications[order_processing]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_processing']) ? esc_textarea($this->notif['order_processing']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_processing_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_processing_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_processing_img']) ? $this->notif['order_processing_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_processing_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_completed]" class="notification-title">
            <?php esc_html_e('Order Completed', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is completed.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_completed_active]" value="1" <?php checked( isset( $this->notif['order_completed_active'] ) && $this->notif['order_completed_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-completed-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_completed]" name="ware_notifications[order_completed]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_completed']) ? esc_textarea($this->notif['order_completed']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_completed_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_completed_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_completed_img']) ? $this->notif['order_completed_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_completed_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_failed]" class="notification-title">
            <?php esc_html_e('Order Failed', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order fails.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_failed_active]" value="1" <?php checked( isset( $this->notif['order_failed_active'] ) && $this->notif['order_failed_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-failed-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_failed]" name="ware_notifications[order_failed]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_failed']) ? esc_textarea($this->notif['order_failed']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_failed_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_failed_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_failed_img']) ? $this->notif['order_failed_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_failed_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_refunded]" class="notification-title">
            <?php esc_html_e('Order Refunded', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is refunded.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_refunded_active]" value="1" <?php checked( isset( $this->notif['order_refunded_active'] ) && $this->notif['order_refunded_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-refunded-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_refunded]" name="ware_notifications[order_refunded]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_refunded']) ? esc_textarea($this->notif['order_refunded']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_refunded_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_refunded_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_refunded_img']) ? $this->notif['order_refunded_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_refunded_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_cancelled]" class="notification-title">
            <?php esc_html_e('Order Cancelled', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order is cancelled.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_cancelled_active]" value="1" <?php checked( isset( $this->notif['order_cancelled_active'] ) && $this->notif['order_cancelled_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-cancelled-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_cancelled]" name="ware_notifications[order_cancelled]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_cancelled']) ? esc_textarea($this->notif['order_cancelled']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_cancelled_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_cancelled_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_cancelled_img']) ? $this->notif['order_cancelled_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_cancelled_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form english">
    <div class="heading-bar">
        <label for="ware_notifications[order_note]" class="notification-title">
            <?php esc_html_e('Order Note', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent when an order note is added.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_note_active]" value="1" <?php checked( isset( $this->notif['order_note_active'] ) && $this->notif['order_note_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-note-english">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[order_note]" name="ware_notifications[order_note]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder, 'ware'); ?>"><?php echo isset($this->notif['order_note']) ? esc_textarea($this->notif['order_note']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_note_img]" placeholder="<?php esc_html_e($img_format, 'ware'); ?>" class="image_url regular-text order_note_img upload-text" value="<?php echo esc_attr(isset($this->notif['order_note_img']) ? $this->notif['order_note_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn, 'ware'); ?>" class="upload-btn" data-id="order_note_img">
            </div>
        </div>
    </div>
</div>

    	<?php if ( ! empty( $status_list ) ) : ?>
    <?php foreach ( $status_list as $status_name => $custom_status ) : ?>
        <?php if ( strtolower( $custom_status ) === 'draft' ) continue;?>
    
        <div class="notification-form english">
            <div class="heading-bar">
                <label for="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>]" class="notification-title">
                    <?php printf( __( 'Order - %s:', 'ware' ), esc_html( $status_name ) ); ?>
                </label>
                <div class="card-header">
                    <p class="new">📌 Custom status</p>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>_active]" value="1" <?php checked( isset( $this->notif['order_' . esc_attr( $custom_status ) . '_active'] ) && $this->notif['order_' . esc_attr( $custom_status ) . '_active'] === '1' ); ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="notification nofi-order-<?php echo esc_attr( $custom_status ); ?>-english">
                <div class="form">
                    <br>
                    <hr class="line">
                    <div class="placeholder-container"></div>
                    <textarea id="ware_notifications[order_<?php echo esc_html( $custom_status ); ?>]" name="ware_notifications[order_<?php echo esc_html( $custom_status ); ?>]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your message here..', 'ware' ); ?>"><?php echo esc_textarea( isset( $this->notif[ 'order_' . esc_html( $custom_status ) ] ) ? $this->notif[ 'order_' . esc_html( $custom_status ) ] : '' ); ?></textarea>
                    <div class="upload-field">
                        <input type="text" name="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>_img]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text order_<?php echo esc_attr( $custom_status ); ?>_img upload-text" value="<?php echo esc_attr( isset( $this->notif[ 'order_' . $custom_status . '_img' ] ) ? $this->notif[ 'order_' . $custom_status . '_img' ] : '' ); ?>">
                        <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="order_<?php echo $custom_status; ?>_img">
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


    </div>

    <div class="editor-content layout-ar" data-lang="arabic">
    		<?php
    		$blank_ar           = 'اتركها فارغة لتعطيلها.';
    		$txt_placeholder_ar = 'اكتب رسالتك باللغة العربية هنا...';
    		$img_format_ar      = 'الصيغ المتاحة .png, .jpg, .jpeg';
    		$upload_btn_ar      = 'رفع الصورة';
    		
    		$txt_placeholder_arabic = __('اكتب رسالتك باللغة العربية هنا...', 'ware');
            $img_format_arabic = __('الصيغ المتاحة .png, .jpg, .jpeg', 'ware');
            $upload_btn_arabic = __('رفع الصورة', 'ware');

    		?>
    
    	<div class="notification-form arabic hint">
    		<div class="hint-box">
    			<label for="ware_notifications" class="hint-title"><?php esc_html_e( 'اشعارات حالة الطلب باللغة العربية', 'ware' ); ?></label>
    			<p class="hint-desc"><?php esc_html_e( 'إرسل اشعارات حالة الطلب باللغة العربية تلقائياً، تعمل فقط في حالة اذا كانت اللغة العربية هي لغة الموقع الرئيسية أو اذا كانت لغة اضافية بموقع متعدد اللغات.', 'ware' ); ?></p>
    		</div>
    	</div>
    	
    	
    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[registration_message_arabic]" class="notification-title">
            <?php esc_html_e('إشعار التسجيل', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يتم إرساله عند تسجيل المستخدم في WordPress.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 جديد</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[registration_message_arabic_active]" value="1" <?php checked( isset( $this->notif['registration_message_arabic_active'] ) && $this->notif['registration_message_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-registration-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-containerloginar"></div>
            <textarea id="ware_notifications[registration_message_arabic]" name="ware_notifications[registration_message_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_ar, 'ware'); ?>"><?php echo isset($this->notif['registration_message_arabic']) ? esc_textarea($this->notif['registration_message_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[registration_message_img_arabic]" placeholder="<?php esc_html_e($img_format_ar, 'ware'); ?>" class="image_url regular-text registration_message_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['registration_message_img_arabic']) ? $this->notif['registration_message_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_ar, 'ware'); ?>" class="upload-btn" data-id="registration_message_img_arabic">
            </div>
        </div>
    </div>
</div>
	
    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[login_message_arabic]" class="notification-title">
            <?php esc_html_e('إشعار تسجيل الدخول', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('إرسال عند تسجيل دخول المستخدم إلى ووردبريس.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 جديد</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[login_message_arabic_active]" value="1" <?php checked( isset( $this->notif['login_message_arabic_active'] ) && $this->notif['login_message_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-login-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-containerloginar"></div>
            <textarea id="ware_notifications[login_message_arabic]" name="ware_notifications[login_message_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['login_message_arabic']) ? esc_textarea($this->notif['login_message_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[login_message_arabic_img]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text login_message_arabic_img upload-text" value="<?php echo esc_attr(isset($this->notif['login_message_arabic_img']) ? $this->notif['login_message_arabic_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="login_message_arabic_img">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_onhold_arabic]" class="notification-title">
            <?php esc_html_e('قيد الانتظار', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يتم إرساله عند وضع الطلب في الانتظار.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_onhold_arabic_active]" value="1" <?php checked( isset( $this->notif['order_onhold_arabic_active'] ) && $this->notif['order_onhold_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-onhold-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_onhold_arabic]" name="ware_notifications[order_onhold_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_onhold_arabic']) ? esc_textarea($this->notif['order_onhold_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_onhold_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_onhold_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_onhold_img_arabic']) ? $this->notif['order_onhold_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_onhold_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_pending_arabic]" class="notification-title">
            <?php esc_html_e('بانتظار الدفع', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند تعليق الدفع.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_pending_arabic_active]" value="1" <?php checked( isset( $this->notif['order_pending_arabic_active'] ) && $this->notif['order_pending_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-pending-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_pending_arabic]" name="ware_notifications[order_pending_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_pending_arabic']) ? esc_textarea($this->notif['order_pending_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_pending_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_pending_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_pending_img_arabic']) ? $this->notif['order_pending_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_pending_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_processing_arabic]" class="notification-title">
            <?php esc_html_e('قيد التنفيذ', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عندما يكون الطلب قيد المعالجة.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_processing_arabic_active]" value="1" <?php checked( isset( $this->notif['order_processing_arabic_active'] ) && $this->notif['order_processing_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-processing-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_processing_arabic]" name="ware_notifications[order_processing_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_processing_arabic']) ? esc_textarea($this->notif['order_processing_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_processing_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_processing_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_processing_img_arabic']) ? $this->notif['order_processing_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_processing_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_completed_arabic]" class="notification-title">
            <?php esc_html_e('الطلب مكتمل', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند اكتمال الطلب.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_completed_arabic_active]" value="1" <?php checked( isset( $this->notif['order_completed_arabic_active'] ) && $this->notif['order_completed_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-completed-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_completed_arabic]" name="ware_notifications[order_completed_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_completed_arabic']) ? esc_textarea($this->notif['order_completed_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_completed_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_completed_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_completed_img_arabic']) ? $this->notif['order_completed_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_completed_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_failed_arabic]" class="notification-title">
            <?php esc_html_e('فشل الطلب', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند فشل الطلب.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_failed_arabic_active]" value="1" <?php checked( isset( $this->notif['order_failed_arabic_active'] ) && $this->notif['order_failed_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-failed-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_failed_arabic]" name="ware_notifications[order_failed_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_failed_arabic']) ? esc_textarea($this->notif['order_failed_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_failed_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_failed_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_failed_img_arabic']) ? $this->notif['order_failed_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_failed_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_refunded_arabic]" class="notification-title">
            <?php esc_html_e('ارجاع الطلب', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند استرداد الطلب.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_refunded_arabic_active]" value="1" <?php checked( isset( $this->notif['order_refunded_arabic_active'] ) && $this->notif['order_refunded_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-refunded-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_refunded_arabic]" name="ware_notifications[order_refunded_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_refunded_arabic']) ? esc_textarea($this->notif['order_refunded_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_refunded_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_refunded_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_refunded_img_arabic']) ? $this->notif['order_refunded_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_refunded_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_cancelled_arabic]" class="notification-title">
            <?php esc_html_e('الغاء الطلب', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند إلغاء الطلب.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_cancelled_arabic_active]" value="1" <?php checked( isset( $this->notif['order_cancelled_arabic_active'] ) && $this->notif['order_cancelled_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-cancelled-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_cancelled_arabic]" name="ware_notifications[order_cancelled_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_cancelled_arabic']) ? esc_textarea($this->notif['order_cancelled_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_cancelled_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_cancelled_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_cancelled_img_arabic']) ? $this->notif['order_cancelled_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_cancelled_img_arabic">
            </div>
        </div>
    </div>
</div>

    <div class="notification-form arabic">
    <div class="heading-bar">
        <label for="ware_notifications[order_note_arabic]" class="notification-title">
            <?php esc_html_e('ملاحظات الطلب', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('يرسل عند إضافة ملاحظة إلى الطلب.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[order_note_arabic_active]" value="1" <?php checked( isset( $this->notif['order_note_arabic_active'] ) && $this->notif['order_note_arabic_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-note-arabic">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container-ar"></div>
            <textarea id="ware_notifications[order_note_arabic]" name="ware_notifications[order_note_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_arabic, 'ware'); ?>"><?php echo isset($this->notif['order_note_arabic']) ? esc_textarea($this->notif['order_note_arabic']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[order_note_img_arabic]" placeholder="<?php esc_html_e($img_format_arabic, 'ware'); ?>" class="image_url regular-text order_note_img_arabic upload-text" value="<?php echo esc_attr(isset($this->notif['order_note_img_arabic']) ? $this->notif['order_note_img_arabic'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_arabic, 'ware'); ?>" class="upload-btn" data-id="order_note_img_arabic">
            </div>
        </div>
    </div>
</div>

    <?php if ( ! empty( $status_list ) ) : ?>
    <?php foreach ( $status_list as $status_name => $custom_status ) : ?>
    
    <div class="notification-form arabic">
        <div class="heading-bar">
            <label for="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>_arabic]" class="notification-title">
                <?php printf( __( 'طلب - %s:', 'ware' ), esc_html( $status_name ) ); ?>
            </label>
            <div class="card-header">
                <p class="new">📌 حالة مخصصة</p>
                <div class="switch-container">
                    <label class="switch">
                        <input type="checkbox" name="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>_arabic_active]" value="1" <?php checked( isset( $this->notif['order_' . esc_attr( $custom_status ) . '_arabic_active'] ) && $this->notif['order_' . esc_attr( $custom_status ) . '_arabic_active'] === '1' ); ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="notification nofi-order-<?php echo esc_attr( $custom_status ); ?>-arabic">
            <div class="form">
                <br>
                <hr class="line">
                <div class="placeholder-container-ar"></div>
                <textarea id="ware_notifications[order_<?php echo esc_html( $custom_status ); ?>_arabic]" name="ware_notifications[order_<?php echo esc_html( $custom_status ); ?>_arabic]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'اكتب رسالتك هنا..', 'ware' ); ?>"><?php echo esc_textarea( isset( $this->notif[ 'order_' . esc_html( $custom_status ) . '_arabic' ] ) ? $this->notif[ 'order_' . esc_html( $custom_status ) . '_arabic' ] : '' ); ?></textarea>
                <div class="upload-field">
                    <input type="text" name="ware_notifications[order_<?php echo esc_attr( $custom_status ); ?>_img_arabic]" placeholder="<?php esc_html_e( 'رابط الصورة (بحد أقصى 1 ميجابايت)...', 'ware' ); ?>" class="image_url regular-text order_<?php echo esc_attr( $custom_status ); ?>_img_arabic upload-text" value="<?php echo esc_attr( isset( $this->notif[ 'order_' . $custom_status . '_img_arabic' ] ) ? $this->notif[ 'order_' . $custom_status . '_img_arabic' ] : '' ); ?>">
                    <input type="button" name="upload-btn" value="<?php esc_html_e( 'رفع الصورة', 'ware' ); ?>" class="upload-btn" data-id="order_<?php echo $custom_status; ?>_img_arabic">
                </div>
            </div>
        </div>
    </div>
    		
    <?php endforeach; ?>
    <?php endif; ?>
    
    </div>

	<footer class="ware-panel-footer">
		<input type="submit" class="button-primarywa"
				value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
	</footer>
	
		<?php
	}

	public function admin_notification_settings() {
		if ( $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$status_list      = wc_get_order_statuses();
			$status_list_temp = array();
			$original_status  = array(
				'pending',
				'failed',
				'on-hold',
				'processing',
				'completed',
				'refunded',
				'cancelled',
			);
			foreach ( $status_list as $key => $status ) {
				$status_name = str_replace( 'wc-', '', $key );
				if ( ! in_array( $status_name, $original_status ) ) {
					$status_list_temp[ $status ] = $status_name;
				}
			}
			$status_list = $status_list_temp;
		}
		?>
		<?php settings_fields( 'ware_storage_notifications' ); ?>
			
			
			
	<div class="info-banner">
		<p class="banner-text"><?php esc_html_e( 'Receive WhatsApp messages about new orders or order status updates to stay informed in real-time.', 'ware' ); ?></p>
		<input type="submit" class="button-primarywa saveit top" value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
	</div>
	<hr class="line">
	<div class="notif-layout">
	    
	    
	<div class="notification-form english hint">
	<div class="hint-box">
		<label for="ware_notifications" class="hint-title"><?php esc_html_e( 'Admin order notifications', 'ware' ); ?></label>
		<p class="hint-desc"><?php esc_html_e( 'Stay updated by receiving customer orders directly on your WhatsApp.', 'ware' ); ?></p>
	</div>
	<div class="notification-form phone">
		<div class="heading-bar">
			<label for="ware_notifications[admin_number]" class="notification-title"><?php esc_html_e( 'Your Whatsapp number:', 'ware' ); ?></label>
		</div>
		<div class="notification">
			<div class="phone-field">
				<input id="admin_number" type="text" name="ware_notifications[admin_number]" placeholder="<?php echo esc_attr__( '010 01234567', 'ware' ); ?>" class="admin_number regular-text admin_number upload-text" value="<?php echo esc_attr( isset( $this->notif['admin_number'] ) ? $this->notif['admin_number'] : '' ); ?>">
			</div>
		</div>
	</div>
</div>


	
		<?php
		$blank           = __( 'leave blank to deactivate', 'ware' );
		$txt_placeholder = __( 'Write your message...', 'ware' );
		$img_format      = __( 'Accepts .png, .jpg, .jpeg', 'ware' );
		$upload_btn      = __( 'Upload image', 'ware' );
		$txt_placeholder_admin = __('Enter the admin notification message...', 'ware');
$img_format_admin = __('Image URL (optional)', 'ware');
$upload_btn_admin = __('Upload Image', 'ware');

		?>



        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_registration_message]" class="notification-title">
            <?php esc_html_e('User Registration', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when a user registers on WordPress.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 New</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_registration_message_active]" value="1" <?php checked( isset( $this->notif['admin_registration_message_active'] ) && $this->notif['admin_registration_message_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-registration-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-containerlogin"></div>
            <textarea id="ware_notifications[admin_registration_message]" name="ware_notifications[admin_registration_message]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_registration_message']) ? esc_textarea($this->notif['admin_registration_message']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_registration_message_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_registration_message_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_registration_message_img']) ? $this->notif['admin_registration_message_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_registration_message_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_login_message]" class="notification-title">
            <?php esc_html_e('User Login', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when a user logs in to WordPress.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <p class="new">🎉 New</p>
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_login_message_active]" value="1" <?php checked( isset( $this->notif['admin_login_message_active'] ) && $this->notif['admin_login_message_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-login-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-containerlogin"></div>
            <textarea id="ware_notifications[admin_login_message]" name="ware_notifications[admin_login_message]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_login_message']) ? esc_textarea($this->notif['admin_login_message']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_login_message_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_login_message_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_login_message_img']) ? $this->notif['admin_login_message_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_login_message_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
      <div class="heading-bar">
        <label for="ware_notifications[admin_onhold]" class="notification-title">
            <?php esc_html_e('Admin On-Hold', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to the admin when an order is placed on hold.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_onhold_active]" value="1" <?php checked( isset( $this->notif['admin_onhold_active'] ) && $this->notif['admin_onhold_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-onhold-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_onhold]" name="ware_notifications[admin_onhold]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_onhold']) ? esc_textarea($this->notif['admin_onhold']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_onhold_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_onhold_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_onhold_img']) ? $this->notif['admin_onhold_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_onhold_img">
            </div>
        </div>
    </div>
</div>
        
        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_pending]" class="notification-title">
            <?php esc_html_e('Admin Pending Payment', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order is pending payment.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_pending_active]" value="1" <?php checked( isset( $this->notif['admin_pending_active'] ) && $this->notif['admin_pending_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-pending-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_pending]" name="ware_notifications[admin_pending]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_pending']) ? esc_textarea($this->notif['admin_pending']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_pending_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_pending_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_pending_img']) ? $this->notif['admin_pending_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_pending_img">
            </div>
        </div>
    </div>
</div>
        
        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_processing]" class="notification-title">
            <?php esc_html_e('Admin Order Processing', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order is being processed.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_processing_active]" value="1" <?php checked( isset( $this->notif['admin_processing_active'] ) && $this->notif['admin_processing_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-processing-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_processing]" name="ware_notifications[admin_processing]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_processing']) ? esc_textarea($this->notif['admin_processing']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_processing_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_processing_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_processing_img']) ? $this->notif['admin_processing_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_processing_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_completed]" class="notification-title">
            <?php esc_html_e('Admin Completed Order', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order is completed.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_completed_active]" value="1" <?php checked( isset( $this->notif['admin_completed_active'] ) && $this->notif['admin_completed_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-completed-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_completed]" name="ware_notifications[admin_completed]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_completed']) ? esc_textarea($this->notif['admin_completed']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_completed_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_completed_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_completed_img']) ? $this->notif['admin_completed_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_completed_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_failed]" class="notification-title">
            <?php esc_html_e('Admin Order Failed', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order fails.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_failed_active]" value="1" <?php checked( isset( $this->notif['admin_failed_active'] ) && $this->notif['admin_failed_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-failed-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_failed]" name="ware_notifications[admin_failed]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_failed']) ? esc_textarea($this->notif['admin_failed']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_failed_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_failed_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_failed_img']) ? $this->notif['admin_failed_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_failed_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_refunded]" class="notification-title">
            <?php esc_html_e('Admin Refunded Order', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order is refunded.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_refunded_active]" value="1" <?php checked( isset( $this->notif['admin_refunded_active'] ) && $this->notif['admin_refunded_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-refunded-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_refunded]" name="ware_notifications[admin_refunded]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_refunded']) ? esc_textarea($this->notif['admin_refunded']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_refunded_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_refunded_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_refunded_img']) ? $this->notif['admin_refunded_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_refunded_img">
            </div>
        </div>
    </div>
</div>

        <div class="notification-form admin">
    <div class="heading-bar">
        <label for="ware_notifications[admin_cancelled]" class="notification-title">
            <?php esc_html_e('Admin Order Cancelled', 'ware'); ?>
            <span class="tooltip-text"><?php esc_html_e('Sent to admin when an order is cancelled.', 'ware'); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[admin_cancelled_active]" value="1" <?php checked( isset( $this->notif['admin_cancelled_active'] ) && $this->notif['admin_cancelled_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-order-cancelled-admin">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[admin_cancelled]" name="ware_notifications[admin_cancelled]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e($txt_placeholder_admin, 'ware'); ?>"><?php echo isset($this->notif['admin_cancelled']) ? esc_textarea($this->notif['admin_cancelled']) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[admin_cancelled_img]" placeholder="<?php esc_html_e($img_format_admin, 'ware'); ?>" class="image_url regular-text admin_cancelled_img upload-text" value="<?php echo esc_attr(isset($this->notif['admin_cancelled_img']) ? $this->notif['admin_cancelled_img'] : ''); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e($upload_btn_admin, 'ware'); ?>" class="upload-btn" data-id="admin_cancelled_img">
            </div>
        </div>
    </div>
</div>

        <?php if ( ! empty( $status_list ) ) : ?>
    <?php foreach ( $status_list as $status_name => $custom_status ) : ?>
        <div class="notification-form admin">
            <div class="heading-bar">
                <label for="ware_notifications[admin_order_<?php echo esc_attr( $custom_status ); ?>]" class="notification-title">
                    <?php printf( __( 'Admin Order - %s', 'ware' ), esc_html( $status_name ) ); ?>
                </label>
                <div class="card-header">
                    <p class="new">📌 Custom status</p>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="ware_notifications[admin_order_<?php echo esc_attr( $custom_status ); ?>_active]" value="1" <?php checked( isset( $this->notif['admin_order_' . esc_attr( $custom_status ) . '_active'] ) && $this->notif['admin_order_' . esc_attr( $custom_status ) . '_active'] === '1' ); ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="notification nofi-order-<?php echo esc_attr( $custom_status ); ?>-admin">
                <div class="form">
                    <br>
                    <hr class="line">
                    <div class="placeholder-container"></div>
                    <textarea id="ware_notifications[admin_order_<?php echo esc_html( $custom_status ); ?>]" name="ware_notifications[admin_order_<?php echo esc_html( $custom_status ); ?>]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your message here..', 'ware' ); ?>"><?php echo esc_textarea( isset( $this->notif[ 'admin_order_' . esc_html( $custom_status ) ] ) ? $this->notif[ 'admin_order_' . esc_html( $custom_status ) ] : '' ); ?></textarea>
                    <div class="upload-field">
                        <input type="text" name="ware_notifications[admin_order_<?php echo esc_attr( $custom_status ); ?>_img]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text admin_order_<?php echo esc_attr( $custom_status ); ?>_img upload-text" value="<?php echo esc_attr( isset( $this->notif[ 'admin_order_' . $custom_status . '_img' ] ) ? $this->notif[ 'admin_order_' . $custom_status . '_img' ] : '' ); ?>">
                        <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="admin_order_<?php echo $custom_status; ?>_img">
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<footer class="ware-panel-footer">
	<input type="submit" class="button-primarywa"
			value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
</footer>
		<?php
	}

	public function followup_settings() {
		?>
		<?php settings_fields( 'ware_storage_notifications' ); ?>

	<div class="info-banner">
		<p class="banner-text"><?php esc_html_e( 'Retarget customers with WhatsApp messages based on their order status and a timeframe you set.', 'ware' ); ?></p>
		<input type="submit" class="button-primarywa saveit top" value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
	</div>
	<hr class="line">
		<?php
		$blank           = __( 'leave blank to deactivate', 'ware' );
		$txt_placeholder = __( 'Write your message...', 'ware' );
		$img_format      = __( 'Accepts .png, .jpg, .jpeg', 'ware' );
		$upload_btn      = __( 'Upload image', 'ware' );
		$timer_icon      = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="timer-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M13 7h-2v5.414l3.293 3.293 1.414-1.414L13 11.586z"></path></svg>';

		?>

	<div class="notification-form english hint">
		<div class="hint-box">
			<label for="ware_notifications" class="hint-title"><?php esc_html_e( 'Order follow up notifications', 'ware' ); ?></label>
			<p class="hint-desc"><?php esc_html_e( 'Send timely follow-up messages to customers after their orders are placed, keeping them informed about their order status and encouraging engagement.', 'ware' ); ?></p>
		</div>
	</div>


	<div class="tabs">
	<input type="radio" name="tabs" id="tabone" checked="checked">
	<label for="tabone"><?php esc_html_e( 'On-Hold', 'ware' ); ?> </label>
	<div class="tab one">
		<div class="layout">
		    
			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_onhold]" class="notification-title"><?php esc_html_e( '#1 - On-hold follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Remind customers their order is awaiting fulfillment and reassure them.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_onhold_active]" value="1" <?php checked( isset( $this->notif['followup_onhold_active'] ) && $this->notif['followup_onhold_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-onhold">
      
        <div class="form">
              <br>
            <hr class="line">
            <!-- Add textareas for  English messages -->
              <div class="placeholder-container"></div>
              <textarea id="ware_notifications[followup_onhold]" name="ware_notifications[followup_onhold]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( $txt_placeholder, 'ware' ); ?>"><?php echo isset( $this->notif['followup_onhold'] ) ? esc_textarea( $this->notif['followup_onhold'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_onhold_img]" placeholder="<?php esc_html_e( $img_format, 'ware' ); ?>" class="image_url regular-text followup_onhold_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_img'] ) ? $this->notif['followup_onhold_img'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( $upload_btn, 'ware' ); ?>" class="upload-btn" data-id="followup_onhold_img">
            </div>
               <hr class="sep">
    <div class="timer">
        <label for="ware_notifications[followup_onhold_day]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
        <div class="input-with-hours">
            <?php echo $timer_icon; ?>
            <input id="ware_notifications[followup_onhold_day]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_onhold_day]" type="number" placeholder="<?php esc_html_e( '24', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_day'] ) ? $this->notif['followup_onhold_day'] : '' ); ?>">
            <span class="hours-label">hours</span>
        </div>
    </div>
        </div>
        
    </div>
 
</div>

			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_onhold_2]" class="notification-title"><?php esc_html_e( '#2 - On-hold follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Remind customers their order is still awaiting fulfillment and reassure them again.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_onhold_2_active]" value="1" <?php checked( isset( $this->notif['followup_onhold_2_active'] ) && $this->notif['followup_onhold_2_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-onhold-2">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_onhold_2]" name="ware_notifications[followup_onhold_2]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your follow-up message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_onhold_2'] ) ? esc_textarea( $this->notif['followup_onhold_2'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_onhold_img_2]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_onhold_img_2 upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_img_2'] ) ? $this->notif['followup_onhold_img_2'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_onhold_img_2">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_onhold_day_2]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_onhold_day_2]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_onhold_day_2]" type="number" placeholder="<?php esc_html_e( '48', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_day_2'] ) ? $this->notif['followup_onhold_day_2'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_onhold_3]" class="notification-title"><?php esc_html_e( '#3 - On-hold follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Remind customers their order is still on hold.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_onhold_3_active]" value="1" <?php checked( isset( $this->notif['followup_onhold_3_active'] ) && $this->notif['followup_onhold_3_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-onhold-3">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_onhold_3]" name="ware_notifications[followup_onhold_3]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your follow-up message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_onhold_3'] ) ? esc_textarea( $this->notif['followup_onhold_3'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_onhold_img_3]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_onhold_img_3 upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_img_3'] ) ? $this->notif['followup_onhold_img_3'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_onhold_img_3">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_onhold_day_3]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_onhold_day_3]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_onhold_day_3]" type="number" placeholder="<?php esc_html_e( '72', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_day_3'] ) ? $this->notif['followup_onhold_day_3'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

	    	<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_onhold_4]" class="notification-title"><?php esc_html_e( '#4 - On-hold follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Remind customers their order is still on hold after 96 hours.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_onhold_4_active]" value="1" <?php checked( isset( $this->notif['followup_onhold_4_active'] ) && $this->notif['followup_onhold_4_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-onhold-4">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_onhold_4]" name="ware_notifications[followup_onhold_4]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your follow-up message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_onhold_4'] ) ? esc_textarea( $this->notif['followup_onhold_4'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_onhold_img_4]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_onhold_img_4 upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_img_4'] ) ? $this->notif['followup_onhold_img_4'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_onhold_img_4">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_onhold_day_4]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_onhold_day_4]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_onhold_day_4]" type="number" placeholder="<?php esc_html_e( '96', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_onhold_day_4'] ) ? $this->notif['followup_onhold_day_4'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

		</div>
	</div>              
			  
	<input type="radio" name="tabs" id="tabtwo">
	<label for="tabtwo"><?php esc_html_e( 'Post Purchase', 'ware' ); ?></label>
	<div class="tab two">
		<div class="layout">
		    
			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_aftersales]" class="notification-title"><?php esc_html_e( 'Aftersales Follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Send a follow-up message to customers after their order is completed.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_aftersales_active]" value="1" <?php checked( isset( $this->notif['followup_aftersales_active'] ) && $this->notif['followup_aftersales_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-aftersales">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_aftersales]" name="ware_notifications[followup_aftersales]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your aftersales message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_aftersales'] ) ? esc_textarea( $this->notif['followup_aftersales'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_aftersales_img]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_aftersales_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_img'] ) ? $this->notif['followup_aftersales_img'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_aftersales_img">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_aftersales_day]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_aftersales_day]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_aftersales_day]" type="number" placeholder="<?php esc_html_e( '48', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_day'] ) ? $this->notif['followup_aftersales_day'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_aftersales_2]" class="notification-title">
            <?php esc_html_e( 'Aftersales Follow-up 2', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Send a second follow-up message to customers after their order is completed.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_aftersales_2_active]" value="1" <?php checked( isset( $this->notif['followup_aftersales_2_active'] ) && $this->notif['followup_aftersales_2_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-aftersales-2">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_aftersales_2]" name="ware_notifications[followup_aftersales_2]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your aftersales message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_aftersales_2'] ) ? esc_textarea( $this->notif['followup_aftersales_2'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_aftersales_img_2]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_aftersales_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_img_2'] ) ? $this->notif['followup_aftersales_img_2'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_aftersales_img_2">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_aftersales_day_2]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_aftersales_day_2]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_aftersales_day_2]" type="number" placeholder="<?php esc_html_e( '72', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_day_2'] ) ? $this->notif['followup_aftersales_day_2'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_aftersales_3]" class="notification-title">
            <?php esc_html_e( 'Aftersales Follow-up 3', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Send a third follow-up message to customers after their order is completed.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_aftersales_3_active]" value="1" <?php checked( isset( $this->notif['followup_aftersales_3_active'] ) && $this->notif['followup_aftersales_3_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-aftersales-3">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_aftersales_3]" name="ware_notifications[followup_aftersales_3]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your aftersales message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_aftersales_3'] ) ? esc_textarea( $this->notif['followup_aftersales_3'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_aftersales_img_3]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_aftersales_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_img_3'] ) ? $this->notif['followup_aftersales_img_3'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_aftersales_img_3">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_aftersales_day_3]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_aftersales_day_3]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_aftersales_day_3]" type="number" placeholder="<?php esc_html_e( '96', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_day_3'] ) ? $this->notif['followup_aftersales_day_3'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
    
</div>

			<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_aftersales_4]" class="notification-title">
            <?php esc_html_e( 'Aftersales Follow-up 4', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Send a fourth follow-up message to customers after their order is completed.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_aftersales_4_active]" value="1" <?php checked( isset( $this->notif['followup_aftersales_4_active'] ) && $this->notif['followup_aftersales_4_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-aftersales-4">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_aftersales_4]" name="ware_notifications[followup_aftersales_4]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your aftersales message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_aftersales_4'] ) ? esc_textarea( $this->notif['followup_aftersales_4'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_aftersales_img_4]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_aftersales_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_img_4'] ) ? $this->notif['followup_aftersales_img_4'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_aftersales_img_4">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_aftersales_day_4]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_aftersales_day_4]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_aftersales_day_4]" type="number" placeholder="<?php esc_html_e( '120', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_aftersales_day_4'] ) ? $this->notif['followup_aftersales_day_4'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

		</div>
	</div>              
</div>  
<footer class="ware-panel-footer">
	<input type="submit" class="button-primarywa" value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
</footer>


		<?php
	}

	public function abandoned_cart_settings() {
		?>
		<?php settings_fields( 'ware_storage_notifications' ); ?>

	<div class="info-banner">
		<p class="banner-text"><?php esc_html_e( 'Target visitors who abandoned their shopping carts after entering their details but did not complete the purchase.', 'ware' ); ?></p>
		<?php
		if ( is_plugin_active( 'woo-save-abandoned-carts/cartbounty-abandoned-carts.php' ) ) {
			?>
		<input type="submit" class="button-primarywa saveit top" value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
			<?php
		}
		?>
	</div>
	<hr class="line">
	<div class="tab">
	  
		<div class="form-table ware-table">
		<?php
		if ( ! is_plugin_active( 'woo-save-abandoned-carts/cartbounty-abandoned-carts.php' ) ) {
			printf(
				__(
					'<div class="hint-head"><span>Enable Abandoned Cart Notifications</span></div><div class="hint-info"><p class="desc">Install the <strong>Cartbounty plugin</strong> to activate notifications for abandoned carts.</p><a href="%s" class="banner-cta">Install Cartbounty</a></div>',
					'ware'
				),
				admin_url( 'plugin-install.php?s=Cartbounty%20Abandoned%20Cart&tab=search&type=term' )
			);
		}
		?>
		</div>
		<?php
		if ( is_plugin_active( 'woo-save-abandoned-carts/cartbounty-abandoned-carts.php' ) ) {
			?>
		  
			<?php
			$blank           = __( 'leave blank to deactivate', 'ware' );
			$txt_placeholder = __( 'Write your message...', 'ware' );
			$img_format      = __( 'Accepts .png, .jpg, .jpeg', 'ware' );
			$upload_btn      = __( 'Upload image', 'ware' );
			$timer_icon      = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="timer-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M13 7h-2v5.414l3.293 3.293 1.414-1.414L13 11.586z"></path></svg>';

			?>
		
	<div class="notification-form english hint">
		<div class="hint-box">
			<label for="ware_notifications" class="hint-title"><?php esc_html_e( 'Abandoned cart notifications', 'ware' ); ?></label>
			<p class="hint-desc"><?php esc_html_e( 'Remind customers about the items left in their cart and encourage them to complete their purchase with personalized WhatsApp messages.', 'ware' ); ?></p>
		</div>
	</div>

		<div class="notification-form">
    <div class="heading-bar">
        <label for="ware_notifications[followup_abandoned]" class="notification-title">
            <?php esc_html_e( 'Abandoned Cart Follow-up', 'ware' ); ?>
            <span class="tooltip-text"><?php esc_html_e( 'Send a follow-up message to customers who have abandoned their cart.', 'ware' ); ?></span>
        </label>
        <div class="card-header">
            <div class="switch-container">
                <label class="switch">
                    <input type="checkbox" name="ware_notifications[followup_abandoned_active]" value="1" <?php checked( isset( $this->notif['followup_abandoned_active'] ) && $this->notif['followup_abandoned_active'] === '1' ); ?>>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class="notification nofi-followup-abandoned">
        <div class="form">
            <br>
            <hr class="line">
            <div class="placeholder-container"></div>
            <textarea id="ware_notifications[followup_abandoned]" name="ware_notifications[followup_abandoned]" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( 'Write your abandoned cart message here..', 'ware' ); ?>"><?php echo isset( $this->notif['followup_abandoned'] ) ? esc_textarea( $this->notif['followup_abandoned'] ) : ''; ?></textarea>
            <div class="upload-field">
                <input type="text" name="ware_notifications[followup_abandoned_img]" placeholder="<?php esc_html_e( 'Image URL (Max 1 MB)...', 'ware' ); ?>" class="image_url regular-text followup_abandoned_img upload-text" value="<?php echo esc_attr( isset( $this->notif['followup_abandoned_img'] ) ? $this->notif['followup_abandoned_img'] : '' ); ?>">
                <input type="button" name="upload-btn" value="<?php esc_html_e( 'Upload Image', 'ware' ); ?>" class="upload-btn" data-id="followup_abandoned_img">
            </div>
            <hr class="sep">
            <div class="timer">
                <label for="ware_notifications[followup_abandoned_day]"><?php esc_html_e( 'Send message after:', 'ware' ); ?></label>
                <div class="input-with-hours">
                    <?php echo $timer_icon; ?>
                    <input id="ware_notifications[followup_abandoned_day]" class="admin_number regular-text admin_number upload-text" name="ware_notifications[followup_abandoned_day]" type="number" placeholder="<?php esc_html_e( '24', 'ware' ); ?>" value="<?php echo esc_attr( isset( $this->notif['followup_abandoned_day'] ) ? $this->notif['followup_abandoned_day'] : '' ); ?>">
                    <span class="hours-label">hours</span>
                </div>
            </div>
        </div>
    </div>
</div>

			<?php
		}
		?>

	</div>
	
			<?php
			if ( is_plugin_active( 'woo-save-abandoned-carts/cartbounty-abandoned-carts.php' ) ) {
				?>
				<footer class="ware-panel-footer">
				<input type="submit" class="button-primarywa"
						value="<?php esc_html_e( 'Save Changes', 'ware' ); ?>">
			</footer>
				<?php
			}
			?>
		<?php
	}

	public function other_settings() {
		?>
		<?php settings_fields( 'ware_storage_notifications' ); ?>

	
		<?php
	}

	public function help_info() {
		?>
		<?php settings_fields( 'ware_storage_notifications' ); ?>

	<div class="info-banner">
		<p class="banner-text"><?php esc_html_e( 'Send messages to any WhatsApp number from this section.', 'ware' ); ?></p>
	</div>
	<hr class="line">
		<?php
		$blank           = __( 'leave blank to deactivate', 'ware' );
		$txt_placeholder = __( 'Write your message...', 'ware' );
		$img_format      = __( 'Accepts .png, .jpg, .jpeg', 'ware' );
		$upload_btn      = __( 'Upload image', 'ware' );
		?>
		<div class="ware-panel">

<form method="post">    
	<div class="notification-form msg">
		<div class="heading">
			<label for="ware_test-message" class="notification-title"><?php esc_html_e( 'Send WhatsApp message to:', 'ware' ); ?>
				<span class="tooltip-text"><?php esc_html_e( 'You can send a WhatsApp message to an individual customer directly from here.', 'ware' ); ?></span>
			</label>
			<input id="ware_test_number" class="admin_number regular-text admin_number upload-text" name="ware_test_number" type="text">
		</div>
		<hr class="divi">
		<div class="notification">
			<div class="form">
				<!-- Add textareas for  English messages -->
				<textarea id="ware_test_message" name="ware_test_message" cols="50" rows="5" class="ware-emoji" placeholder="<?php esc_html_e( $txt_placeholder, 'ware' ); ?>"></textarea>
				<div class="upload-field">
					<input type="text" name="ware_test_image" placeholder="<?php esc_html_e( $img_format, 'ware' ); ?>" class="image_url regular-text ware-test-image upload-text">
					<input type="button" name="upload-btn" value="<?php esc_html_e( $upload_btn, 'ware' ); ?>" class="upload-btn" data-id="ware-test-image">
				</div>
			</div>
		</div>
		<hr class="divi">
		<input type="submit" name="ware_send_test" class="button-primarywa" value="<?php esc_html_e( 'Send Message', 'ware' ); ?>">
	</div>


</form>
		</div>
		<?php
	}

	public function setup_info() {
		?>
			<div class="info-body">
		<div>
			<form method="post" action="options.php" class="setting-form">
		<?php settings_fields( 'ware_storage_instances' ); ?>
			<div class="heading-bar credential">
				<div class="access-title">
					<span><?php esc_html_e( 'Notifications number', 'ware' ); ?></span>
				</div>
				<p><span><?php esc_html_e( 'WhatsApp number for order updates. Learn ', 'ware' ); ?></span>
				<a href="https://app.arrivewhats.com/whatsapp_profile" class="" target="_blank"><?php esc_html_e( 'how to connect.', 'ware' ); ?></a>
				</p>
			</div>
			<label for="ware_instances[instance_id]" class="keys-label">
		<?php esc_html_e( 'Instance ID', 'ware' ); ?></label>
			<input type="text" id="instance_id" name="ware_instances[instance_id]" placeholder="Your instance ID" class="regular-text data" value="<?php echo esc_attr( isset( $this->instances['instance_id'] ) ? $this->instances['instance_id'] : '' ); ?>">
<label for="ware_instances[access_token]" class="keys-label">
		<?php esc_html_e( 'Access token', 'ware' ); ?>
					</label>
				<input type="text" id="access_token" name="ware_instances[access_token]" placeholder="Your access token" class="regular-text data" value="<?php echo esc_attr( isset( $this->instances['access_token'] ) ? $this->instances['access_token'] : '' ); ?>">
				<input type="submit" class="setting-button"
					value="<?php esc_html_e( 'Connect', 'ware' ); ?>" style="margin-top:10px;">  
				</form>

		<?php if ( isset( $this->instances['access_token'] ) && isset( $this->instances['instance_id'] ) ) : ?>
				<div class="instance-control">
					<p><strong><?php esc_html_e( 'Instance Control', 'ware' ); ?></strong></p>
				   
					<a href="#" class="button button-secondarywa ins-action" data-action="status"><?php esc_html_e( 'Connection status', 'ware' ); ?></a>
			 
			<a href="#" class="button button-secondarywa ins-action" data-action="connectionButtons"><?php esc_html_e( 'Connection test', 'ware' ); ?></a>
			 
					<div class="instance-desc">
						<br>
					<strong> <span>▼</span>  <?php esc_html_e( 'Control Description', 'ware' ); ?> </strong>
						<div>
							<strong><?php esc_html_e( 'Connection status', 'ware' ); ?>:  </strong><?php esc_html_e( 'A connection test is performed between the WhatsApp number and the ArriveWoosystem to inform you of the result whether it is connected or not', 'ware' ); ?>
							<br>
							<strong><?php esc_html_e( 'Connection test', 'ware' ); ?>:  </strong><?php esc_html_e( 'A WhatsApp message is sent from your number registered with ArriveWooand added to the WordPress plugin settings (account ID and access Token) to the ArriveWooBot number to verify that the plugin is active and notifications are sent normally.', 'ware' ); ?>
					</div>      
				</div>
			  
				<div id="control-modal" class="modal"></div>
		<?php endif; ?>
			</div>

		</div>
		
		<div class="setting-banner"></div>


		<?php
	}

    public function logs_page() {
    $logger        = new ware_logger();
    $customer_logs = $logger->get_log_file('waresend');
    if (isset($_GET['clear_logs']) && $_GET['clear_logs'] == 1) {
        $handle_to_clear = 'waresend';
        $logger->clear($handle_to_clear);
        echo '<div class="notice notice-success is-dismissible"><p>';
        echo __('Logs cleared successfully.', 'ware');
        echo '</p></div>';
    }
    $message_types = $this->get_unique_log_values($customer_logs, 8);
    $plugin_statuses = $this->get_unique_log_values($customer_logs, 9);

    ?>
    <div class="wrap" id="ware-wrap">
    <div class="form-wrapper">
        <div class="ware-tab-wrapper">
            <div class="hint-inf mob">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 68, 68, 1);transform: ;msFilter:;" class="alert-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8.589 8 8-8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                <p class="desc"><?php esc_html_e('To view your ArriveWoolog, please browse from your computer.', 'ware'); ?></p>
            </div>
            <div class="hint-inf">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 68, 68, 1);transform: ;msFilter:;" class="alert-icon"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8.589 8 8-8-3.589 8-8 8z"></path><path d="M11 11h2v6h-2zm0-4h2v2h-2z"></path></svg>
                <p class="desc"><?php esc_html_e("ArriveWooLog only counts notification messages, we'll release a new update to count login and checkout confirmation messages.", 'ware'); ?></p>
            </div>
            <div class="search-container">
                <label for="log-search"><?php esc_html_e('Search in log:', 'ware'); ?></label>
                <div style="display: flex; flex-direction: row; gap: 16px; align-items: center;">
                    <input type="text" id="log-search" placeholder="<?php esc_html_e('Type to search...Date/WhatsApp Number/Message/Image Attachment/Status', 'ware'); ?>">
                    <a href="#" id="clear-logs-btn" data-action="<?php echo admin_url('admin.php?page=ware-message-log&clear_logs=1'); ?>" class="button log-clear"><?php esc_html_e('Clear Logs', 'ware'); ?></a>
                </div>
            </div>
            
            <!-- Filters Container -->
            <div class="filters-container">
                <!-- Date Range Filter -->
                <div class="inline-input-group">
                    <div class="input-group">
                        <label for="date-from" class="input-label"><?php esc_html_e('From', 'ware'); ?></label>
                        <input type="datetime-local" id="date-from" placeholder="<?php esc_html_e('From', 'ware'); ?>">
                    </div>
                    <div class="input-group">
                        <label for="date-to" class="input-label"><?php esc_html_e('To', 'ware'); ?></label>
                        <input type="datetime-local" id="date-to" placeholder="<?php esc_html_e('To', 'ware'); ?>">
                    </div>
                </div>

                <!-- Preset Date Range Filter -->
                <div class="inline-input-group">
                    <div class="input-group">
                        <label for="preset-date-range" class="input-label"><?php esc_html_e('Preset Date Ranges:', 'ware'); ?></label>
                        <select id="preset-date-range">
                            <option value=""><?php esc_html_e('Select Preset Range', 'ware'); ?></option>
                            <option value="1"><?php esc_html_e('Last Day', 'ware'); ?></option>
                            <option value="7"><?php esc_html_e('Last 7 Days', 'ware'); ?></option>
                            <option value="30"><?php esc_html_e('Last 30 Days', 'ware'); ?></option>
                            <option value="90"><?php esc_html_e('Last 3 Months', 'ware'); ?></option>
                            <option value="180"><?php esc_html_e('Last 6 Months', 'ware'); ?></option>
                            <option value="365"><?php esc_html_e('Last Year', 'ware'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Phone Number Filter -->
                <div class="inline-input-group">
                    <div class="input-group">
                        <label for="phone-filter" class="input-label"><?php esc_html_e('Phone Number:', 'ware'); ?></label>
                        <input type="text" id="phone-filter" placeholder="<?php esc_html_e('Enter phone number', 'ware'); ?>">
                    </div>
                </div>

                <!-- Message Type Filter -->
                <div class="inline-input-group">
                    <div class="input-group">
                        <label for="message-type-filter" class="input-label"><?php esc_html_e('Message Type:', 'ware'); ?></label>
                        <select id="message-type-filter">
                            <option value=""><?php esc_html_e('All', 'ware'); ?></option>
                            <?php foreach ($message_types as $type): ?>
                                <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <div class="inline-input-group">
                    <button id="clear-filters" class="button"><?php esc_html_e('Clear Filters', 'ware'); ?></button>
                </div>
            </div>

            <div id="result-count" style="margin-bottom: 10px;"></div>

            <table class="wp-list-table widefat fixed striped table-view-list posts table-message-logs" style="margin:10px 0;">
                <thead>
                    <tr class="header-row">
                        <th><?php esc_html_e('Record Num', 'ware'); ?></th> <!-- New field to number records -->
                        <th><?php esc_html_e('User ID', 'ware'); ?></th>
                        <th><?php esc_html_e('Order ID', 'ware'); ?></th>
                        <th><?php esc_html_e('Customer Name', 'ware'); ?></th>
                        <th><?php esc_html_e('Date', 'ware'); ?></th>
                        <th><?php esc_html_e('WhatsApp Number', 'ware'); ?></th>
                        <th><?php esc_html_e('Message', 'ware'); ?></th>
                        <th><?php esc_html_e('Image Attachment', 'ware'); ?></th>
                        <th><?php esc_html_e('Message Type', 'ware'); ?></th>
                        <th><?php esc_html_e('arrivewhats.com status', 'ware'); ?></th>
                        <th><?php esc_html_e('Resend', 'ware'); ?></th>
                    </tr>
                </thead>
                <tbody id="log-entries">
                    <?php 
                    $customer_logs = isset($customer_logs) && is_string($customer_logs) ? $customer_logs : '';

                $id_counter = 1; 
                echo preg_replace_callback(
                    '/<tr><td><\/td><td>(.*?)<\/td><td><a href="(.*?)">(.*?)<\/a><\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><\/tr>/',
                    function ($matches) use (&$id_counter) {
                        return '<tr><td>' . $id_counter++ . '</td>' .
                               '<td><a href="' . esc_url(get_edit_user_link($matches[1])) . '">' . esc_html($matches[1]) . '</a></td>' .
                               '<td><a href="' . esc_url($matches[2]) . '">' . esc_html($matches[3]) . '</a></td>' .
                               '<td>' . esc_html($matches[4]) . '</td>' .
                               '<td class="log-date" data-date="' . esc_html($matches[5]) . '">' . '</td>' .
                               '<td class="log-phone" data-phone="' . esc_html($matches[6]) . '">' . esc_html($matches[6]) . ' <span class="edit-phone"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 68, 68, 1);transform: ;msFilter:;" class="edit-icon"><path d="M12 21c-.56 0-1.12-.16-1.6-.48L6.22 18.2c-.74-.46-.83-1.4-.2-1.96l6.58-5.88c.56-.5 1.44-.5 2 0l6.58 5.88c.63.56.54 1.5-.2 1.96l-4.18 2.32c-.48.32-1.04.48-1.6.48zM6 15.7L12 20l6-4.3L12 12 6 15.7zM12 11.4l6-5.4H6l6 5.4z"/></svg></span></td>' .
                               '<td class="log-message" data-message="' . esc_html($matches[7]) . '"></td>' .
                               '<td>' . $this->get_image_preview_html($matches[8]) . '</td>' .
                               '<td>' . $matches[9] . '</td></tr>';
                    },
                    $customer_logs
            );

                    ?>
                </tbody>   
            </table>
            <div id="log-popup" class="log-popup">
                <div class="log-popup-content">
                    <span class="log-popup-close">&times;</span>
                    <div id="log-popup-details"></div>
                </div>
            </div>
        </div>
        <div class="info">
        <?php
        $this->setup_info();
        ?>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#log-search, #date-from, #date-to, #phone-filter, #message-type-filter').on('change keyup', function() {
        filterAndSortLogs();
    });
    $('#preset-date-range').on('change', function() {
        var days = parseInt($(this).val());
        if (days) {
            var toDate = new Date();
            var fromDate = new Date();
            fromDate.setDate(fromDate.getDate() - days);
            var formattedToDate = toDate.toISOString().slice(0, 16);
            var formattedFromDate = fromDate.toISOString().slice(0, 16);

            $('#date-from').val(formattedFromDate);
            $('#date-to').val(formattedToDate);

            filterAndSortLogs();
        }
    });
    $('#clear-filters').on('click', function() {
        $('#log-search').val('');
        $('#date-from').val('');
        $('#date-to').val('');
        $('#phone-filter').val('');
        $('#message-type-filter').val('');
        $('#preset-date-range').val('');
        filterAndSortLogs();
    });

    function filterAndSortLogs() {
        var searchQuery = $('#log-search').val().toLowerCase();
        var fromDate = $('#date-from').val();
        var toDate = $('#date-to').val();
        var phoneFilter = $('#phone-filter').val().toLowerCase();
        var messageTypeFilter = $('#message-type-filter').val().toLowerCase(); 
        var resultCount = 0;
        var rows = $('#log-entries tr').get();
        $.each(rows, function(index, row) {
            var $row = $(row);
            var date = $row.find('td:nth-child(5)').text();
            var phone = $row.find('td:nth-child(6)').text().toLowerCase();
            var messageType = $row.find('td:nth-child(9)').text().toLowerCase();
            var show = true;
            if (searchQuery && $row.text().toLowerCase().indexOf(searchQuery) === -1) {
                show = false;
            }
            if (fromDate && toDate) {
                var rowDate = new Date(date.replace(' ', 'T'));
                var startDate = new Date(fromDate);
                var endDate = new Date(toDate);
                if (rowDate < startDate || rowDate > endDate) {
                    show = false;
                }
            }
            if (phoneFilter && !phone.includes(phoneFilter)) {
                show = false;
            }
            if (messageTypeFilter && messageType !== messageTypeFilter) {
                show = false;
            }

            if (show) {
                $row.show();
                resultCount++;
            } else {
                $row.hide();
            }
        });
        $('#log-entries').html(rows.reverse());
        $('#result-count').text(resultCount + ' ' + '<?php esc_html_e("Results", "ware"); ?>');
    }
    filterAndSortLogs();
    $('.log-date').hover(function() {
        $(this).css('background-color', '#e0e0e0');
    }, function() {
        $(this).css('background-color', '');
    }).click(function() {
        $(this).text($(this).data('date')); 
    });
    $('.log-message').html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 68, 68, 1);transform: ;msFilter:;" class="message-icon"><path d="M3 3h18v18H3z" fill="none"/><path d="M12 21c-.56 0-1.12-.16-1.6-.48L6.22 18.2c-.74-.46-.83-1.4-.2-1.96l6.58-5.88c.56-.5 1.44-.5 2 0l6.58 5.88c.63.56.54 1.5-.2 1.96l-4.18 2.32c-.48.32-1.04.48-1.6.48zM6 15.7L12 20l6-4.3L12 12 6 15.7zM12 11.4l6-5.4H6l6 5.4z"/></svg>')
        .click(function() {
            $(this).text($(this).data('message'));
        });
    $('.log-img').each(function() {
        var imgUrl = $(this).text();
        if (imgUrl) {
            $(this).html('<img src="' + imgUrl + '" alt="Image Preview" style="max-width: 48px; max-height: 48px;border-radius: 5px;" />');
        }
    });
    $('.edit-phone').click(function() {
        var $phoneCell = $(this).closest('.log-phone');
        var phoneNumber = $phoneCell.text().trim();
        $phoneCell.html('<input type="text" class="editable-phone" value="' + phoneNumber + '" />');
        $('.editable-phone').focus();

        $('.editable-phone').on('blur', function() {
            var newPhoneNumber = $(this).val().trim();
            $phoneCell.text(newPhoneNumber);
            $.post(ajaxurl, {
                action: 'update_phone_number_in_log',
                phone_number: newPhoneNumber,
                original_phone_number: phoneNumber,
            });
            $.post(ajaxurl, {
                action: 'update_billing_phone_number',
                phone_number: newPhoneNumber,
                order_id: $phoneCell.closest('tr').find('td:nth-child(3) a').text()
            });
        });
    });
    $(document).on('click', '.button.log-resend', function (event) {
        event.preventDefault();

        var instanceId = $(this).data('instance-id');
        var accessToken = $(this).data('access-token');
        var phone = $(this).data('phone');
        var message = $(this).data('message');
        var img = $(this).data('img');

        var url = 'https://app.arrivewhats.com/api/send?' + $.param({
            access_token: accessToken,
            instance_id: instanceId,
            number: phone,
            message: message
        });

        $.getJSON(url, function (data) {
            $.post(ajaxurl, {
                action: 'log_resend_message',
                phone: phone,
                message: message,
                img: img,
                message_type: 'Log Resend',
                response_data: JSON.stringify(data)
            }, function(response) {
                location.reload();
            });

            console.log('Success:', data);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            var errorData = {
                status: 'Error',
                statusText: textStatus,
                error: errorThrown
            };

            $.post(ajaxurl, {
                action: 'log_resend_message',
                phone: phone,
                message: message,
                img: img,
                message_type: 'Log Resend',
                response_data: JSON.stringify(errorData) 
            }, function(response) {
                location.reload();
            });

            console.error('Error:', textStatus, errorThrown);
        });
    });

    $(document).on('click', '.plugin-status-link', function (event) {
        event.preventDefault();
        var popupData = $(this).data('popup');
        $('#log-popup-details').html('<pre>' + JSON.stringify(popupData, null, 2) + '</pre>');
        $('#log-popup').show();
    });

    $('.log-popup-close').on('click', function() {
        $('#log-popup').hide();
    });
    $(window).on('click', function(event) {
        if (event.target.id === 'log-popup') {
            $('#log-popup').hide();
        }
    });

    $('#clear-logs-btn').on('click', function(e) {
        e.preventDefault();
        if (confirm('<?php esc_html_e('Are you sure you want to clear all logs?', 'ware'); ?>')) {
            window.location.href = $(this).data('action');
        }
    });
});


</script>
    
<?php
}

    private function get_image_preview_html($img_url) {
        if ($img_url) {
            return '<img src="' . esc_url($img_url) . '" alt="Image Preview" style="max-width: 48px; max-height: 48px;;border-radius: 5px;" />';
        }
        return '';
    }
    
    private function get_unique_log_values($logs, $column_index) {
        $values = [];
        if (is_string($logs) && !empty($logs)) {
            preg_match_all('/<tr>.*?<td.*?>.*?<\/td>' . str_repeat('.*?<td.*?>.*?<\/td>', $column_index - 1) . '.*?<td.*?>(.*?)<\/td>/', $logs, $matches);
            
            foreach ($matches[1] as $value) {
                if (!in_array($value, $values)) {
                    $values[] = $value;
                }
            }
        }
        return $values;
    }
    private function convert_to_hijri($date_time) {
        return $date_time->format('Y-m-d H:i:s');
    }
}