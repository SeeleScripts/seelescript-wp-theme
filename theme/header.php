<?php
/**
 * The header for our theme
 *
 * This is the template that displays the `<head>` element and everything up
 * until the site header is closed.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package SeeleScript
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a href="#content" class="sr-only"><?php esc_html_e( 'Skip to content', 'seelescript' ); ?></a>

<?php get_template_part( 'template-parts/layout/header', 'content' ); ?>
