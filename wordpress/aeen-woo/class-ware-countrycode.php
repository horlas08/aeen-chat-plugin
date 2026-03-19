<?php
/**
 * ArriveWhats WA notife.
 *
 * @package arrivebotCampany
 */

namespace WAR;

use function esc_attr;
use function esc_html;
use function get_bloginfo;
use function get_option;
use function is_rtl;
use function plugin_dir_url;
use function plugins_url;
use function register_setting;
use function sanitize_text_field;
use function settings_fields;
use function submit_button;
use function update_option;
use function wp_create_nonce;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;
use function wp_send_json_error;
use function wp_send_json_success;

/**
 * Class ArriveWoo_Countrycode
 */
class ArriveWoo_Countrycode {
	/**
	 * Constructor
	 * Initialize hooks for loading textdomain, enqueuing scripts, adding admin menu, registering settings, and handling AJAX requests.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_save_iti_settings', array( $this, 'save_iti_settings' ) );
	}

	/**
	 * Load plugin textdomain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ware', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Enqueue frontend scripts and styles.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'ware-admin-telcss', plugins_url( 'assets/css/intlTelInput.css', __FILE__ ) );

		wp_enqueue_script( 'intlTelInput', plugins_url( 'assets/js/resources/intlTelInput.min.js', __FILE__ ), array( 'jquery' ), '23.0.10', true );
		wp_enqueue_script( 'woocommerce-iti', plugins_url( 'assets/js/woocommerce-iti.js', __FILE__ ), array( 'jquery', 'intlTelInput' ), '1.0.0', true );

		$country_names = array_map(
			function ( $country ) {
				return array(
					'iso2' => $country['iso2'],
					'name' => __( $country['name'], 'ware' ),
				);
			},
			$this->get_all_countries()
		);

		$settings_data = array(
			'allowlist'       => esc_attr( get_option( 'ware_allowlist', '' ) ),
			'default_country' => esc_attr( get_option( 'ware_default_country', 'us' ) ),
			'utilsScriptUrl'  => plugins_url( 'assets/js/resources/utils.js', __FILE__, '23.0.10', true ),
			'countryNames'    => $country_names,
			'isArabic'        => $this->is_arabic_language(),
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
			'nonce'           => wp_create_nonce( 'save_iti_settings' ),
		);
		wp_localize_script( 'woocommerce-iti', 'woocommerceITISettings', $settings_data );

		wp_localize_script(
			'woocommerce-iti',
			'wareTranslations',
			array(
				'changeCountry'        => __( 'Change', 'ware' ),
				'selectDefaultCountry' => __( 'Select Default Country', 'ware' ),
				'settingsSaved'        => __( 'Settings saved successfully!', 'ware' ),
				'invalidOptionPage'    => __( 'Invalid option page.', 'ware' ),
			)
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_admin_scripts( $hook ) {
		global $pagenow;
		if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'ware-countrycode' === $_GET['page'] ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_style( 'jquery-ui-dialog' );

			wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'assets/js/resources/select2.min.js', array( 'jquery' ), '4.0.13', true );
			wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'assets/css/resources/select2.min.css' );

			wp_enqueue_script( 'intlTelInput', plugin_dir_url( __FILE__ ) . 'assets/js/resources/intlTelInput.min.js', array( 'jquery' ), '23.0.10', true );
			wp_enqueue_style( 'intlTelInput-css', plugin_dir_url( __FILE__ ) . 'assets/css/intlTelInput.css' );

			wp_enqueue_script( 'woocommerce-iti-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/woocommerce-iti-admin.js', array( 'jquery', 'intlTelInput', 'jquery-ui-dialog', 'select2' ), '1.0', true );

			if ( is_rtl() ) {
				wp_enqueue_style( 'ware-admin-rtl-css', plugins_url( 'assets/css/ware-admin-rtl-style.css', __FILE__ ), array(), '1.1.4' );
			}

			wp_enqueue_style( 'woocommerce-iti-admin-css', plugin_dir_url( __FILE__ ) . 'assets/css/ware-admin-style.css' );

			// Localize script for Select2 and intlTelInput settings
			wp_localize_script(
				'woocommerce-iti-admin-js',
				'wcITIAdminSettings',
				array(
					'intlTelInputUrl' => plugin_dir_url( __FILE__ ) . 'assets/js/resources/intlTelInput.min.js',
					'utilsScriptUrl'  => plugins_url( 'assets/js/resources/utils.js', __FILE__, '23.0.10', true ),
					'countryNames'    => $this->get_all_countries(),
					'isArabic'        => $this->is_arabic_language(),
					'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
					'nonce'           => wp_create_nonce( 'save_iti_settings' ),
					'changeCountry'   => __( 'Change', 'ware' ),
				)
			);
		}
	}

	/**
	 * Save ITI settings via AJAX.
	 */
	public function save_iti_settings() {
		check_ajax_referer( 'save_iti_settings', 'security' );

		header( 'Content-Type: application/json; charset=utf-8' );

		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === 'ware_settings' ) {
			$default_country = isset( $_POST['ware_default_country'] ) ? sanitize_text_field( $_POST['ware_default_country'] ) : '';
			$allowlist       = isset( $_POST['ware_allowlist'] ) ? array_map( 'sanitize_text_field', (array) $_POST['ware_allowlist'] ) : array();

			// Update options
			update_option( 'ware_default_country', $default_country );
			update_option( 'ware_allowlist', implode( ',', $allowlist ) );

			wp_send_json_success( array( 'message' => __( 'Settings saved successfully!', 'ware' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Invalid option page.', 'ware' ) ) );
		}
	}

	/**
	 * Check if the current language is Arabic.
	 *
	 * @return bool
	 */
	public function is_arabic_language() {
		$site_language = get_bloginfo( 'language' );
		$user_language = get_user_locale();

		return $site_language === 'ar' || strpos( $user_language, 'ar' ) === 0;
	}

	/**
	 * Add admin menu for country code settings.
	 */
	public function add_admin_menu() {
		$hook = add_menu_page(
			__( 'Country code settings', 'ware' ),
			__( 'Country Code', 'ware' ),
			'manage_options',
			'ware-countrycode',
			array( $this, 'settings_page' )
		);
		remove_menu_page( 'ware-countrycode' );
	}

	/**
	 * Register settings for country code.
	 */
	public function register_settings() {
		register_setting( 'ware_settings', 'ware_default_country' );
		register_setting(
			'ware_settings',
			'ware_allowlist',
			array(
				'sanitize_callback' => function ( $input ) {
					return is_array( $input ) ? implode( ',', $input ) : '';
				},
			)
		);
        
		add_settings_section( 'ware_section', '', array( $this, 'settings_section_description' ), 'ware' );
		add_settings_field( 'ware_default_country', __( 'Default country code', 'ware' ), array( $this, 'default_country_field_html' ), 'ware', 'ware_section' );
		add_settings_field( 'ware_allowlist', __( 'Country codes whitelist', 'ware' ), array( $this, 'allowlist_field_html' ), 'ware', 'ware_section' );
	}

	/**
	 * Display settings section description.
	 */
	public function settings_section_description() {
		echo '
        <div class="notification-form english hint">
            <div class="hint-box">
                <label class="hint-title">' . esc_html__( 'ArriveWoo Country  code settings', 'ware' ) . '</label>
                <p class="hint-desc">' . esc_html__( 'Configure the default country code and the list of allowed countries for the WordPress login and register forms, as well as the WooCommerce checkout form.', 'ware' ) . '</p>
                <div class="country-inst">
                    <p class="hint-desc">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: #a9fba5; border: 1px solid #044; border-radius: 100px; transform: rotate(90deg); msFilter:progid:DXImageTransform.Microsoft.BasicImage(rotation=1);"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2z"></path></svg>
                        ' . esc_html__( 'Enabled Country', 'ware' ) . '
                    </p>
                    <p class="hint-desc">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: #ecefeb; border: 1px solid #044; border-radius: 100px; transform: rotate(90deg); msFilter:progid:DXImageTransform.Microsoft.BasicImage(rotation=1);"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-10S17.514 2 12 2z"></path></svg>
                        ' . esc_html__( 'Disabled Country', 'ware' ) . '
                    </p>
                </div>
            </div>
        </div>';
	}

	/**
	 * Display HTML for the default country field.
	 */
	public function default_country_field_html() {
		$allowlist       = esc_attr( get_option( 'ware_allowlist', '' ) );
		$allowlist_array = explode( ',', $allowlist );
		$value           = esc_attr( get_option( 'ware_default_country', 'eg' ) );

		echo '<select id="ware_default_country" name="ware_default_country" style="display: none;">';
		foreach ( $allowlist_array as $country_code ) {
			$selected = $value === $country_code ? 'selected' : '';
			echo '<option value="' . esc_attr( $country_code ) . '" ' . $selected . '>' . esc_html( strtoupper( $country_code ) ) . '</option>';
		}
		echo '</select>';

		echo '<div id="country-select-display" class="country-selected">
                <select id="change-country">';
		foreach ( $allowlist_array as $country_code ) {
			echo '<option value="' . esc_attr( $country_code ) . '" data-image="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.6/flags/4x3/' . esc_attr( $country_code ) . '.svg">' . esc_html( $this->get_country_name( $country_code ) ) . '</option>';
		}
		echo '</select>
            </div>';
	}

	/**
	 * Get the name of a country based on its ISO2 code.
	 *
	 * @param string $country_code The ISO2 code of the country.
	 * @return string The name of the country.
	 */
	public function get_country_name( $country_code ) {
		$all_countries = $this->get_all_countries();
		foreach ( $all_countries as $country ) {
			if ( $country['iso2'] === $country_code ) {
				return __( $country['name'], 'ware' );
			}
		}
		return $country_code; // Return code if not found
	}

	/**
	 * Display HTML for the country allowlist field.
	 */
	public function allowlist_field_html() {
		$value              = esc_attr( get_option( 'ware_allowlist', '' ) );
		$selected_countries = explode( ',', $value );
		$all_countries      = $this->get_all_countries();

		$regions = $this->group_countries_by_region( $all_countries );

		echo '<div class="search-bar">
            <input type="text" id="country-search" placeholder="' . esc_attr__( 'Search for countries...', 'ware' ) . '">
            <button type="button" id="select-all" class="btn regular">' . esc_html__( 'Select All', 'ware' ) . '</button>
            <button type="button" id="deselect-all" class="btn regular">' . esc_html__( 'Deselect All', 'ware' ) . '</button>
        </div>';

		echo '<div class="country-regions">';
		foreach ( $regions as $region => $countries ) {
			echo '<div class="region-group" data-region="' . esc_attr( $region ) . '">';
			echo '<h3 class="region-title">' . esc_html( $region, 'ware' ) . '</h3>';
			echo '<div class="country-checklist">';
			foreach ( $countries as $country ) {
				$checked = in_array( $country['iso2'], $selected_countries ) ? 'checked' : '';
				echo '<label class="country-label ' . ( $checked ? 'active' : '' ) . '" data-country-name="' . esc_attr( $country['name'] ) . '">';
				echo '<input type="checkbox" class="country-checkbox" name="ware_allowlist[]" value="' . esc_attr( $country['iso2'] ) . '" ' . $checked . ' style="display: none;">';
				echo '<span class="country-flag" style="background: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.6/flags/4x3/' . esc_attr( $country['iso2'] ) . '.svg) center center / cover no-repeat;"></span>';
				echo '<span class="country-name">' . esc_html( $country['name'] ) . '</span>';
				echo '<span class="checkmark ' . ( $checked ? 'checked' : '' ) . '"></span>';
				echo '</label>';
			}
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Display the settings page.
	 */
	public function settings_page() {
		?>
		<div id="ware-wrap" class="wrap">
			<form id="ware-settings-form" class="wp-tab-panels" method="post" action="options.php">
				<?php
				settings_fields( 'ware_settings' );
				do_settings_sections( 'ware' );
				submit_button();
				?>
			</form>
			<div id="settings-saved-modal" title="<?php echo esc_html__( 'Settings saved', 'ware' ); ?>" style="display:none;">
				<p><?php echo esc_html__( 'Settings saved successfully!', 'ware' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Get all countries with their details.
	 *
	 * @return array An array of countries with their ISO2 code, name, dial code, and region.
	 */
	public function get_all_countries() {
		return array(
			array(
				'iso2'     => 'eg',
				'name'     => __( 'Egypt', 'ware' ),
				'dialCode' => '20',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'sa',
				'name'     => __( 'Saudi Arabia', 'ware' ),
				'dialCode' => '966',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'af',
				'name'     => __( 'Afghanistan', 'ware' ),
				'dialCode' => '93',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'al',
				'name'     => __( 'Albania', 'ware' ),
				'dialCode' => '355',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'dz',
				'name'     => __( 'Algeria', 'ware' ),
				'dialCode' => '213',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'as',
				'name'     => __( 'American Samoa', 'ware' ),
				'dialCode' => '1684',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'ad',
				'name'     => __( 'Andorra', 'ware' ),
				'dialCode' => '376',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ao',
				'name'     => __( 'Angola', 'ware' ),
				'dialCode' => '244',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ai',
				'name'     => __( 'Anguilla', 'ware' ),
				'dialCode' => '1264',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ag',
				'name'     => __( 'Antigua and Barbuda', 'ware' ),
				'dialCode' => '1268',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ar',
				'name'     => __( 'Argentina', 'ware' ),
				'dialCode' => '54',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'am',
				'name'     => __( 'Armenia', 'ware' ),
				'dialCode' => '374',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'aw',
				'name'     => __( 'Aruba', 'ware' ),
				'dialCode' => '297',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'au',
				'name'     => __( 'Australia', 'ware' ),
				'dialCode' => '61',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'at',
				'name'     => __( 'Austria', 'ware' ),
				'dialCode' => '43',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'az',
				'name'     => __( 'Azerbaijan', 'ware' ),
				'dialCode' => '994',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'bs',
				'name'     => __( 'Bahamas', 'ware' ),
				'dialCode' => '1242',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'bh',
				'name'     => __( 'Bahrain', 'ware' ),
				'dialCode' => '973',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'bd',
				'name'     => __( 'Bangladesh', 'ware' ),
				'dialCode' => '880',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'bb',
				'name'     => __( 'Barbados', 'ware' ),
				'dialCode' => '1246',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'by',
				'name'     => __( 'Belarus', 'ware' ),
				'dialCode' => '375',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'be',
				'name'     => __( 'Belgium', 'ware' ),
				'dialCode' => '32',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'bz',
				'name'     => __( 'Belize', 'ware' ),
				'dialCode' => '501',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'bj',
				'name'     => __( 'Benin', 'ware' ),
				'dialCode' => '229',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'bm',
				'name'     => __( 'Bermuda', 'ware' ),
				'dialCode' => '1441',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'bt',
				'name'     => __( 'Bhutan', 'ware' ),
				'dialCode' => '975',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'bo',
				'name'     => __( 'Bolivia', 'ware' ),
				'dialCode' => '591',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ba',
				'name'     => __( 'Bosnia and Herzegovina', 'ware' ),
				'dialCode' => '387',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'bw',
				'name'     => __( 'Botswana', 'ware' ),
				'dialCode' => '267',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'br',
				'name'     => __( 'Brazil', 'ware' ),
				'dialCode' => '55',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'bn',
				'name'     => __( 'Brunei', 'ware' ),
				'dialCode' => '673',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'bg',
				'name'     => __( 'Bulgaria', 'ware' ),
				'dialCode' => '359',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'bf',
				'name'     => __( 'Burkina Faso', 'ware' ),
				'dialCode' => '226',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'bi',
				'name'     => __( 'Burundi', 'ware' ),
				'dialCode' => '257',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'cv',
				'name'     => __( 'Cabo Verde', 'ware' ),
				'dialCode' => '238',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'kh',
				'name'     => __( 'Cambodia', 'ware' ),
				'dialCode' => '855',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'cm',
				'name'     => __( 'Cameroon', 'ware' ),
				'dialCode' => '237',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ca',
				'name'     => __( 'Canada', 'ware' ),
				'dialCode' => '1',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'cf',
				'name'     => __( 'Central African Republic', 'ware' ),
				'dialCode' => '236',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'td',
				'name'     => __( 'Chad', 'ware' ),
				'dialCode' => '235',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'cl',
				'name'     => __( 'Chile', 'ware' ),
				'dialCode' => '56',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'cn',
				'name'     => __( 'China', 'ware' ),
				'dialCode' => '86',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'co',
				'name'     => __( 'Colombia', 'ware' ),
				'dialCode' => '57',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'km',
				'name'     => __( 'Comoros', 'ware' ),
				'dialCode' => '269',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'cd',
				'name'     => __( 'Congo, Democratic', 'ware' ),
				'dialCode' => '243',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'cg',
				'name'     => __( 'Congo', 'ware' ),
				'dialCode' => '242',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'cr',
				'name'     => __( 'Costa Rica', 'ware' ),
				'dialCode' => '506',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ci',
				'name'     => __( "Cote d'ivoire", 'ware' ),
				'dialCode' => '225',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'hr',
				'name'     => __( 'Croatia', 'ware' ),
				'dialCode' => '385',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'cu',
				'name'     => __( 'Cuba', 'ware' ),
				'dialCode' => '53',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'cy',
				'name'     => __( 'Cyprus', 'ware' ),
				'dialCode' => '357',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'cz',
				'name'     => __( 'Czech Republic', 'ware' ),
				'dialCode' => '420',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'dk',
				'name'     => __( 'Denmark', 'ware' ),
				'dialCode' => '45',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'dj',
				'name'     => __( 'Djibouti', 'ware' ),
				'dialCode' => '253',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'dm',
				'name'     => __( 'Dominica', 'ware' ),
				'dialCode' => '1767',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'do',
				'name'     => __( 'Dominican Republic', 'ware' ),
				'dialCode' => '1809',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ec',
				'name'     => __( 'Ecuador', 'ware' ),
				'dialCode' => '593',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'sv',
				'name'     => __( 'El Salvador', 'ware' ),
				'dialCode' => '503',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'gq',
				'name'     => __( 'Equatorial Guinea', 'ware' ),
				'dialCode' => '240',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'er',
				'name'     => __( 'Eritrea', 'ware' ),
				'dialCode' => '291',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ee',
				'name'     => __( 'Estonia', 'ware' ),
				'dialCode' => '372',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'sz',
				'name'     => __( 'Eswatini', 'ware' ),
				'dialCode' => '268',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'et',
				'name'     => __( 'Ethiopia', 'ware' ),
				'dialCode' => '251',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'fj',
				'name'     => __( 'Fiji', 'ware' ),
				'dialCode' => '679',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'fi',
				'name'     => __( 'Finland', 'ware' ),
				'dialCode' => '358',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'fr',
				'name'     => __( 'France', 'ware' ),
				'dialCode' => '33',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ga',
				'name'     => __( 'Gabon', 'ware' ),
				'dialCode' => '241',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'gm',
				'name'     => __( 'Gambia', 'ware' ),
				'dialCode' => '220',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ge',
				'name'     => __( 'Georgia', 'ware' ),
				'dialCode' => '995',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'de',
				'name'     => __( 'Germany', 'ware' ),
				'dialCode' => '49',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'gh',
				'name'     => __( 'Ghana', 'ware' ),
				'dialCode' => '233',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'gr',
				'name'     => __( 'Greece', 'ware' ),
				'dialCode' => '30',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'gd',
				'name'     => __( 'Grenada', 'ware' ),
				'dialCode' => '1473',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'gu',
				'name'     => __( 'Guam', 'ware' ),
				'dialCode' => '1671',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'gt',
				'name'     => __( 'Guatemala', 'ware' ),
				'dialCode' => '502',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'gn',
				'name'     => __( 'Guinea', 'ware' ),
				'dialCode' => '224',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'gw',
				'name'     => __( 'Guinea-Bissau', 'ware' ),
				'dialCode' => '245',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'gy',
				'name'     => __( 'Guyana', 'ware' ),
				'dialCode' => '592',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ht',
				'name'     => __( 'Haiti', 'ware' ),
				'dialCode' => '509',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'hn',
				'name'     => __( 'Honduras', 'ware' ),
				'dialCode' => '504',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'hk',
				'name'     => __( 'Hong Kong', 'ware' ),
				'dialCode' => '852',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'hu',
				'name'     => __( 'Hungary', 'ware' ),
				'dialCode' => '36',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'is',
				'name'     => __( 'Iceland', 'ware' ),
				'dialCode' => '354',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'in',
				'name'     => __( 'India', 'ware' ),
				'dialCode' => '91',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'id',
				'name'     => __( 'Indonesia', 'ware' ),
				'dialCode' => '62',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'ir',
				'name'     => __( 'Iran', 'ware' ),
				'dialCode' => '98',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'iq',
				'name'     => __( 'Iraq', 'ware' ),
				'dialCode' => '964',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'ie',
				'name'     => __( 'Ireland', 'ware' ),
				'dialCode' => '353',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'it',
				'name'     => __( 'Italy', 'ware' ),
				'dialCode' => '39',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'jm',
				'name'     => __( 'Jamaica', 'ware' ),
				'dialCode' => '1876',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'jp',
				'name'     => __( 'Japan', 'ware' ),
				'dialCode' => '81',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'jo',
				'name'     => __( 'Jordan', 'ware' ),
				'dialCode' => '962',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'kz',
				'name'     => __( 'Kazakhstan', 'ware' ),
				'dialCode' => '7',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'ke',
				'name'     => __( 'Kenya', 'ware' ),
				'dialCode' => '254',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ki',
				'name'     => __( 'Kiribati', 'ware' ),
				'dialCode' => '686',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'kp',
				'name'     => __( 'North Korea', 'ware' ),
				'dialCode' => '850',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'kr',
				'name'     => __( 'South Korea', 'ware' ),
				'dialCode' => '82',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'kw',
				'name'     => __( 'Kuwait', 'ware' ),
				'dialCode' => '965',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'kg',
				'name'     => __( 'Kyrgyzstan', 'ware' ),
				'dialCode' => '996',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'la',
				'name'     => __( "Lao People's Democratic Republic", 'ware' ),
				'dialCode' => '856',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'lv',
				'name'     => __( 'Latvia', 'ware' ),
				'dialCode' => '371',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'lb',
				'name'     => __( 'Lebanon', 'ware' ),
				'dialCode' => '961',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'ls',
				'name'     => __( 'Lesotho', 'ware' ),
				'dialCode' => '266',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'lr',
				'name'     => __( 'Liberia', 'ware' ),
				'dialCode' => '231',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ly',
				'name'     => __( 'Libya', 'ware' ),
				'dialCode' => '218',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'li',
				'name'     => __( 'Liechtenstein', 'ware' ),
				'dialCode' => '423',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'lt',
				'name'     => __( 'Lithuania', 'ware' ),
				'dialCode' => '370',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'lu',
				'name'     => __( 'Luxembourg', 'ware' ),
				'dialCode' => '352',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'mo',
				'name'     => __( 'Macao', 'ware' ),
				'dialCode' => '853',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'mg',
				'name'     => __( 'Madagascar', 'ware' ),
				'dialCode' => '261',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mw',
				'name'     => __( 'Malawi', 'ware' ),
				'dialCode' => '265',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'my',
				'name'     => __( 'Malaysia', 'ware' ),
				'dialCode' => '60',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'mv',
				'name'     => __( 'Maldives', 'ware' ),
				'dialCode' => '960',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'ml',
				'name'     => __( 'Mali', 'ware' ),
				'dialCode' => '223',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mt',
				'name'     => __( 'Malta', 'ware' ),
				'dialCode' => '356',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'mh',
				'name'     => __( 'Marshall Islands', 'ware' ),
				'dialCode' => '692',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'mr',
				'name'     => __( 'Mauritania', 'ware' ),
				'dialCode' => '222',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mu',
				'name'     => __( 'Mauritius', 'ware' ),
				'dialCode' => '230',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mx',
				'name'     => __( 'Mexico', 'ware' ),
				'dialCode' => '52',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'fm',
				'name'     => __( 'Micronesia', 'ware' ),
				'dialCode' => '691',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'md',
				'name'     => __( 'Moldova', 'ware' ),
				'dialCode' => '373',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'mc',
				'name'     => __( 'Monaco', 'ware' ),
				'dialCode' => '377',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'mn',
				'name'     => __( 'Mongolia', 'ware' ),
				'dialCode' => '976',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'me',
				'name'     => __( 'Montenegro', 'ware' ),
				'dialCode' => '382',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ma',
				'name'     => __( 'Morocco', 'ware' ),
				'dialCode' => '212',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mz',
				'name'     => __( 'Mozambique', 'ware' ),
				'dialCode' => '258',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'mm',
				'name'     => __( 'Myanmar', 'ware' ),
				'dialCode' => '95',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'na',
				'name'     => __( 'Namibia', 'ware' ),
				'dialCode' => '264',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'nr',
				'name'     => __( 'Nauru', 'ware' ),
				'dialCode' => '674',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'np',
				'name'     => __( 'Nepal', 'ware' ),
				'dialCode' => '977',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'nl',
				'name'     => __( 'Netherlands', 'ware' ),
				'dialCode' => '31',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'nz',
				'name'     => __( 'New Zealand', 'ware' ),
				'dialCode' => '64',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'ni',
				'name'     => __( 'Nicaragua', 'ware' ),
				'dialCode' => '505',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ne',
				'name'     => __( 'Niger', 'ware' ),
				'dialCode' => '227',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ng',
				'name'     => __( 'Nigeria', 'ware' ),
				'dialCode' => '234',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'nu',
				'name'     => __( 'Niue', 'ware' ),
				'dialCode' => '683',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'mk',
				'name'     => __( 'North Macedonia', 'ware' ),
				'dialCode' => '389',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'no',
				'name'     => __( 'Norway', 'ware' ),
				'dialCode' => '47',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'om',
				'name'     => __( 'Oman', 'ware' ),
				'dialCode' => '968',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'pk',
				'name'     => __( 'Pakistan', 'ware' ),
				'dialCode' => '92',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'pw',
				'name'     => __( 'Palau', 'ware' ),
				'dialCode' => '680',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'ps',
				'name'     => __( 'Palestine', 'ware' ),
				'dialCode' => '970',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'pa',
				'name'     => __( 'Panama', 'ware' ),
				'dialCode' => '507',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'pg',
				'name'     => __( 'Papua New Guinea', 'ware' ),
				'dialCode' => '675',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'py',
				'name'     => __( 'Paraguay', 'ware' ),
				'dialCode' => '595',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'pe',
				'name'     => __( 'Peru', 'ware' ),
				'dialCode' => '51',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ph',
				'name'     => __( 'Philippines', 'ware' ),
				'dialCode' => '63',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'pl',
				'name'     => __( 'Poland', 'ware' ),
				'dialCode' => '48',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'pt',
				'name'     => __( 'Portugal', 'ware' ),
				'dialCode' => '351',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'qa',
				'name'     => __( 'Qatar', 'ware' ),
				'dialCode' => '974',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'ro',
				'name'     => __( 'Romania', 'ware' ),
				'dialCode' => '40',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ru',
				'name'     => __( 'Russia', 'ware' ),
				'dialCode' => '7',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'rw',
				'name'     => __( 'Rwanda', 'ware' ),
				'dialCode' => '250',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'kn',
				'name'     => __( 'Saint Kitts and Nevis', 'ware' ),
				'dialCode' => '1869',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'lc',
				'name'     => __( 'Saint Lucia', 'ware' ),
				'dialCode' => '1758',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'vc',
				'name'     => __( 'Saint Vincent and the Grenadines', 'ware' ),
				'dialCode' => '1784',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'ws',
				'name'     => __( 'Samoa', 'ware' ),
				'dialCode' => '685',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'sm',
				'name'     => __( 'San Marino', 'ware' ),
				'dialCode' => '378',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'st',
				'name'     => __( 'Sao Tome and Principe', 'ware' ),
				'dialCode' => '239',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'sn',
				'name'     => __( 'Senegal', 'ware' ),
				'dialCode' => '221',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'rs',
				'name'     => __( 'Serbia', 'ware' ),
				'dialCode' => '381',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'sc',
				'name'     => __( 'Seychelles', 'ware' ),
				'dialCode' => '248',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'sl',
				'name'     => __( 'Sierra Leone', 'ware' ),
				'dialCode' => '232',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'sg',
				'name'     => __( 'Singapore', 'ware' ),
				'dialCode' => '65',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'sk',
				'name'     => __( 'Slovakia', 'ware' ),
				'dialCode' => '421',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'si',
				'name'     => __( 'Slovenia', 'ware' ),
				'dialCode' => '386',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'sb',
				'name'     => __( 'Solomon Islands', 'ware' ),
				'dialCode' => '677',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'so',
				'name'     => __( 'Somalia', 'ware' ),
				'dialCode' => '252',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'za',
				'name'     => __( 'South Africa', 'ware' ),
				'dialCode' => '27',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ss',
				'name'     => __( 'South Sudan', 'ware' ),
				'dialCode' => '211',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'es',
				'name'     => __( 'Spain', 'ware' ),
				'dialCode' => '34',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'lk',
				'name'     => __( 'Sri Lanka', 'ware' ),
				'dialCode' => '94',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'sd',
				'name'     => __( 'Sudan', 'ware' ),
				'dialCode' => '249',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'sr',
				'name'     => __( 'Suriname', 'ware' ),
				'dialCode' => '597',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'se',
				'name'     => __( 'Sweden', 'ware' ),
				'dialCode' => '46',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ch',
				'name'     => __( 'Switzerland', 'ware' ),
				'dialCode' => '41',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'sy',
				'name'     => __( 'Syrian Arab Republic', 'ware' ),
				'dialCode' => '963',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'tw',
				'name'     => __( 'Taiwan', 'ware' ),
				'dialCode' => '886',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'tj',
				'name'     => __( 'Tajikistan', 'ware' ),
				'dialCode' => '992',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'tz',
				'name'     => __( 'Tanzania', 'ware' ),
				'dialCode' => '255',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'th',
				'name'     => __( 'Thailand', 'ware' ),
				'dialCode' => '66',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'tl',
				'name'     => __( 'Timor-Leste', 'ware' ),
				'dialCode' => '670',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'tg',
				'name'     => __( 'Togo', 'ware' ),
				'dialCode' => '228',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'to',
				'name'     => __( 'Tonga', 'ware' ),
				'dialCode' => '676',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'tt',
				'name'     => __( 'Trinidad and Tobago', 'ware' ),
				'dialCode' => '1868',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'tn',
				'name'     => __( 'Tunisia', 'ware' ),
				'dialCode' => '216',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'tr',
				'name'     => __( 'Turkey', 'ware' ),
				'dialCode' => '90',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'tm',
				'name'     => __( 'Turkmenistan', 'ware' ),
				'dialCode' => '993',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'tv',
				'name'     => __( 'Tuvalu', 'ware' ),
				'dialCode' => '688',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 'ug',
				'name'     => __( 'Uganda', 'ware' ),
				'dialCode' => '256',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'ua',
				'name'     => __( 'Ukraine', 'ware' ),
				'dialCode' => '380',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'ae',
				'name'     => __( 'United Arab Emirates', 'ware' ),
				'dialCode' => '971',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'gb',
				'name'     => __( 'United Kingdom', 'ware' ),
				'dialCode' => '44',
				'region'   => __( 'Europe', 'ware' ),
			),
			array(
				'iso2'     => 'us',
				'name'     => __( 'United States', 'ware' ),
				'dialCode' => '1',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'uy',
				'name'     => __( 'Uruguay', 'ware' ),
				'dialCode' => '598',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'uz',
				'name'     => __( 'Uzbekistan', 'ware' ),
				'dialCode' => '998',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'vu',
				'name'     => __( 'Vanuatu', 'ware' ),
				'dialCode' => '678',
				'region'   => __( 'Oceania', 'ware' ),
			),
			array(
				'iso2'     => 've',
				'name'     => __( 'Venezuela', 'ware' ),
				'dialCode' => '58',
				'region'   => __( 'Americas', 'ware' ),
			),
			array(
				'iso2'     => 'vn',
				'name'     => __( 'Vietnam', 'ware' ),
				'dialCode' => '84',
				'region'   => __( 'Asia', 'ware' ),
			),
			array(
				'iso2'     => 'ye',
				'name'     => __( 'Yemen', 'ware' ),
				'dialCode' => '967',
				'region'   => __( 'Middle East', 'ware' ),
			),
			array(
				'iso2'     => 'zm',
				'name'     => __( 'Zambia', 'ware' ),
				'dialCode' => '260',
				'region'   => __( 'Africa', 'ware' ),
			),
			array(
				'iso2'     => 'zw',
				'name'     => __( 'Zimbabwe', 'ware' ),
				'dialCode' => '263',
				'region'   => __( 'Africa', 'ware' ),
			),
		);
	}

	/**
	 * Group countries by their regions.
	 *
	 * @param array $all_countries The list of all countries.
	 * @return array An array of countries grouped by region.
	 */
	private function group_countries_by_region( $all_countries ) {
		$regions = array();
		foreach ( $all_countries as $country ) {
			$regions[ $country['region'] ][] = $country;
		}
		return $regions;
	}
}

// Instantiate the ArriveWoo_Countrycode class.
new ArriveWoo_Countrycode();
?>
