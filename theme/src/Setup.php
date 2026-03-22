<?php
/**
 * Theme setup and asset enqueueing.
 *
 * @package SeeleScript
 */

namespace SeeleScript;

/**
 * Class Setup
 *
 * Bootstraps the theme: registers support flags, menus, widgets,
 * and enqueues all front-end and editor assets.
 */
class Setup {

	/**
	 * Wire up all WordPress action / filter hooks.
	 *
	 * Call once from functions.php: SeeleScript\Setup::init();
	 */
	public static function init(): void {
		add_action( 'after_setup_theme', array( static::class, 'theme_setup' ) );
		add_action( 'widgets_init', array( static::class, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts', array( static::class, 'enqueue_scripts' ) );
		add_action( 'enqueue_block_assets', array( static::class, 'enqueue_block_editor_script' ) );
		add_filter( 'tiny_mce_before_init', array( static::class, 'tinymce_add_class' ) );
		add_filter( 'register_block_type_args', array( static::class, 'modify_heading_levels' ), 10, 2 );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Hooked into `after_setup_theme` (runs before `init`).
	 */
	public static function theme_setup(): void {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 */
		load_theme_textdomain( 'seelescript', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'menu-1' => __( 'Primary', 'seelescript' ),
				'menu-2' => __( 'Footer Menu', 'seelescript' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Remove support for block templates.
		remove_theme_support( 'block-templates' );
	}

	/**
	 * Register widget area.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	public static function widgets_init(): void {
		register_sidebar(
			array(
				'name'          => __( 'Footer', 'seelescript' ),
				'id'            => 'sidebar-1',
				'description'   => __( 'Add widgets here to appear in your footer.', 'seelescript' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}

	/**
	 * Enqueue front-end scripts and styles.
	 */
	public static function enqueue_scripts(): void {
		wp_enqueue_style(
			'seelescript-style',
			get_stylesheet_uri(),
			array(),
			SEELESCRIPT_VERSION
		);

		wp_enqueue_script(
			'seelescript-script',
			get_template_directory_uri() . '/js/script.min.js',
			array(),
			SEELESCRIPT_VERSION,
			true
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue the block editor script.
	 */
	public static function enqueue_block_editor_script(): void {
		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if (
			$current_screen &&
			$current_screen->is_block_editor() &&
			'widgets' !== $current_screen->id
		) {
			wp_enqueue_script(
				'seelescript-editor',
				get_template_directory_uri() . '/js/block-editor.min.js',
				array(
					'wp-blocks',
					'wp-edit-post',
				),
				SEELESCRIPT_VERSION,
				true
			);

			wp_add_inline_script(
				'seelescript-editor',
				"tailwindTypographyClasses = '" . esc_attr( SEELESCRIPT_TYPOGRAPHY_CLASSES ) . "'.split(' ');",
				'before'
			);
		}
	}

	/**
	 * Add the Tailwind Typography classes to TinyMCE.
	 *
	 * @param array $settings TinyMCE settings.
	 * @return array
	 */
	public static function tinymce_add_class( array $settings ): array {
		$settings['body_class'] = SEELESCRIPT_TYPOGRAPHY_CLASSES;
		return $settings;
	}

	/**
	 * Limit the block editor to heading levels supported by Tailwind Typography.
	 *
	 * @param array  $args       Array of arguments for registering a block type.
	 * @param string $block_type Block type name including namespace.
	 * @return array
	 */
	public static function modify_heading_levels( array $args, string $block_type ): array {
		if ( 'core/heading' !== $block_type ) {
			return $args;
		}

		// Remove <h1>, <h5> and <h6>.
		$args['attributes']['levelOptions']['default'] = array( 2, 3, 4 );

		return $args;
	}
}
