<?php

/**
 * Class WP_RealEstate_CMB2_Field_Taxonomy_Location
 */
class WP_RealEstate_CMB2_Field_Taxonomy_Location {

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public function __construct() {
		add_filter( 'cmb2_render_wpre_taxonomy_location', array( $this, 'render_taxonomy_location' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wpre_taxonomy_location', array( $this, 'sanitize' ), 10, 4 );
		add_filter( 'cmb2_types_esc_wpre_taxonomy_location', array( $this, 'escaped_value' ), 10, 3 );
		add_filter( 'cmb2_repeat_table_row_types', array( $this, 'table_row_class' ), 10, 1 );

		// Ajax endpoints.
		add_action( 'wre_ajax_wpre_process_change_location', array( $this, 'process_change_location' ) );
		
		// compatible handlers.
		add_action( 'wp_ajax_wpre_process_change_location', array( $this, 'process_change_location' ) );
		add_action( 'wp_ajax_nopriv_wpre_process_change_location', array( $this, 'process_change_location' ) );
	}

	/**
	 * Render select box field
	 */
	public function render_taxonomy_location( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$this->setup_admin_scripts();

		if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
			$field_type_object->type = new CMB2_Type_Select( $field_type_object );
		}
		
		$nb_fields = apply_filters('wp_realestate_cmb2_field_taxonomy_location_number', 4);
		$parent = 0;
		echo '<div class="field-taxonomy-location-wrapper field-taxonomy-location-wrapper-'.$nb_fields.'">';
		for ($i=1; $i <= $nb_fields; $i++) {

			$taxonomy_options = $this->get_taxonomy_options( $field_escaped_value, $field_type_object, $parent );
			$parent = !empty($taxonomy_options['parent']) ? $taxonomy_options['parent'] : 'no';
			
			$field_name = apply_filters('wp_realestate_cmb2_field_taxonomy_location_field_name_'.$i, 'Country');
			$placeholder = $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : $field->args( 'description' );
			$placeholder = sprintf($placeholder, $field_name);

			$a = $field_type_object->parse_args( 'wpre_taxonomy_location', array(
				'style'            => 'width: 99%',
				'class'            => 'wpre_taxonomy_location wpre_taxonomy_location'.$i,
				'name'             => $field_type_object->_name() . '[]',
				'id'               => $field_type_object->_id().$i,
				'desc'             => $field_type_object->_desc( true ),
				'options'          => $taxonomy_options['option'],
				'data-placeholder' => $placeholder,
				'data-next' => ($i + 1),
				'data-taxonomy' => $field_type_object->field->args( 'taxonomy' ),
				'data-allowclear' => true
			) );

			$attrs = $field_type_object->concat_attrs( $a, array( 'desc', 'options' ) );
			echo sprintf( '<div class="field-taxonomy-location-%d"><select%s>%s</select></div>', $i, $attrs, $a['options'] );
		}
		echo '</div>';
		if ( !empty($a['desc']) ) {
			echo $a['desc'];
		}
	}

	public function get_taxonomy_options( $field_escaped_value = array(), $field_type_object, $parent ) {
		$options = (array) $this->get_terms($field_type_object->field->args( 'taxonomy' ), array('parent' => $parent));
		
		$field_escaped_value = $this->options_terms($field_type_object->field);
		
		// if ( ! empty( $field_escaped_value ) ) {
		// 	if ( !is_array($field_escaped_value) ) {
		// 		$field_escaped_value = array($field_escaped_value);
		// 	}
		// 	$options = $this->sort_array_by_array( $options, $field_escaped_value );
		// }

		$return = array();
		$selected_items = '';
		$other_items = '<option></option>';

		foreach ( $options as $option_value => $option_label ) {
			// Clone args & modify for just this item
			$option = array(
				'value' => $option_value,
				'label' => $option_label,
			);

			// Split options into those which are selected and the rest
			if ( in_array( $option_value, (array) $field_escaped_value ) ) {
				$return['parent'] = $option_value;
				$option['checked'] = true;
				$selected_items .= $field_type_object->select_option( $option );
			} else {
				$other_items .= $field_type_object->select_option( $option );
			}
		}
		$return['option'] = $selected_items . $other_items;

		return $return;
	}

	public function options_terms($field) {
		if ( empty($field->data_args()['id']) ) {
			return array();
		}
		$object_id = $field->data_args()['id'];
		$terms = get_the_terms( $object_id, $field->args( 'taxonomy' ) );

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			foreach ( $terms as $index => $term ) {
				$terms[ $index ] = $term->term_id;
			}
		}

		return $terms;
	}

