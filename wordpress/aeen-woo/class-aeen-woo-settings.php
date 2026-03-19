<?php
/**
 * AeenWoo Settings.
 *
 * @package AeenWoo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AeenWoo_Settings {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		// Menu added via class-ware-main.php
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function enqueue_admin_assets() {
		global $pagenow;
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'ware-settings' === $_GET['page'] ) {
			// Enqueue styles if needed. Reusing existing style for now or create new.
			// wp_enqueue_style( 'aeen-woo-admin-css', plugins_url( 'assets/css/aeen-woo-admin.css', __FILE__ ) );
            // Using standard WP styles for now to ensure cleanliness.
		}
	}

	public function register_settings() {
		register_setting( 'aeen_woo_settings_group', 'aeen_woo_settings' );

		add_settings_section(
			'aeen_woo_connection_section',
			__( 'Connection Settings', 'aeen-woo' ),
			null,
			'ware-settings'
		);

		add_settings_field(
			'store_url',
			__( 'Chatwoot Store URL', 'aeen-woo' ),
			array( $this, 'render_field' ),
			'ware-settings',
			'aeen_woo_connection_section',
			array( 'field' => 'store_url' )
		);

		add_settings_field(
			'access_code',
			__( 'Access Code', 'aeen-woo' ),
			array( $this, 'render_field' ),
			'ware-settings',
			'aeen_woo_connection_section',
			array( 'field' => 'access_code' )
		);

        add_settings_field(
			'account_id',
			__( 'Account ID', 'aeen-woo' ),
			array( $this, 'render_field' ),
			'ware-settings',
			'aeen_woo_connection_section',
			array( 'field' => 'account_id' )
		);

        // Inbox Select
		add_settings_field(
			'inbox_id',
			__( 'Inbox Channel (WhatsApp)', 'aeen-woo' ),
			array( $this, 'render_inbox_select' ),
			'ware-settings',
			'aeen_woo_connection_section'
		);
	}

	public function render_field( $args ) {
		$options = get_option( 'aeen_woo_settings' );
		$field = $args['field'];
		$value = isset( $options[ $field ] ) ? $options[ $field ] : '';
		echo '<input type="text" name="aeen_woo_settings[' . esc_attr( $field ) . ']" value="' . esc_attr( $value ) . '" class="regular-text">';
        if ($field === 'access_code') {
            echo '<p class="description">' . __( 'Copy this from your Chatwoot Integration Settings.', 'aeen-woo' ) . '</p>';
        }
	}

    public function render_inbox_select() {
        $options = get_option( 'aeen_woo_settings' );
        $store_url = isset( $options['store_url'] ) ? $options['store_url'] : '';
        $access_code = isset( $options['access_code'] ) ? $options['access_code'] : '';
        $current_inbox = isset( $options['inbox_id'] ) ? $options['inbox_id'] : '';

        if ( empty( $store_url ) || empty( $access_code ) ) {
            echo '<p>' . __( 'Please save Store URL and Access Code first to fetch inboxes.', 'aeen-woo' ) . '</p>';
            return;
        }

        // Fetch inboxes from Chatwoot
        $api_url = trailingslashit( $store_url ) . 'webhooks/woo/inboxes?access_code=' . $access_code;
        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            echo '<p style="color:red;">' . sprintf( __( 'Error fetching inboxes: %s', 'aeen-woo' ), $response->get_error_message() ) . '</p>';
            return;
        }

        $body = wp_remote_retrieve_body( $response );
        $inboxes = json_decode( $body, true );

        if ( empty( $inboxes ) || ! is_array( $inboxes ) || isset($inboxes['error']) ) {
             $err = isset($inboxes['error']) ? $inboxes['error'] : 'Unknown error';
             echo '<p style="color:red;">' . sprintf( __( 'Could not load inboxes. Response: %s', 'aeen-woo' ), $err ) . '</p>';
             return;
        }

        echo '<select name="aeen_woo_settings[inbox_id]">';
        echo '<option value="">' . __( 'Select an Inbox', 'aeen-woo' ) . '</option>';
        foreach ( $inboxes as $inbox ) {
            $selected = selected( $current_inbox, $inbox['id'], false );
            echo '<option value="' . esc_attr( $inbox['id'] ) . '" ' . $selected . '>' . esc_html( $inbox['name'] ) . '</option>';
        }
        echo '</select>';
    }

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p><?php _e('Configure your connection to Chatwoot.', 'aeen-woo'); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'aeen_woo_settings_group' );
				do_settings_sections( 'ware-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
