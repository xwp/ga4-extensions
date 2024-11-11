<?php
/**
 * Google Analytics 4 Extensions - GA4 Tag Functions
 *
 * @package ga4-extensions
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register the GA4 Measurement ID setting.
 */
function ga4_ext_register_settings() {
	register_setting(
		'general', // Option group.
		'ga4_measurement_id', // Option name.
		[
			'type'              => 'string',
			'sanitize_callback' => 'ga4_ext_sanitize_measurement_id',
			'default'           => '',
		]
	);

	add_settings_field(
		'ga4_measurement_id',
		__( 'Google Analytics 4 ID', 'ga4-extensions' ),
		'ga4_ext_render_measurement_id_field',
		'general',
		'default',
		[
			'label_for' => 'ga4_measurement_id',
		]
	);
}

/**
 * Sanitize the GA4 Measurement ID input.
 *
 * @param string $input The input value.
 * @return string The sanitized GA4 Measurement ID.
 */
function ga4_ext_sanitize_measurement_id( $input ) {
	// Ensures the input matches the GA4 Measurement ID format (G-XXXXXXX).
	if ( preg_match( '/^G-[A-Z0-9]+$/', strtoupper( trim( $input ) ) ) ) {
		return strtoupper( trim( $input ) );
	}

	// Invalid, reset to the empty string.
	return '';
}

/**
 * Render the GA4 Measurement ID input field.
 *
 * @param array $args The field arguments.
 */
function ga4_ext_render_measurement_id_field( $args ) {
	$option = get_option( 'ga4_measurement_id', '' );
	?>
	<input
		type="text"
		id="<?php echo esc_attr( $args['label_for'] ); ?>"
		name="ga4_measurement_id"
		value="<?php echo esc_attr( $option ); ?>"
		class="regular-text"
		placeholder="G-XXXXXXX"
	/>
	<p class="description">
		<?php esc_html_e( 'Enter your Google Analytics 4 Measurement ID (e.g., G-XXXXXXXXXX).', 'ga4-extensions' ); ?>
	</p>
	<?php
}

/**
 * Enqueue the GA4 scripts in the footer.
 */
function ga4_ext_enqueue_ga4_scripts() {
	$measurement_id = get_option( 'ga4_measurement_id', '' );

	// If no Measurement ID is set, do not enqueue the scripts.
	if ( empty( $measurement_id ) ) {
		return;
	}

	// Get just the domain of the site URL.
	// https://developer.wordpress.org/reference/functions/get_site_url/
	$domain = wp_parse_url( get_site_url(), PHP_URL_HOST );

	if ( ! $domain ) {
		return;
	}

	// Enqueue the gtag script deferred, in the future.
	wp_register_script(
		'ga4-ext-gtagjs',
		'https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $measurement_id ),
		[],
		null,
		[
			'in_footer' => true,
			'strategy'  => 'defer',
		]
	);

	// Added before, as it's the only supported inline script by the defer strategy.
	wp_add_inline_script(
		'ga4-ext-gtagjs',
		'window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("set", "linker", { "domains": ' . wp_json_encode( [ $domain ] ) . ' });
gtag("js", new Date());
gtag("config", "' . esc_attr( $measurement_id ) . '");',
		'before'
	);
	wp_enqueue_script( 'ga4-ext-gtagjs' );
}

add_action( 'admin_init', 'ga4_ext_register_settings' );
add_action( 'wp_footer', 'ga4_ext_enqueue_ga4_scripts' );
