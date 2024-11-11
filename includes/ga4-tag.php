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

	// Invalid, reset back to the empty string.
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
 * Retrieves the post author's username.
 *
 * @return string Author username or empty string.
 */
function ga4_ext_get_post_author() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$post_author = get_userdata( $post->post_author );

	return $post_author ? $post_author->user_login : 'unknown';
}

/**
 * Retrieves the post categories as a space-separated string.
 *
 * @return string Categories or empty string.
 */
function ga4_ext_get_post_category() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$terms = get_the_terms( $post->ID, 'category' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return 'uncategorized';
	}

	$term_slugs = wp_list_pluck( $terms, 'slug' );

	return trim( implode( ' ', $term_slugs ) );
}

/**
 * Retrieves the post tags as a space-separated string.
 *
 * @return string Tags or empty string.
 */
function ga4_ext_get_post_tags() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$terms = get_the_terms( $post->ID, 'post_tag' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	$term_slugs = wp_list_pluck( $terms, 'slug' );

	return trim( implode( ' ', $term_slugs ) );
}

/**
 * Is the current user a subscriber?
 *
 * @return int
 */
function ga4_ext_is_subscriber(): int {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();

		if (
			$user instanceof WP_User
			&& in_array( 'subscriber', (array) $user->roles, true )
		) {
			return 1;
		}
	}

	return 0;
}

/**
 * Enqueue the GA4 scripts.
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

	// Enqueue the gtag script deferred.
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

	$data = [];
	if ( is_single() ) {
		$post_author   = ga4_ext_get_post_author();
		$post_category = ga4_ext_get_post_category();
		$post_tags     = ga4_ext_get_post_tags();

		$data['post_author']   = $post_author;
		$data['post_category'] = $post_category;
		$data['post_tags']     = $post_tags;
	}

	$is_subscriber = ga4_ext_is_subscriber();

	// Added before, as it's the only type of supported inline script by the defer strategy.
	wp_add_inline_script(
		'ga4-ext-gtagjs',
		'window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag("set", "linker", { "domains": ' . wp_json_encode( [ $domain ] ) . ' });
gtag("js", new Date() );
gtag("config", "' . esc_attr( $measurement_id ) . '", ' . wp_json_encode( $data ) . ');
gtag("set", "user_properties", { is_subscriber: ' . esc_attr( $is_subscriber ) . ' } );',
		'before'
	);
	wp_enqueue_script( 'ga4-ext-gtagjs' );
}

add_action( 'admin_init', 'ga4_ext_register_settings' );
add_action( 'wp_footer', 'ga4_ext_enqueue_ga4_scripts' );
