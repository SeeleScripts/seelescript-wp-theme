<?php
/**
 * Footer Settings — admin page renderer and field callbacks.
 *
 * @package SeeleScript
 */

namespace SeeleScript\Admin;

/**
 * Class FooterSettings
 *
 * Renders the Footer Settings admin page and its field callbacks.
 */
class FooterSettings {


	/**
	 * Render the Footer Logo image-upload field.
	 */
	public static function render_logo_field(): void {
		$logo_url = get_option( ThemeSettings::OPTION_FOOTER_LOGO, '' );
		$logo_url = is_string( $logo_url ) ? $logo_url : '';
		$field_id = 'seelescript-footer-logo';
		?>
		<div class="seelescript-image-upload" data-input-id="<?php echo esc_attr( $field_id ); ?>">
			<div class="seelescript-image-preview" style="margin-bottom:10px;">
				<?php if ( $logo_url ) : ?>
					<img
						src="<?php echo esc_url( $logo_url ); ?>"
						alt="<?php esc_attr_e( 'Footer Logo Preview', 'seelescript' ); ?>"
						style="max-width:300px;max-height:150px;display:block;"
					>
				<?php endif; ?>
			</div>
			<input
				type="hidden"
				id="<?php echo esc_attr( $field_id ); ?>"
				name="<?php echo esc_attr( ThemeSettings::OPTION_FOOTER_LOGO ); ?>"
				value="<?php echo esc_url( $logo_url ); ?>"
			>
			<button
				type="button"
				class="button seelescript-upload-btn"
				data-title="<?php esc_attr_e( 'Select Footer Logo', 'seelescript' ); ?>"
				data-button-text="<?php esc_attr_e( 'Use this image', 'seelescript' ); ?>"
			>
				<?php esc_html_e( 'Select Image', 'seelescript' ); ?>
			</button>
			<?php if ( $logo_url ) : ?>
				<button type="button" class="button seelescript-remove-btn" style="margin-left:5px;">
					<?php esc_html_e( 'Remove', 'seelescript' ); ?>
				</button>
			<?php endif; ?>
			<p class="description"><?php esc_html_e( 'Upload or select the logo displayed in the footer.', 'seelescript' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render the Copyright Text field.
	 */
	public static function render_copyright_field(): void {
		$copyright = get_option( ThemeSettings::OPTION_FOOTER_COPYRIGHT, '' );
		$copyright = is_string( $copyright ) ? $copyright : '';
		?>
		<input
			type="text"
			id="seelescript-footer-copyright"
			name="<?php echo esc_attr( ThemeSettings::OPTION_FOOTER_COPYRIGHT ); ?>"
			value="<?php echo esc_attr( $copyright ); ?>"
			class="regular-text"
			placeholder="<?php esc_attr_e( '© 2024 Your Company. All rights reserved.', 'seelescript' ); ?>"
		>
		<p class="description"><?php esc_html_e( 'Copyright text shown in the footer. Supports plain text only.', 'seelescript' ); ?></p>
		<?php
	}

	/**
	 * Render the Social Icons repeater field.
	 *
	 * Stores data as a JSON-encoded array via a hidden input that is kept
	 * in sync by admin-theme-settings.js.
	 */
	public static function render_social_field(): void {
		$raw   = get_option( ThemeSettings::OPTION_FOOTER_SOCIAL, '[]' );
		$raw   = is_string( $raw ) ? $raw : '[]';
		$items = json_decode( $raw, true );
		$items = is_array( $items ) ? $items : array();
		?>
		<div id="seelescript-social-wrap">
			<input
				type="hidden"
				id="seelescript-social-data"
				name="<?php echo esc_attr( ThemeSettings::OPTION_FOOTER_SOCIAL ); ?>"
				value="<?php echo esc_attr( $raw ); ?>"
			>

			<div id="social-icons-list">
				<?php foreach ( $items as $idx => $item ) : ?>
					<div class="social-icon-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
						<input
							type="text"
							class="social-icon-class"
							placeholder="<?php esc_attr_e( 'Icon class (e.g. fab fa-twitter)', 'seelescript' ); ?>"
							value="<?php echo esc_attr( $item['icon'] ?? '' ); ?>"
							style="width:220px;"
						>
						<input
							type="url"
							class="social-icon-url"
							placeholder="https://"
							value="<?php echo esc_url( $item['url'] ?? '' ); ?>"
							style="width:260px;"
						>
						<button type="button" class="button button-secondary remove-social-row">
							<?php esc_html_e( 'Remove', 'seelescript' ); ?>
						</button>
					</div>
				<?php endforeach; ?>
			</div>

			<button type="button" id="add-social-row" class="button" style="margin-top:8px;">
				<?php esc_html_e( '+ Add Social Icon', 'seelescript' ); ?>
			</button>
		</div>
		<p class="description" style="margin-top:8px;">
			<?php esc_html_e( 'Add icon class (e.g. using Font Awesome: fab fa-twitter) and the link URL for each social account.', 'seelescript' ); ?>
		</p>

		<?php /* Row template — hidden, cloned by JS */ ?>
		<template id="social-icon-row-template">
			<div class="social-icon-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
				<input
					type="text"
					class="social-icon-class"
					placeholder="<?php esc_attr_e( 'Icon class (e.g. fab fa-twitter)', 'seelescript' ); ?>"
					value=""
					style="width:220px;"
				>
				<input
					type="url"
					class="social-icon-url"
					placeholder="https://"
					value=""
					style="width:260px;"
				>
				<button type="button" class="button button-secondary remove-social-row">
					<?php esc_html_e( 'Remove', 'seelescript' ); ?>
				</button>
			</div>
		</template>
		<?php
	}
}
