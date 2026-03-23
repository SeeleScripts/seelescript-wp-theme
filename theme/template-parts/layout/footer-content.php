<?php
use SeeleScript\TemplateTags;

$footer_logo      = TemplateTags::footer_logo_url();
$footer_copyright = TemplateTags::footer_copyright();
$social_icons     = TemplateTags::footer_social_icons();
?>

<footer id="colophon" class="footer footer-center p-10 bg-base-200 text-base-content rounded">
	
	<?php if ( has_nav_menu( 'menu-2' ) ) : ?>
		<nav class="grid grid-flow-col gap-4">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-2',
					'container'      => false,
					'items_wrap'     => '%3$s', // No wrapping <ul> so we can style with daisyUI classes if needed
					'depth'          => 1,
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<?php if ( ! empty( $social_icons ) ) : ?>
		<nav>
			<div class="grid grid-flow-col gap-4">
				<?php foreach ( $social_icons as $item ) : ?>
					<a href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<i class="<?php echo esc_attr( $item['icon'] ); ?> text-2xl"></i>
					</a>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>

	<aside>
		<?php if ( $footer_logo ) : ?>
			<img src="<?php echo esc_url( $footer_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="max-h-16 mb-4">
		<?php endif; ?>

		<p>
			<?php if ( $footer_copyright ) : ?>
				<?php echo esc_html( $footer_copyright ); ?>
			<?php else : ?>
				&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'seelescript' ); ?>
			<?php endif; ?>
		</p>
	</aside>

	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div class="footer-sidebar mt-4">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	<?php endif; ?>

</footer><!-- #colophon -->