	public function sort_array_by_array( array $array, array $orderArray ) {
		$ordered = array();

		foreach ( $orderArray as $key ) {
			if ( array_key_exists( $key, $array ) ) {
				$ordered[ $key ] = $array[ $key ];
				unset( $array[ $key ] );
			}
		}

		return $ordered + $array;
	}

	/**
	 * Handle sanitization for repeatable fields
	 */
	public function sanitize( $check, $meta_value, $object_id, $field_args ) {
		if ( empty($meta_value) || !is_array( $meta_value ) ) {
			return $check;
		}
		if ( $field_args['repeatable'] ) {
			foreach ( $meta_value as $key => $val ) {
				$meta_value[$key] = array_map( 'absint', $val );
				wp_set_object_terms( $object_id, array_map( 'absint', $val ), $field_args['taxonomy'], false );
			}
		} else {
			$meta_value = array_map( 'absint', $meta_value );
			wp_set_object_terms( $object_id, $meta_value, $field_args['taxonomy'], false );
		}

		return $meta_value;
	}


	/**
	 * Handle escaping for repeatable fields
	 */
	public function escaped_value( $check, $meta_value, $field_args ) {
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[$key] = array_map( 'esc_attr', $val );
		}

		return $meta_value;
	}

	/**
	 * Add 'table-layout' class to multi-value select field
	 */
	public function table_row_class( $check ) {
		$check[] = 'wpre_taxonomy_location';

		return $check;
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function setup_admin_scripts() {
		$asset_path = apply_filters( 'wpre_cmb2_field_select2_asset_path', plugins_url( '', __FILE__  ) );

		wp_enqueue_script( 'wpre-taxonomy-location-script', $asset_path . '/js/script.js', array( 'cmb2-scripts', 'select2', 'jquery-ui-sortable' ), self::VERSION );
		wp_localize_script( 'wpre-taxonomy-location-script', 'location_opts', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajaxurl_endpoint'      => WP_RealEstate_Ajax::get_endpoint(),
			'ajax_nonce' => wp_create_nonce( 'wpre-ajax-nonce' )
		));
		wp_enqueue_style( 'wpre-taxonomy-location-style', $asset_path . '/css/style.css', array( 'select2' ), self::VERSION );
	}

    public function get_terms($taxonomy, $query_args = array()) {
        $return = array();

        $defaults = array(
	        'hide_empty' => false,
	        'orderby' => 'name',
            'order' => 'ASC',
            'hierarchical' => 1,
	    );
	    $args = wp_parse_args( $query_args, $defaults );
	    
	    $terms = get_terms( $taxonomy, $args );
	    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
	        foreach ( $terms as $key => $term ) {
                $return[$term->term_id] = $term->name;
	        }
	    }
		
        return $return;
    }

    public function process_change_location() {
    	check_ajax_referer( 'wpre-ajax-nonce', 'security' );
    	$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
    	$parent = !empty($_POST['parent']) ? $_POST['parent'] : '';
		$options = array( array('id' => '', 'name' => ''));
		if ( $parent ) {
			$args = array(
		        'hide_empty' => false,
		        'orderby' => 'name',
	            'order' => 'ASC',
	            'hierarchical' => 1,
	            'parent' => $parent
		    );
		    
		    $terms = get_terms( $taxonomy, $args );
		    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
		        foreach ( $terms as $key => $term ) {
	                $options[] = array('id' => $term->term_id, 'name' => $term->name);
		        }
		    }
		}

		echo json_encode($options);
		wp_die();
    }
}
$wpre_cmb2_field_select2 = new WP_RealEstate_CMB2_Field_Taxonomy_Location();
