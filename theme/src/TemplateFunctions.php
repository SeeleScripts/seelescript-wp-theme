<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package SeeleScript
 */

namespace SeeleScript;

/**
 * Class TemplateFunctions
 *
 * Hook-registered static methods and utility helpers used across the theme.
 */
class TemplateFunctions {

	/**
	 * Wire up all WordPress action / filter hooks.
	 *
	 * Call once from functions.php: SeeleScript\TemplateFunctions::init();
	 */
	public static function init(): void {
		add_action( 'wp_head', array( static::class, 'pingback_header' ) );
		add_filter( 'comment_form_defaults', array( static::class, 'comment_form_defaults' ) );
		add_filter( 'get_the_archive_title', array( static::class, 'get_the_archive_title' ) );
		add_filter( 'excerpt_more', array( static::class, 'continue_reading_link' ) );
		add_filter( 'the_content_more_link', array( static::class, 'continue_reading_link' ) );
	}

	// -------------------------------------------------------------------------
	// WordPress action / filter callbacks
	// -------------------------------------------------------------------------

	/**
	 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
	 */
	public static function pingback_header(): void {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}

	/**
	 * Changes comment form default fields.
	 *
	 * @param array $defaults The default comment form arguments.
	 * @return array Returns the modified fields.
	 */
	public static function comment_form_defaults( array $defaults ): array {
		$comment_field = $defaults['comment_field'];

		// Adjust height of comment form.
		$defaults['comment_field'] = preg_replace( '/rows="\d+"/', 'rows="5"', $comment_field );

		return $defaults;
	}

	/**
	 * Filters the default archive titles.
	 *
	 * @return string
	 */
	public static function get_the_archive_title(): string {
		if ( is_category() ) {
			$title = __( 'Category Archives: ', 'seelescript' ) . '<span>' . single_term_title( '', false ) . '</span>';
		} elseif ( is_tag() ) {
			$title = __( 'Tag Archives: ', 'seelescript' ) . '<span>' . single_term_title( '', false ) . '</span>';
		} elseif ( is_author() ) {
			$title = __( 'Author Archives: ', 'seelescript' ) . '<span>' . get_the_author_meta( 'display_name' ) . '</span>';
		} elseif ( is_year() ) {
			$title = __( 'Yearly Archives: ', 'seelescript' ) . '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'seelescript' ) ) . '</span>';
		} elseif ( is_month() ) {
			$title = __( 'Monthly Archives: ', 'seelescript' ) . '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'seelescript' ) ) . '</span>';
		} elseif ( is_day() ) {
			$title = __( 'Daily Archives: ', 'seelescript' ) . '<span>' . get_the_date() . '</span>';
		} elseif ( is_post_type_archive() ) {
			$cpt   = get_post_type_object( get_queried_object()->name );
			$title = sprintf(
				/* translators: %s: Post type singular name */
				esc_html__( '%s Archives', 'seelescript' ),
				$cpt->labels->singular_name
			);
		} elseif ( is_tax() ) {
			$tax   = get_taxonomy( get_queried_object()->taxonomy );
			$title = sprintf(
				/* translators: %s: Taxonomy singular name */
				esc_html__( '%s Archives', 'seelescript' ),
				$tax->labels->singular_name
			);
		} else {
			$title = __( 'Archives:', 'seelescript' );
		}

		return $title;
	}

	/**
	 * Create the continue reading link.
	 *
	 * @param string $more_string The string shown within the more link.
	 * @return string
	 */
	public static function continue_reading_link( string $more_string ): string {
		if ( ! is_admin() ) {
			$continue_reading = sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s', 'seelescript' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="sr-only">"', '"</span>', false )
			);

			$more_string = '<a href="' . esc_url( get_permalink() ) . '">' . $continue_reading . '</a>';
		}

		return $more_string;
	}

	// -------------------------------------------------------------------------
	// Utility helpers (called from other theme classes / template files)
	// -------------------------------------------------------------------------

	/**
	 * Determines whether the post thumbnail can be displayed.
	 *
	 * @return bool
	 */
	public static function can_show_post_thumbnail(): bool {
		return (bool) apply_filters(
			'seelescript_can_show_post_thumbnail',
			! post_password_required() && ! is_attachment() && has_post_thumbnail()
		);
	}

	/**
	 * Returns the size for avatars used in the theme.
	 *
	 * @return int
	 */
	public static function get_avatar_size(): int {
		return 60;
	}

	/**
	 * Outputs a comment in the HTML5 format.
	 *
	 * This function overrides the default WordPress comment output in HTML5
	 * format, adding the required class for Tailwind Typography. Based on the
	 * `html5_comment()` function from WordPress core.
	 *
	 * @param \WP_Comment $comment Comment to display.
	 * @param array       $args    An array of arguments.
	 * @param int         $depth   Depth of the current comment.
	 */
	public static function html5_comment( \WP_Comment $comment, array $args, int $depth ): void {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

		$commenter          = wp_get_current_commenter();
		$show_pending_links = ! empty( $commenter['comment_author'] );

		if ( $commenter['comment_author_email'] ) {
			$moderation_note = __( 'Your comment is awaiting moderation.', 'seelescript' );
		} else {
			$moderation_note = __( 'Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'seelescript' );
		}
		?>
		<<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment->has_children ? 'parent' : '', $comment ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php
						if ( 0 !== $args['avatar_size'] ) {
							echo get_avatar( $comment, $args['avatar_size'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						<?php
						$comment_author = get_comment_author_link( $comment );

						if ( '0' === $comment->comment_approved && ! $show_pending_links ) {
							$comment_author = get_comment_author( $comment );
						}

						printf(
							/* translators: %s: Comment author link. */
							wp_kses_post( __( '%s <span class="says">says:</span>', 'seelescript' ) ),
							sprintf( '<b class="fn">%s</b>', wp_kses_post( $comment_author ) )
						);
						?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
						<?php
						printf(
							'<a href="%s"><time datetime="%s">%s</time></a>',
							esc_url( get_comment_link( $comment, $args ) ),
							esc_attr( get_comment_time( 'c' ) ),
							esc_html(
								sprintf(
									/* translators: 1: Comment date, 2: Comment time. */
									__( '%1$s at %2$s', 'seelescript' ),
									get_comment_date( '', $comment ),
									get_comment_time()
								)
							)
						);

						edit_comment_link( __( 'Edit', 'seelescript' ), ' <span class="edit-link">', '</span>' );
						?>
					</div><!-- .comment-metadata -->

					<?php if ( '0' === $comment->comment_approved ) : ?>
					<em class="comment-awaiting-moderation"><?php echo esc_html( $moderation_note ); ?></em>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div <?php TemplateTags::content_class( 'comment-content' ); ?>>
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<?php
				if ( '1' === $comment->comment_approved || $show_pending_links ) {
					comment_reply_link(
						array_merge(
							$args,
							array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<div class="reply">',
								'after'     => '</div>',
							)
						)
					);
				}
				?>
			</article><!-- .comment-body -->
		<?php
	}
}
