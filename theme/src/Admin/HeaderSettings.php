<?php
/**
 * Header Settings — admin page renderer and field callbacks.
 *
 * @package SeeleScript
 */

namespace SeeleScript\Admin;

/**
 * Class HeaderSettings
 *
 * Renders the Header Settings admin page and its field callbacks.
 */
class HeaderSettings {

	/**
	 * Render the full Header Settings admin page.
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'seelescript' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Header Settings', 'seelescript' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( ThemeSettings::GROUP_HEADER );
				do_settings_sections( ThemeSettings::SLUG_HEADER );
				submit_button( __( 'Save Header Settings', 'seelescript' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the Site Logo image-upload field.
	 */
	public static function render_logo_field(): void {
		$logo_url = get_option( ThemeSettings::OPTION_HEADER_LOGO, '' );
		$logo_url = is_string( $logo_url ) ? $logo_url : '';
		$field_id = 'seelescript-header-logo';
		?>
		<div class="seelescript-image-upload" data-input-id="<?php echo esc_attr( $field_id ); ?>">
			<div class="seelescript-image-preview" style="margin-bottom:10px;">
				<?php if ( $logo_url ) : ?>
					<img
						src="<?php echo esc_url( $logo_url ); ?>"
						alt="<?php esc_attr_e( 'Header Logo Preview', 'seelescript' ); ?>"
						style="max-width:300px;max-height:150px;display:block;"
					>
				<?php endif; ?>
			</div>
			<input
				type="hidden"
				id="<?php echo esc_attr( $field_id ); ?>"
				name="<?php echo esc_attr( ThemeSettings::OPTION_HEADER_LOGO ); ?>"
				value="<?php echo esc_url( $logo_url ); ?>"
			>
			<button
				type="button"
				class="button seelescript-upload-btn"
				data-title="<?php esc_attr_e( 'Select Site Logo', 'seelescript' ); ?>"
				data-button-text="<?php esc_attr_e( 'Use this image', 'seelescript' ); ?>"
			>
				<?php esc_html_e( 'Select Image', 'seelescript' ); ?>
			</button>
			<?php if ( $logo_url ) : ?>
				<button type="button" class="button seelescript-remove-btn" style="margin-left:5px;">
					<?php esc_html_e( 'Remove', 'seelescript' ); ?>
				</button>
			<?php endif; ?>
			<p class="description"><?php esc_html_e( 'Upload or select the site logo displayed in the header.', 'seelescript' ); ?></p>
		</div>
		<?php
	}
}
