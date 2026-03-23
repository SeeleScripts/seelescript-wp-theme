<?php
/**
 * Custom template tag helpers for the SeeleScript theme.
 *
 * Eventually, some functionality here could be replaced by core features.
 *
 * @package SeeleScript
 */

namespace SeeleScript;

/**
 * Class TemplateTags
 *
 * Static helper methods used directly inside template files.
 */
class TemplateTags {

	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	public static function posted_on(): void {
		$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}

	/**
	 * Prints HTML with meta information about the post author.
	 */
	public static function posted_by(): void {
		printf(
			/* translators: 1: Posted by label (screen-reader only). 2: Author link. 3: Post author name. */
			'<span class="sr-only">%1$s</span><span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span>',
			esc_html__( 'Posted by', 'seelescript' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}

	/**
	 * Prints HTML with the comment count for the current post.
	 */
	public static function comment_count(): void {
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			/* translators: %s: Name of current post. Only visible to screen readers. */
			comments_popup_link( sprintf( __( 'Leave a comment<span class="sr-only"> on %s</span>', 'seelescript' ), get_the_title() ) );
		}
	}

	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * Used in the entry header.
	 */
	public static function entry_meta(): void {
		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			static::posted_by();

			// Posted on.
			static::posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list( __( ', ', 'seelescript' ) );
			if ( $categories_list ) {
				printf(
					/* translators: 1: Posted in label (screen-reader only). 2: List of categories. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Posted in', 'seelescript' ),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list( '', __( ', ', 'seelescript' ) );
			if ( $tags_list ) {
				printf(
					/* translators: 1: Tags label (screen-reader only). 2: List of tags. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Tags:', 'seelescript' ),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Comment count.
		if ( ! is_singular() ) {
			static::comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'seelescript' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}

	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * Used in the entry footer.
	 */
	public static function entry_footer(): void {
		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			static::posted_by();

			// Posted on.
			static::posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list( __( ', ', 'seelescript' ) );
			if ( $categories_list ) {
				printf(
					/* translators: 1: Posted in label (screen-reader only). 2: List of categories. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Posted in', 'seelescript' ),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list( '', __( ', ', 'seelescript' ) );
			if ( $tags_list ) {
				printf(
					/* translators: 1: Tags label (screen-reader only). 2: List of tags. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Tags:', 'seelescript' ),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Comment count.
		if ( ! is_singular() ) {
			static::comment_count();
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'seelescript' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}

	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element except when viewing a single post.
	 */
	public static function post_thumbnail(): void {
		if ( ! TemplateFunctions::can_show_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<figure>
				<?php the_post_thumbnail(); ?>
			</figure><!-- .post-thumbnail -->

			<?php
		else :
			?>

			<figure>
				<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php the_post_thumbnail(); ?>
				</a>
			</figure>

			<?php
		endif; // End is_singular().
	}

	/**
	 * Returns the HTML markup to generate a user avatar.
	 *
	 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                           user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @return string
	 */
	public static function get_user_avatar_markup( mixed $id_or_email = null ): string {
		if ( ! isset( $id_or_email ) ) {
			$id_or_email = get_current_user_id();
		}

		return sprintf( '<div class="vcard">%s</div>', get_avatar( $id_or_email, TemplateFunctions::get_avatar_size() ) );
	}

	/**
	 * Displays a list of avatars involved in a discussion for a given post.
	 *
	 * @param array $comment_authors Comment authors to list as avatars.
	 */
	public static function discussion_avatars_list( array $comment_authors ): void {
		if ( empty( $comment_authors ) ) {
			return;
		}
		echo '<ol>', "\n";
		foreach ( $comment_authors as $id_or_email ) {
			printf(
				"<li>%s</li>\n",
				static::get_user_avatar_markup( $id_or_email ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		echo '</ol>', "\n";
	}

	/**
	 * Wraps `the_posts_pagination` for use throughout the theme.
	 */
	public static function the_posts_navigation(): void {
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Newer posts', 'seelescript' ),
				'next_text' => __( 'Older posts', 'seelescript' ),
			)
		);
	}

	/**
	 * Displays the class names for the post content wrapper.
	 *
	 * Adds Tailwind Typography modifier classes (defined via the
	 * SEELESCRIPT_TYPOGRAPHY_CLASSES constant) to the supplied class list.
	 *
	 * @param string|string[] $classes Space-separated string or array of class names.
	 */
	public static function content_class( string|array $classes = '' ): void {
		$all_classes = array( $classes, SEELESCRIPT_TYPOGRAPHY_CLASSES );

		foreach ( $all_classes as &$class_groups ) {
			if ( ! empty( $class_groups ) ) {
				if ( ! is_array( $class_groups ) ) {
					$class_groups = preg_split( '#\s+#', $class_groups );
				}
			} else {
				// Ensure we always coerce class to being an array.
				$class_groups = array();
			}
		}

		$combined_classes = array_merge( $all_classes[0], $all_classes[1] );
		$combined_classes = array_map( 'esc_attr', $combined_classes );

		// Separates class names with a single space, preparing them for the content wrapper.
		echo 'class="' . esc_attr( implode( ' ', $combined_classes ) ) . '"';
	}

	// =========================================================================
	// Theme Settings getters
	// =========================================================================

	/**
	 * Returns the header logo URL saved in Theme Settings.
	 *
	 * @return string URL string, or empty string if not set.
	 */
	public static function header_logo_url(): string {
		$val = get_option( 'seelescript_header_logo', '' );
		return is_string( $val ) ? $val : '';
	}

	/**
	 * Returns the footer logo URL saved in Theme Settings.
	 *
	 * @return string URL string, or empty string if not set.
	 */
	public static function footer_logo_url(): string {
		$val = get_option( 'seelescript_footer_logo', '' );
		return is_string( $val ) ? $val : '';
	}

	/**
	 * Returns the footer copyright text saved in Theme Settings.
	 *
	 * @return string Plain text, or empty string if not set.
	 */
	public static function footer_copyright(): string {
		$val = get_option( 'seelescript_footer_copyright', '' );
		return is_string( $val ) ? $val : '';
	}

	/**
	 * Returns the footer social icons as an indexed array.
	 *
	 * Each entry is an associative array with keys 'icon' and 'url'.
	 *
	 * @return array<int, array{icon: string, url: string}>
	 */
	public static function footer_social_icons(): array {
		$raw     = get_option( 'seelescript_footer_social_icons', '[]' );
		$raw     = is_string( $raw ) ? $raw : '[]';
		$decoded = json_decode( $raw, true );
		return is_array( $decoded ) ? $decoded : array();
	}
}
