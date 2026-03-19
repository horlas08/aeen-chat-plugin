<?php
/**
 * Plugin Name: AeenWoo
 * Version: 3.1.0
 * Plugin URI: https://aeentech.com/aeen-woo/
 * Description: AeenWoo - Connect your WooCommerce store to Chatwoot via WhatsApp using AeenTech. Send order updates, recover abandoned carts, and more.
 * Author: aeentech.com
 * Author URI: https://aeentech.com
 * Text Domain: aeen-woo
 * Domain Path: /languages
 *
 * @package AeenWoo
 */
 
/**
 * Class AeenWoo
 *
 * Main class for the AeenWoo plugin.
 */
class AeenWoo {

    /**
     * AeenWoo constructor.
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
     */
    public function on_activation() {
        // Activation logic
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
            <p><?php _e( 'AeenWoo requires WooCommerce to be installed and active.', 'aeen-woo' ); ?></p>
            <p><a href="<?php echo esc_url( $install_url ); ?>" class="button button-primary"><?php _e( 'Install WooCommerce Now', 'aeen-woo' ); ?></a></p>
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
        load_plugin_textdomain( 'aeen-woo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
        require_once 'class-aeen-woo-api.php';
require_once 'class-aeen-woo-settings.php';
        require_once 'class-ware-countrycode.php';
        require_once 'class-ware-system-info.php';
    }
}

// Define plugin constants.
define( 'AEENWOO_NAME', 'aeen-woo' );
define( 'AEENWOO_VERSION', '3.1.0' );
define( 'AEENWOO_URL', plugin_dir_url( __FILE__ ) );
define( 'AEENWOO_PATH', plugin_dir_path( __FILE__ ) );
define( 'AEENWOO_DOMAIN', 'aeen-woo' );

// Initialize the plugin.
new AeenWoo();
new AeenWoo_Settings();
