<?php
/**
 * Homeo Customizer functionality
 *
 * @package WordPress
 * @subpackage Homeo
 * @since Homeo 1.0
 */

/**
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Homeo 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */

function homeo_customize_register( $wp_customize ) {
	$color_scheme = homeo_get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'homeo_sanitize_color_scheme',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( 'color_scheme', array(
		'label'    => esc_html__( 'Base Color Scheme', 'homeo' ),
		'section'  => 'colors',
		'type'     => 'select',
		'choices'  => homeo_get_color_scheme_choices(),
		'priority' => 1,
	) );

	// Add custom header and sidebar text color setting and control.
	$wp_customize->add_setting( 'sidebar_textcolor', array(
		'default'           => $color_scheme[4],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_textcolor', array(
		'label'       => esc_html__( 'Header and Sidebar Text Color', 'homeo' ),
		'description' => esc_html__( 'Applied to the header on small screens and the sidebar on wide screens.', 'homeo' ),
		'section'     => 'colors',
	) ) );

	// Remove the core header textcolor control, as it shares the sidebar text color.
	

	// Add custom header and sidebar background color setting and control.
	$wp_customize->add_setting( 'header_background_color', array(
		'default'           => $color_scheme[1],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_background_color', array(
		'label'       => esc_html__( 'Header and Sidebar Background Color', 'homeo' ),
		'description' => esc_html__( 'Applied to the header on small screens and the sidebar on wide screens.', 'homeo' ),
		'section'     => 'colors',
	) ) );

	// Add an additional description to the header image section.
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'Applied to the header on small screens and the sidebar on wide screens.', 'homeo' );
	$wp_customize->remove_section( 'header_image' );
	$wp_customize->remove_section( 'colors' );
	$wp_customize->remove_section( 'background_image' );
}
add_action( 'customize_register', 'homeo_customize_register', 20 );


/**
 * Register color schemes for Homeo.
 *
 * Can be filtered with {@see 'homeo_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Color.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 *
 * @since Homeo 1.0
 *
 * @return array An associative array of color scheme options.
 */
function homeo_get_color_schemes() {
	/**
	 * Filter the color schemes registered for use with Homeo.
	 *
	 * The default schemes include 'default', 'dark', 'yellow', 'pink', 'purple', and 'blue'.
	 *
	 * @since Homeo 1.0
	 *
	 * @param array $schemes {
	 *     Associative array of color schemes data.
	 *
	 *     @type array $slug {
	 *         Associative array of information for setting up the color scheme.
	 *
	 *         @type string $label  Color scheme label.
	 *         @type array  $colors HEX codes for default colors prepended with a hash symbol ('#').
	 *                              Colors are defined in the following order: Main background, sidebar
	 *                              background, box background, main text and link, sidebar text and link,
	 *                              meta box background.
	 *     }
	 * }
	 */
	return apply_filters( 'homeo_color_schemes', array(
		'default' => array(
			'label'  => esc_html__( 'Default', 'homeo' ),
			'colors' => array(
				'#f1f1f1',
				'#ffffff',
				'#ffffff',
				'#333333',
				'#333333',
				'#f7f7f7',
			),
		),
		'dark'    => array(
			'label'  => esc_html__( 'Dark', 'homeo' ),
			'colors' => array(
				'#111111',
				'#202020',
				'#202020',
				'#bebebe',
				'#bebebe',
				'#1b1b1b',
			),
		),
		'yellow'  => array(
			'label'  => esc_html__( 'Yellow', 'homeo' ),
			'colors' => array(
				'#f4ca16',
				'#ffdf00',
				'#ffffff',
				'#111111',
				'#111111',
				'#f1f1f1',
			),
		),
		'pink'    => array(
			'label'  => esc_html__( 'Pink', 'homeo' ),
			'colors' => array(
				'#ffe5d1',
				'#e53b51',
				'#ffffff',
				'#352712',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'purple'  => array(
			'label'  => esc_html__( 'Purple', 'homeo' ),
			'colors' => array(
				'#674970',
				'#2e2256',
				'#ffffff',
				'#2e2256',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'blue'   => array(
			'label'  => esc_html__( 'Blue', 'homeo' ),
			'colors' => array(
				'#e9f2f9',
				'#55c3dc',
				'#ffffff',
				'#22313f',
				'#ffffff',
				'#f1f1f1',
			),
		),
	) );
}

if ( ! function_exists( 'homeo_get_color_scheme' ) ) :
/**
 * Get the current Homeo color scheme.
 *
 * @since Homeo 1.0
 *
 * @return array An associative array of either the current or default color scheme hex values.
 */
function homeo_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = homeo_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // homeo_get_color_scheme

if ( ! function_exists( 'homeo_get_color_scheme_choices' ) ) :
/**
 * Returns an array of color scheme choices registered for Homeo.
 *
 * @since Homeo 1.0
 *
 * @return array Array of color schemes.
 */
function homeo_get_color_scheme_choices() {
	$color_schemes                = homeo_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // homeo_get_color_scheme_choices

if ( ! function_exists( 'homeo_sanitize_color_scheme' ) ) :
/**
 * Sanitization callback for color schemes.
 *
 * @since Homeo 1.0
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function homeo_sanitize_color_scheme( $value ) {
	$color_schemes = homeo_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		$value = 'default';
	}

	return $value;
}
endif; // homeo_sanitize_color_scheme
