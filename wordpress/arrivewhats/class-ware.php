<?php
/**
 * Plugin Name: ArriveWoo- WooCommerce Order Notifications, and OTP Verification.
 * Version: 3.1.0
 * Plugin URI: https://arrivewhats.com/wa-woocommerce/
 * Description: ArriveWoois the best way to send & receive order updates, recover abandoned carts, drive repeat sales, and secure your store using OTP – all via WhatsApp.
 * Author: arrivewhats.com
 * Author URI: https://arrivewhats.com
 * Text Domain: WAR
 * Domain Path: /languages
 *
 * @package arrivebotCampany
 */
 
/**
 * Class WAR
 *
 * Main class for the ArriveWooplugin.
 */
class WAR {

    /**
     * WAR constructor.
     *
     * Initializes the plugin by setting up hooks and loading dependencies.
     */
    public function __construct() {
        register_activation_hook( __FILE__, array( $this, 'on_activation' ) );

        add_action( 'admin_init', array( $this, 'check_woocommerce_active' ) );
        add_action( 'admin_head', array( $this, 'admin_head_styles' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        

        $this->include_required_files();
        new WWO();
        new ware_Main();
    }

    /**
     * Runs on plugin activation.
     *
     * This function is triggered when the plugin is activated.
     */
    public function on_activation() {
        $this->send_one_time();
    }

    /**
     * Checks if WooCommerce is active.
     *
     * Displays an admin notice if WooCommerce is not active.
     */
    public function check_woocommerce_active() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', array( $this, 'woocommerce_inactive_notice' ) );
        }
    }

    /**
     * Displays an admin notice if WooCommerce is inactive.
     */
    public function woocommerce_inactive_notice() {
        $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'ArriveWoo- Instant Order Notifications & OTP Verification for WooCommerce requires WooCommerce to be installed and active.', 'ware' ); ?></p>
            <p><a href="<?php echo esc_url( $install_url ); ?>" class="button button-primary"><?php _e( 'Install WooCommerce Now', 'ware' ); ?></a></p>
        </div>
        <?php
    }

    /**
     * Adds custom styles to the admin head.
     */
    public function admin_head_styles() {
        ?>
        <style>
            li#toplevel_page_ware img {
                width: 18px;
            }
        </style>
        <?php
    }

    /**
     * Loads the plugin's text domain for translation.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'ware', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Includes the required files for the plugin.
     */
    public function include_required_files() {
        require_once 'class-wwo.php';
        require_once 'class-ware-checkout-otp.php';
        require_once 'class-ware-main.php';
        require_once 'class-ware-ui.php';
        require_once 'class-logger.php';
        require_once 'class-ware-mainset.php';
        require_once 'class-ware-countrycode.php';
        require_once 'class-ware-system-info.php';
    }

    /**
     * Sends a one-time email with site information.
     */
    public function send_one_time() {
        // Increment email send count.
        $email_count = get_option( 'one_time_count', 0 ) + 1;
        update_option( 'one_time_count', $email_count );

        // Retrieve admin user data.
        $admin_user = get_userdata( 1 );
        if ( ! $admin_user ) {
            return;
        }

        $admin_email = $admin_user->user_email;
        $site_name   = get_bloginfo( 'name' );
        $site_url    = home_url();

        // Retrieve the access token from options.
        $instances    = get_option( 'ware_instances' );
        $access_token = isset( $instances['access_token'] ) ? sanitize_text_field( $instances['access_token'] ) : '';

        if ( empty( $access_token ) ) {
            return;
        }

        // Compose the email message.
        $message  = "Welcome,\n";
        $message .= 'My email: ' . sanitize_email( $admin_email ) . "\n";
        $message .= 'My site name: ' . esc_html( $site_name ) . "\n";
        $message .= 'My website link: ' . esc_url( $site_url ) . "\n";
        $message .= 'Access Token: ' . esc_html( $access_token ) . "\n";
        $message .= 'Email sent count: ' . intval( $email_count ) . "\n";

        // Set email recipient, subject, and headers.
        $to      = 'activation@arrivewhats.com';
        $subject = 'Welcome Message from ' . esc_html( $site_name );
        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

        // Send the email.
        wp_mail( $to, $subject, $message, $headers );
    }
}

// Define plugin constants.
define( 'WWO_NAME', 'ware' );
define( 'WWO_VERSION', '3.0.4' );
define( 'WWO_URL', plugin_dir_url( __FILE__ ) );
define( 'WWO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WWO_DOMAIN', 'ware' );

// Initialize the plugin.
new WAR();
