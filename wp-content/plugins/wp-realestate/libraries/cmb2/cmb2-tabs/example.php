<?php
/*
 * Example metabox
 */
add_filter( 'cmb2_init', 'cmbt_example_metaboxes' );
function cmbt_example_metaboxes() {
	$box_options = array(
		'id'           => 'example_tabs_metaboxes',
		'title'        => __( 'Example tabs', 'wp-realestate' ),
		'object_types' => array( 'page' ),
		'show_names'   => true,
	);

	// Setup meta box
	$cmb = new_cmb2_box( $box_options );

	// Setting tabs
	$tabs_setting           = array(
		'config' => $box_options,
		// 'layout' => 'vertical', // Default : horizontal
		'tabs'   => array()
	);
	$tabs_setting['tabs'][] = array(
		'id'     => 'tab1',
		'title'  => __( 'Tab 1', 'wp-realestate' ),
		'fields' => array(
			array(
				'name' => __( 'Title', 'wp-realestate' ),
				'id'   => 'header_title',
				'type' => 'text'
			),
			array(
				'name' => __( 'Subtitle', 'wp-realestate' ),
				'id'   => 'header_subtitle',
				'type' => 'text'
			),
			array(
				'name'    => __( 'Background image', 'wp-realestate' ),
				'id'      => 'header_background',
				'type'    => 'file',
				'options' => array(
					'url' => false
				)
			)
		)
	);
	$tabs_setting['tabs'][] = array(
		'id'     => 'tab2',
		'title'  => __( 'Tab 2', 'wp-realestate' ),
		'fields' => array(
			array(
				'name' => __( 'Title', 'wp-realestate' ),
				'id'   => 'review_title',
				'type' => 'text'
			),
			array(
				'name' => __( 'Subtitle', 'wp-realestate' ),
				'id'   => 'review_subtitle',
				'type' => 'text'
			),
			array(
				'id'      => 'reviews',
				'type'    => 'group',
				'options' => array(
					'group_title'   => __( 'Review {#}', 'wp-realestate' ),
					'add_button'    => __( 'Add review', 'wp-realestate' ),
					'remove_button' => __( 'Remove review', 'wp-realestate' ),
					'sortable'      => false
				),
				'fields'  => array(
					array(
						'name' => __( 'Author name', 'wp-realestate' ),
						'id'   => 'name',
						'type' => 'text'
					),
					array(
						'name'    => __( 'Author avatar', 'wp-realestate' ),
						'id'      => 'avatar',
						'type'    => 'file',
						'options' => array(
							'url' => false
						)
					),
					array(
						'name' => __( 'Comment', 'wp-realestate' ),
						'id'   => 'comment',
						'type' => 'textarea'
					)
				)
			)
		)
	);

	// Set tabs
	$cmb->add_field( array(
		'id'   => '__tabs',
		'type' => 'tabs',
		'tabs' => $tabs_setting
	) );
}

/*
 * Example options page
 */
add_action( 'cmb2_admin_init', 'cmbt_example_options_page_metabox' );
function cmbt_example_options_page_metabox() {
	$box_options = array(
		'id'          => 'myprefix_option_metabox',
		'title'       => __( 'Example tabs', 'wp-realestate' ),
		'show_names'  => true,
		'object_type' => 'options-page',
		'show_on'     => array(
			// These are important, don't remove
			'key'   => 'options-page',
			'value' => array( 'myprefix_options' )
		),
	);

	// Setup meta box
	$cmb = new_cmb2_box( $box_options );

	// setting tabs
	$tabs_setting = array(
		'config' => $box_options,
		//		'layout' => 'vertical', // Default : horizontal
		'tabs'   => array()
	);

	$tabs_setting['tabs'][] = array(
		'id'     => 'tab1',
		'title'  => __( 'Tab 1', 'wp-realestate' ),
		'fields' => array(
			array(
				'name' => __( 'Title', 'wp-realestate' ),
				'id'   => 'header_title',
				'type' => 'text'
			),
			array(
				'name' => __( 'Subtitle', 'wp-realestate' ),
				'id'   => 'header_subtitle',
				'type' => 'text'
			),
			array(
				'name'    => __( 'Background image', 'wp-realestate' ),
				'id'      => 'header_background',
				'type'    => 'file',
				'options' => array(
					'url' => false
				)
			)
		)
	);
	$tabs_setting['tabs'][] = array(
		'id'     => 'tab2',
		'title'  => __( 'Tab 2', 'wp-realestate' ),
		'fields' => array(
			array(
				'name' => __( 'Title', 'wp-realestate' ),
				'id'   => 'review_title',
				'type' => 'text'
			),
			array(
				'name' => __( 'Subtitle', 'wp-realestate' ),
				'id'   => 'review_subtitle',
				'type' => 'text'
			),
			array(
				'id'      => 'reviews',
				'type'    => 'group',
				'options' => array(
					'group_title'   => __( 'Review {#}', 'wp-realestate' ),
					'add_button'    => __( 'Add review', 'wp-realestate' ),
					'remove_button' => __( 'Remove review', 'wp-realestate' ),
					'sortable'      => false
				),
				'fields'  => array(
					array(
						'name' => __( 'Author name', 'wp-realestate' ),
						'id'   => 'name',
						'type' => 'text'
					),
					array(
						'name'    => __( 'Author avatar', 'wp-realestate' ),
						'id'      => 'avatar',
						'type'    => 'file',
						'options' => array(
							'url' => false
						)
					),
					array(
						'name' => __( 'Comment', 'wp-realestate' ),
						'id'   => 'comment',
						'type' => 'textarea'
					)
				)
			)
		)
	);

	$cmb->add_field( array(
		'id'   => '__tabs',
		'type' => 'tabs',
		'tabs' => $tabs_setting
	) );
}