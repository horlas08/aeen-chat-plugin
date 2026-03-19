<?php
/**
 * ArriveWhats WA notife.
 *
 * @package arrivebotCampany.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ware_checkout_otp {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_save_blocked_numbers', array( $this, 'save_blocked_numbers' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'init', array( $this, 'load_otp_verification' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_filter( 'manage_users_columns', array( $this, 'add_user_columns' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'show_user_column_data' ), 10, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this, 'make_columns_sortable' ) );
		add_action( 'pre_get_users', array( $this, 'sort_users_by_column' ) );
		add_action( 'wp_ajax_update_user_phone_number', array( $this, 'update_user_phone_number' ) );
		add_action( 'wp_ajax_nopriv_update_user_phone_number', array( $this, 'update_user_phone_number' ) );
		add_action( 'wp_ajax_nopriv_verify_otp', array( $this, 'verify_otp_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_check_if_phone_number_blocked', array( $this, 'check_if_phone_number_blocked' ) );
	}

	public function update_user_phone_number() {
		check_ajax_referer( 'otp-ajax-nonce', 'security' );
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( esc_html__( 'User is not logged in.', 'ware' ) );
			wp_die();
		}

		$phone_number = sanitize_text_field( $_POST['phone_number'] );
		if ( empty( $phone_number ) ) {
			wp_send_json_error( esc_html__( 'Phone number is missing.', 'ware' ) );
			wp_die();
		}

		$current_user = wp_get_current_user();
		update_user_meta( $current_user->ID, 'billing_phone', $phone_number );
		wp_send_json_success( esc_html__( 'Phone number updated successfully.', 'ware' ) );
	}

	// Enqueue scripts and styles
	public function enqueue_scripts() {
		wp_enqueue_script( 'ware-checkout-js', plugin_dir_url( __FILE__ ) . 'assets/js/checkout.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'ware-checkout-css', plugin_dir_url( __FILE__ ) . 'assets/css/checkout.css', array(), '1.0', 'all' );

		$translation_array = array(
			'otp_sent_success'     => esc_html__( 'OTP sent successfully via WhatsApp.', 'ware' ),
			'otp_sent_failure'     => esc_html__( 'Failed to send OTP. Please try again.', 'ware' ),
			'otp_verified_success' => esc_html__( 'OTP verified successfully.', 'ware' ),
			'otp_incorrect'        => esc_html__( 'Incorrect OTP. Please try again.', 'ware' ),
			'phone_registered'     => esc_html__( 'This phone number is already registered. Please login or use a different number.', 'ware' ),
		);
		wp_localize_script( 'ware-checkout-js', 'ware_translations', $translation_array );

		wp_localize_script(
			'ware-checkout-js',
			'otpAjax',
			array(
				'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'nonce'                 => wp_create_nonce( 'otp-ajax-nonce' ),
				'isLoggedIn'            => is_user_logged_in() ? 'true' : 'false',
				'enableForVisitorsOnly' => get_option( 'ware_enable_otp_for_visitors', 'no' ),
			)
		);
	}

	// Register settings, section, and fields
	public function register_settings() {
		add_option( 'ware_enable_otp', 'no' );
		add_option( 'ware_enable_otp_for_visitors', 'no' );
		add_option( 'ware_otp_message_template', 'Hi {{name}}, {{otp}} is your checkout Generated OTP code. Do not share this code with others.' );
		add_option( 'ware_blocked_numbers', '' );

		register_setting( 'ware_options_group', 'ware_enable_otp' );
		register_setting( 'ware_options_group', 'ware_enable_otp_for_visitors' );
		register_setting( 'ware_options_group', 'ware_otp_message_template' );
		register_setting( 'ware_options_group', 'ware_blocked_numbers' );

		add_settings_section( 'ware_settings_section', esc_html__( '', 'ware' ), null, 'ware-settings' );
		add_settings_field( 'ware_enable_otp_field', esc_html__( 'Enable checkout verification', 'ware' ), array( $this, 'enable_otp_field_callback' ), 'ware-settings', 'ware_settings_section' );
		add_settings_field( 'ware_enable_otp_for_visitors_field', esc_html__( 'Disable for logged in users', 'ware' ), array( $this, 'enable_otp_for_visitors_field_callback' ), 'ware-settings', 'ware_settings_section' );
		add_settings_field( 'ware_otp_message_template_field', esc_html__( 'Message template', 'ware' ), array( $this, 'otp_message_template_field_callback' ), 'ware-settings', 'ware_settings_section' );
		add_settings_field( 'ware_blocked_numbers_field', esc_html__( 'Blocked phone numbers', 'ware' ), array( $this, 'blocked_numbers_field_callback' ), 'ware-settings', 'ware_settings_section' );
	}

	public function blocked_numbers_field_callback() {
		$value = get_option( 'ware_blocked_numbers', '' );
		echo '<input id="ware_blocked_numbers" name="ware_blocked_numbers" value="' . esc_attr( $value ) . '" />';
		echo '<p>' . esc_html__( 'Enter blocked phone numbers separated by commas (,).', 'ware' ) . '</p>';
	}

	public function enable_otp_field_callback() {
		$value   = get_option( 'ware_enable_otp', 'no' );
		$checked = checked( $value, 'yes', false );
		echo '<label class="switch">';
		echo '<input type="checkbox" id="ware_enable_otp" name="ware_enable_otp" value="yes"' . $checked . ' />';
		echo '<span class="slider"></span>';
		echo '</label>';
	}

	public function enable_otp_for_visitors_field_callback() {
		$value   = get_option( 'ware_enable_otp_for_visitors', 'no' );
		$checked = checked( $value, 'yes', false );
		echo '<label class="switch">';
		echo '<input type="checkbox" id="ware_enable_otp_for_visitors" name="ware_enable_otp_for_visitors" value="yes"' . $checked . ' />';
		echo '<span class="slider"></span>';
		echo '</label>';
	}

	public function otp_message_template_field_callback() {
		$value = get_option( 'ware_otp_message_template', 'Hi {{name}}, {{otp}} is your Checkout Generated OTP code. Do not share this code with others.' );
		?>
		<div class="notification-form english otp-card">
			<div class="heading-bar">
				<label for="login_message" class="notification-title"><?php esc_html_e( 'Checkout OTP message', 'ware' ); ?>
					<span class="tooltip-text"><?php esc_html_e( "Sent during checkout to verify the customer's number.", 'ware' ); ?></span>
				</label>
			</div>
			<hr class="line">
			<div class="notification">
				<div class="form">
					<textarea id="ware_otp_message_template" name="ware_otp_message_template" rows="5" cols="85" class="otp_message"><?php echo esc_textarea( $value ); ?></textarea>
					<p class="placeholders">
						<?php esc_html_e( 'Shortcodes: ', 'ware' ); ?>
						<code>{{name}}</code> <?php esc_html_e( 'Member name', 'ware' ); ?> —
						<code>{{otp}}</code> <?php esc_html_e( 'Generated OTP code', 'ware' ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	public function add_settings_page() {
		$hook = add_menu_page(
			__( 'Checkout OTP Verification', 'ware' ),
			__( 'Checkout OTP', 'ware' ),
			'manage_options',
			'ware-checkout-otp',
			array( $this, 'settings_page_html' )
		);
		remove_menu_page( 'ware-checkout-otp' );
	}

	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		include_once plugin_dir_path( __FILE__ ) . 'admin/ware-checkout-admin.php';
	}

	public function admin_enqueue_scripts( $hook ) {
		global $pagenow;
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'ware-checkout-otp' === $_GET['page'] ) {
			wp_enqueue_style( 'ware-checkoutadmin-css', plugin_dir_url( __FILE__ ) . 'assets/css/ware-admin-style.css', array(), '1.0', 'all' );
			if ( is_rtl() ) {
				wp_enqueue_style( 'ware-admin-rtl-css', plugins_url( 'assets/css/ware-admin-rtl-style.css', __FILE__ ), array(), '1.1.4' );
			}
			wp_enqueue_style( 'tagify-css', plugin_dir_url( __FILE__ ) . 'assets/css/resources/tagify.css', array(), '1.0', 'all' );
			wp_enqueue_script( 'tagify-js', plugin_dir_url( __FILE__ ) . 'assets/js/resources/tagify.js', array( 'jquery' ), '4.9.1', true );
			wp_enqueue_script( 'ware-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/checkout-admin.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				'ware-admin-js',
				'ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'ware_ajax_nonce' ),
				)
			);
		}
	}

	public function save_blocked_numbers() {
		check_ajax_referer( 'ware_ajax_nonce', 'security' );
		$blocked_numbers = sanitize_text_field( $_POST['blocked_numbers'] );
		update_option( 'ware_blocked_numbers', $blocked_numbers );
		wp_send_json_success( esc_html__( 'Blocked numbers saved successfully.', 'ware' ) );
	}

	public function load_otp_verification() {
		$enable_otp              = get_option( 'ware_enable_otp', 'no' );
		$enable_otp_for_visitors = get_option( 'ware_enable_otp_for_visitors', 'no' );

		if ( 'yes' === $enable_otp || ( 'yes' === $enable_otp_for_visitors && ! is_user_logged_in() ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'woocommerce_after_order_notes', array( $this, 'add_otp_verification_popup' ) );
			add_action( 'wp_ajax_send_otp', array( $this, 'send_otp_ajax_handler' ) );
			add_action( 'wp_ajax_nopriv_send_otp', array( $this, 'send_otp_ajax_handler' ) );
			add_action( 'wp_ajax_verify_otp', array( $this, 'verify_otp_ajax_handler' ) );
			add_action( 'wp_ajax_nopriv_verify_otp', array( $this, 'verify_otp_ajax_handler' ) );
			add_action( 'woocommerce_checkout_process', array( $this, 'check_if_otp_verified' ) );
			add_action( 'wp_ajax_verify_phone_number', array( $this, 'verify_phone_number_ajax_handler' ) );
			add_action( 'wp_ajax_nopriv_verify_phone_number', array( $this, 'verify_phone_number_ajax_handler' ) );
			add_action( 'woocommerce_checkout_process', array( $this, 'check_if_phone_number_blocked' ) );
		}
	}

	public function verify_phone_number_ajax_handler() {
		check_ajax_referer( 'otp-ajax-nonce', 'security' );
		$phone_number = sanitize_text_field( $_POST['phone_number'] );
		if ( empty( $phone_number ) ) {
			wp_send_json_error(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Phone number is missing.', 'ware' ),
				)
			);
			wp_die();
		}

		$blocked_numbers = explode( ',', get_option( 'ware_blocked_numbers', '' ) );
		$blocked_numbers = array_map( 'trim', $blocked_numbers );

		if ( in_array( $phone_number, $blocked_numbers ) ) {
			wp_send_json_error(
				array(
					'status'  => 'blocked',
					'message' => esc_html__( 'This phone number is blocked.', 'ware' ),
				)
			);
			wp_die();
		}

		if ( is_user_logged_in() ) {
			$current_user      = wp_get_current_user();
			$user_phone_number = get_user_meta( $current_user->ID, 'billing_phone', true );
			$phone_verified    = get_user_meta( $current_user->ID, 'phone_verified', true );

			if ( $phone_number === $user_phone_number ) {
				if ( $phone_verified ) {
					wp_send_json_success(
						array(
							'status'  => 'verified',
							'message' => esc_html__( 'Phone number matches user account and is verified.', 'ware' ),
						)
					);
				} else {
					wp_send_json_error(
						array(
							'status'  => 'not_verified',
							'message' => esc_html__( 'Phone number matches user account but is not verified.', 'ware' ),
						)
					);
				}
				wp_die();
			}
		}

		$user_query = new WP_User_Query(
			array(
				'meta_key'   => 'billing_phone',
				'meta_value' => $phone_number,
				'number'     => 1,
				'exclude'    => is_user_logged_in() ? array( get_current_user_id() ) : array(),
			)
		);

		if ( ! empty( $user_query->get_results() ) ) {
			wp_send_json_error(
				array(
					'status'  => 'registered',
					'message' => esc_html__( 'This phone number is already registered. Please use a different number.', 'ware' ),
				)
			);
			wp_die();
		}

		wp_send_json_success(
			array(
				'status'  => 'not_registered',
				'message' => esc_html__( 'Phone number is not registered.', 'ware' ),
			)
		);
	}


	public function check_if_phone_number_blocked() {
		if ( ! is_user_logged_in() ) {
			$phone_number = WC()->checkout->get_value( 'billing_phone' );
		} else {
			$current_user = wp_get_current_user();
			$phone_number = get_user_meta( $current_user->ID, 'billing_phone', true );
		}

		$blocked_numbers = explode( ',', get_option( 'ware_blocked_numbers', '' ) );
		$blocked_numbers = array_map( 'trim', $blocked_numbers );

		if ( in_array( $phone_number, $blocked_numbers ) ) {
			wc_add_notice( __( 'This phone number is blocked. Please use a different number.', 'ware' ), 'error' );
		}
	}

	public function add_otp_verification_popup() {
		?>
		<div id="ware_otp_popup" class="ware-otp-popup" style="display:none;">
			<div class="ware-otp-popup-content">
				<div class="ware-otp-box">
					<h3 class="ware-title">
						<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" style="fill: #00a884;transform: ;msFilter:;">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.633A8.919 8.919 0 0 0 12.053 3c-4.948 0-8.976 4.027-8.978 8.977 0 1.582.413 3.126 1.198 4.488L3 21.116l4.759-1.249a8.981 8.981 0 0 0 4.29 1.093h.004c4.947 0 8.975-4.027 8.977-8.977a8.926 8.926 0 0 0-2.627-6.35m-6.35 13.812h-.003a7.446 7.446 0 0 1-3.798-1.041l-.272-.162-2.824.741.753-2.753-.177-.282a7.448 7.448 0 0 1-1.141-3.971c.002-4.114 3.349-7.461 7.465-7.461a7.413 7.413 0 0 1 5.275 2.188 7.42 7.42 0 0 1 2.183 5.279c-.002 4.114-3.349 7.462-7.461 7.462m4.093-5.589c-.225-.113-1.327-.655-1.533-.73-.205-.075-.354-.112-.504.112s-.58.729-.711.879-.262.168-.486.056-.947-.349-1.804-1.113c-.667-.595-1.117-1.329-1.248-1.554s-.014-.346.099-.458c.101-.1.224-.262.336-.393.112-.131.149-.224.224-.374s.038-.281-.019-.393c-.056-.113-.505-1.217-.692-1.666-.181-.435-.366-.377-.504-.383a9.65 9.65 0 0 0-.429-.008.826.826 0 0 0-.599.28c-.206.225-.785.767-.785 1.871s.804 2.171.916 2.321c.112.15 1.582 2.415 3.832 3.387.536.231.954.369 1.279.473.537.171 1.026.146 1.413.089.431-.064 1.327-.542 1.514-1.066.187-.524.187-.973.131-1.067-.056-.094-.207-.151-.43-.263"></path>
						</svg>
						<?php esc_html_e( 'Confirm your order', 'ware' ); ?>
					</h3>
					<span class="ware-otp-popup-close">&times;</span>
				</div>
				<div class="ware-otp-content">
					<p class="ware-desc"><?php esc_html_e( 'To complete your order, enter the 6-digit code sent via WhatsApp to', 'ware' ); ?>
						<span id="user_phone_number"></span>
					</p>
					<div class="ware-popup-message"></div>
					<div class="ware-otp-error" style="display:none;"></div>
					<div class="otp-inputs">
						<input type="tel" class="otp-input" maxlength="1">
						<input type="tel" class="otp-input" maxlength="1">
						<input type="tel" class="otp-input" maxlength="1">
						<input type="tel" class="otp-input" maxlength="1">
						<input type="tel" class="otp-input" maxlength="1">
						<input type="tel" class="otp-input" maxlength="1">
					</div>
					<div class="ware-btn-group">
						<button type="button" class="button alt" id="ware_verify_otp_btn"><?php esc_html_e( 'Confirm order', 'ware' ); ?></button>
						<button type="button" class="button alt" id="ware_resend_otp_btn" disabled><?php esc_html_e( 'Resend code', 'ware' ); ?><span id="ware_resend_timer"></span></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function send_otp_ajax_handler() {
		check_ajax_referer( 'otp-ajax-nonce', 'security' );
		$phone_number = sanitize_text_field( $_POST['phone_number'] );
		$first_name   = sanitize_text_field( $_POST['first_name'] );

		if ( empty( $phone_number ) ) {
			wp_send_json_error( esc_html__( 'Phone number is missing.', 'ware' ) );
			wp_die();
		}

		$blocked_numbers = explode( ',', get_option( 'ware_blocked_numbers', '' ) );
		$blocked_numbers = array_map( 'trim', $blocked_numbers );

		if ( in_array( $phone_number, $blocked_numbers ) ) {
			wp_send_json_error( esc_html__( 'This phone number is blocked.', 'ware' ) );
			wp_die();
		}

		$otp = rand( 100000, 999999 );
		WC()->session->set( 'otp', $otp );
		WC()->session->set( 'otp_verified', false );

		if ( ! AeenWoo_API::is_configured() ) {
			error_log( esc_html__( 'AeenWoo settings are missing.', 'ware' ) );
			wp_send_json_error( esc_html__( 'Plugin settings are missing.', 'ware' ) );
			wp_die();
		}

		$message_template = get_option( 'ware_otp_message_template', 'Hi {{name}}, {{otp}} is your Generated OTP code to make order. Do not share this code with others.' );
		$message          = str_replace( array( '{{name}}', '{{otp}}' ), array( $first_name, $otp ), $message_template );

		error_log( 'Sending OTP via WhatsApp to: ' . $phone_number );
        $response = AeenWoo_API::send_message( $phone_number, $message );

		if ( 'success' !== $response['status'] ) {
			error_log( esc_html__( 'Failed to send OTP via WhatsApp. Error: ', 'ware' ) . $response['message'] );
			wp_send_json_error( esc_html__( 'Failed to send OTP via WhatsApp. Error: ', 'ware' ) . $response['message'] );
		} else {
            error_log( 'OTP Sent Successfully' );
			wp_send_json_success( esc_html__( 'OTP sent successfully via WhatsApp.', 'ware' ) );
		}
	}

	public function verify_otp_ajax_handler() {
		check_ajax_referer( 'otp-ajax-nonce', 'security' );
		$user_otp    = sanitize_text_field( $_POST['otp'] );
		$correct_otp = WC()->session->get( 'otp' );

		if ( $user_otp == $correct_otp ) {
			WC()->session->set( 'otp_verified', true );

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				update_user_meta( $current_user->ID, 'phone_verified', true );
			}

			wp_send_json_success( esc_html__( 'OTP verified successfully.', 'ware' ) );
		} else {
			wp_send_json_error( esc_html__( 'Incorrect OTP. Please try again.', 'ware' ) );
		}
	}

	public function check_if_otp_verified() {
		$enable_otp              = get_option( 'ware_enable_otp', 'no' );
		$enable_otp_for_visitors = get_option( 'ware_enable_otp_for_visitors', 'no' );

		if ( 'yes' === $enable_otp || ( 'yes' === $enable_otp_for_visitors && ! is_user_logged_in() ) ) {
			if ( is_user_logged_in() ) {
				$current_user   = wp_get_current_user();
				$phone_verified = get_user_meta( $current_user->ID, 'phone_verified', true );

				if ( $phone_verified ) {
					return;
				}
			} elseif ( WC()->session->get( 'otp_verified' ) ) {
					return;
			}

			if ( ! WC()->session->get( 'otp_verified' ) ) {
				wc_add_notice( esc_html__( 'Please verify the OTP before placing your order.', 'ware' ), 'error' );
			}
		}

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$phone_number = get_user_meta( $current_user->ID, 'billing_phone', true );
		} else {
			$phone_number = WC()->checkout->get_value( 'billing_phone' );
		}

		$blocked_numbers = explode( ',', get_option( 'ware_blocked_numbers', '' ) );
		$blocked_numbers = array_map( 'trim', $blocked_numbers );

		if ( in_array( $phone_number, $blocked_numbers ) ) {
			wc_add_notice( __( 'This phone number is blocked. Please use a different number.', 'ware' ), 'error' );
		}
	}

	public function add_user_columns( $columns ) {
		$columns['whatsapp_number']       = esc_html__( 'Whatsapp Number', 'ware' );
		$columns['whatsapp_verification'] = esc_html__( 'WhatsApp Verified', 'ware' );
		return $columns;
	}

	public function show_user_column_data( $value, $column_name, $user_id ) {
		if ( 'whatsapp_number' === $column_name ) {
			$phone_number = get_user_meta( $user_id, 'billing_phone', true );
			$value        = ! empty( $phone_number ) ? esc_html( $phone_number ) : esc_html__( 'N/A', 'ware' );
		}
		if ( 'whatsapp_verification' === $column_name ) {
			$phone_verified = get_user_meta( $user_id, 'phone_verified', true );
			if ( $phone_verified ) {
				$value = '<span style="color: green;">&#x1F4F1; Verified</span>';
			} else {
				$value = '<span style="color: red;">&#x274C; Not verified</span>';
			}
		}
		return $value;
	}

	public function make_columns_sortable( $columns ) {
		$columns['whatsapp_number']       = 'whatsapp_number';
		$columns['whatsapp_verification'] = 'whatsapp_verification';
		return $columns;
	}


	public function sort_users_by_column( $query ) {
		global $pagenow;

		if ( $pagenow !== 'users.php' ) {
			return;
		}

		if ( isset( $query->query_vars['orderby'] ) ) {
			if ( 'whatsapp_number' === $query->query_vars['orderby'] ) {
				$query->query_vars['meta_key'] = 'billing_phone';
				$query->query_vars['orderby']  = 'meta_value';
			}
			if ( 'whatsapp_verification' === $query->query_vars['orderby'] ) {
				$query->query_vars['meta_key'] = 'phone_verified';
				$query->query_vars['orderby']  = 'meta_value';
			}
		}
	}
}

new ware_checkout_otp();
?>
