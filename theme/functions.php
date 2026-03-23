<?php
/**
 * SeeleScript functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package SeeleScript
 */

if ( ! defined( 'SEELESCRIPT_VERSION' ) ) {
	/*
	 * Set the theme's version number.
	 *
	 * This is used primarily for cache busting. If you use `npm run bundle`
	 * to create your production build, the value below will be replaced in the
	 * generated zip file with a timestamp, converted to base 36.
	 */
	define( 'SEELESCRIPT_VERSION', '0.1.0' );
}

if ( ! defined( 'SEELESCRIPT_TYPOGRAPHY_CLASSES' ) ) {
	/*
	 * Set Tailwind Typography classes for the front end, block editor and
	 * classic editor using the constant below.
	 *
	 * For the front end, these classes are added by the `TemplateTags::content_class`
	 * method. You will see that method used everywhere an `entry-content`
	 * or `page-content` class has been added to a wrapper element.
	 *
	 * For the block editor, these classes are converted to a JavaScript array
	 * and then used by the `./javascript/block-editor.js` file, which adds
	 * them to the appropriate elements in the block editor (and adds them
	 * again when they're removed.)
	 *
	 * For the classic editor (and anything using TinyMCE, like Advanced Custom
	 * Fields), these classes are added to TinyMCE's body class when it
	 * initializes.
	 */
	define(
		'SEELESCRIPT_TYPOGRAPHY_CLASSES',
		'prose prose-neutral max-w-none prose-a:text-primary'
	);
}

/**
 * Load OOP theme classes.
 */
require get_template_directory() . '/src/Setup.php';
require get_template_directory() . '/src/TemplateTags.php';
require get_template_directory() . '/src/TemplateFunctions.php';
require get_template_directory() . '/src/Admin/ThemeSettings.php';
require get_template_directory() . '/src/Admin/HeaderSettings.php';
require get_template_directory() . '/src/Admin/FooterSettings.php';

/**
 * Bootstrap theme setup and WordPress hooks.
 */
SeeleScript\Setup::init();
SeeleScript\TemplateFunctions::init();
SeeleScript\Admin\ThemeSettings::init();
