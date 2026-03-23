<?php
/**
 * Theme Settings — admin menu & settings registration.
 *
 * @package SeeleScript
 */

namespace SeeleScript\Admin;

/**
 * Class ThemeSettings
 *
 * Registers the "Theme Settings" top-level admin menu, its submenus,
 * all WordPress Settings API option groups, and the admin-only assets
 * (wp_enqueue_media + the custom JS for image upload / repeater).
 */
class ThemeSettings {

	/**
	 * Option name: header logo URL.
	 */
	const OPTION_HEADER_LOGO = 'seelescript_header_logo';

	/**
	 * Option name: footer logo URL.
	 */
	const OPTION_FOOTER_LOGO = 'seelescript_footer_logo';

	/**
	 * Option name: footer copyright text.
	 */
	const OPTION_FOOTER_COPYRIGHT = 'seelescript_footer_copyright';

	/**
	 * Option name: footer social icons (JSON-encoded array).
	 */
	const OPTION_FOOTER_SOCIAL = 'seelescript_footer_social_icons';

	/**
	 * Settings group used by header settings page.
	 */
	const GROUP_HEADER = 'seelescript_header_settings';

	/**
	 * Settings group used by footer settings page.
	 */
	const GROUP_FOOTER = 'seelescript_footer_settings';

	/**
	 * Admin page slug for the theme settings page.
	 */
	const SLUG_SETTINGS = 'seelescript-theme-settings';

	/**
	 * Wire up all WordPress hooks.
	 *
	 * Call once from functions.php: SeeleScript\Admin\ThemeSettings::init();
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( static::class, 'register_menus' ) );
		add_action( 'admin_init', array( static::class, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( static::class, 'enqueue_admin_assets' ) );
	}

	// -------------------------------------------------------------------------
	// Menu registration
	// -------------------------------------------------------------------------

	/**
	 * Register the top-level menu.
	 */
	public static function register_menus(): void {
		// Top-level menu page.
		add_menu_page(
			__( 'Theme Settings', 'seelescript' ),
			__( 'Theme Settings', 'seelescript' ),
			'manage_options',
			self::SLUG_SETTINGS,
			array( static::class, 'render_page' ),
			'dashicons-admin-appearance',
			60
		);
	}

	/**
	 * Render the tabbed Theme Settings page.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'seelescript' ) );
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'header';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Theme Settings', 'seelescript' ); ?></h1>
			<?php settings_errors(); ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr( self::SLUG_SETTINGS ); ?>&tab=header" class="nav-tab <?php echo $active_tab === 'header' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Header Settings', 'seelescript' ); ?>
				</a>
				<a href="?page=<?php echo esc_attr( self::SLUG_SETTINGS ); ?>&tab=footer" class="nav-tab <?php echo $active_tab === 'footer' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Footer Settings', 'seelescript' ); ?>
				</a>
			</h2>

			<div class="postbox" style="margin-top: 20px; padding: 20px;">
				<form method="post" action="options.php">
					<?php
					if ( $active_tab === 'header' ) {
						settings_fields( self::GROUP_HEADER );
						do_settings_sections( self::SLUG_SETTINGS . '_header' );
					} else {
						settings_fields( self::GROUP_FOOTER );
						do_settings_sections( self::SLUG_SETTINGS . '_footer' );
					}
					submit_button();
					?>
				</form>
			</div>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Settings registration
	// -------------------------------------------------------------------------

	/**
	 * Register all options with their sanitize callbacks.
	 */
	public static function register_settings(): void {
		// --- Header group ---
		register_setting(
			self::GROUP_HEADER,
			self::OPTION_HEADER_LOGO,
			array(
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);

		add_settings_section(
			'seelescript_header_main',
			__( 'Header Options', 'seelescript' ),
			'__return_false',
			self::SLUG_SETTINGS . '_header'
		);

		add_settings_field(
			self::OPTION_HEADER_LOGO,
			__( 'Site Logo', 'seelescript' ),
			array( HeaderSettings::class, 'render_logo_field' ),
			self::SLUG_SETTINGS . '_header',
			'seelescript_header_main'
		);

		// --- Footer group ---
		register_setting(
			self::GROUP_FOOTER,
			self::OPTION_FOOTER_LOGO,
			array(
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);

		register_setting(
			self::GROUP_FOOTER,
			self::OPTION_FOOTER_COPYRIGHT,
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			self::GROUP_FOOTER,
			self::OPTION_FOOTER_SOCIAL,
			array(
				'sanitize_callback' => array( static::class, 'sanitize_social_icons' ),
				'default'           => '[]',
			)
		);

		add_settings_section(
			'seelescript_footer_main',
			__( 'Footer Options', 'seelescript' ),
			'__return_false',
			self::SLUG_SETTINGS . '_footer'
		);

		add_settings_field(
			self::OPTION_FOOTER_LOGO,
			__( 'Footer Logo', 'seelescript' ),
			array( FooterSettings::class, 'render_logo_field' ),
			self::SLUG_SETTINGS . '_footer',
			'seelescript_footer_main'
		);

		add_settings_field(
			self::OPTION_FOOTER_COPYRIGHT,
			__( 'Copyright Text', 'seelescript' ),
			array( FooterSettings::class, 'render_copyright_field' ),
			self::SLUG_SETTINGS . '_footer',
			'seelescript_footer_main'
		);

		add_settings_field(
			self::OPTION_FOOTER_SOCIAL,
			__( 'Social Icons', 'seelescript' ),
			array( FooterSettings::class, 'render_social_field' ),
			self::SLUG_SETTINGS . '_footer',
			'seelescript_footer_main'
		);
	}

	// -------------------------------------------------------------------------
	// Asset enqueueing
	// -------------------------------------------------------------------------

	/**
	 * Enqueue wp_enqueue_media and the admin JS only on our settings pages.
	 *
	 * @param string $hook The current admin page hook suffix.
	 */
	public static function enqueue_admin_assets( string $hook ): void {
		$our_pages = array(
			'toplevel_page_' . self::SLUG_SETTINGS,
		);

		if ( ! in_array( $hook, $our_pages, true ) ) {
			return;
		}

		// Required for the media uploader.
		wp_enqueue_media();

		wp_enqueue_script(
			'seelescript-admin-settings',
			get_template_directory_uri() . '/js/admin-theme-settings.js',
			array( 'jquery' ),
			SEELESCRIPT_VERSION,
			true
		);
	}

	// -------------------------------------------------------------------------
	// Sanitize callback
	// -------------------------------------------------------------------------

	/**
	 * Sanitize the social icons JSON payload before storing.
	 *
	 * Expects a JSON string like: [{"icon":"fab fa-twitter","url":"https://…"},…]
	 *
	 * @param string $raw Raw input from the settings form.
	 * @return string Sanitized JSON string.
	 */
	public static function sanitize_social_icons( string $raw ): string {
		$decoded = json_decode( wp_unslash( $raw ), true );

		if ( ! is_array( $decoded ) ) {
			return '[]';
		}

		$clean = array();
		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$clean[] = array(
				'icon' => sanitize_text_field( $item['icon'] ?? '' ),
				'url'  => esc_url_raw( $item['url'] ?? '' ),
			);
		}

		return wp_json_encode( $clean );
	}
}
