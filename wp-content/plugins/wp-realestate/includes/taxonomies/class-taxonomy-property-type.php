<?php
/**
 * Types
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WP_RealEstate_Taxonomy_Property_Type{

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 1 );

		add_filter( "manage_edit-property_type_columns", array( __CLASS__, 'tax_columns' ) );
		add_filter( "manage_property_type_custom_column", array( __CLASS__, 'tax_column' ), 10, 3 );
		add_action( "property_type_add_form_fields", array( __CLASS__, 'add_fields_form' ) );
		add_action( "property_type_edit_form_fields", array( __CLASS__, 'edit_fields_form' ), 10, 2 );

		add_action( 'create_term', array( __CLASS__, 'save' )  );
		add_action( 'edit_term', array( __CLASS__, 'save' ) );
	}

	/**
	 *
	 */
	public static function definition() {
		$labels = array(
			'name'              => __( 'Types', 'wp-realestate' ),
			'singular_name'     => __( 'Type', 'wp-realestate' ),
			'search_items'      => __( 'Search Types', 'wp-realestate' ),
			'all_items'         => __( 'All Types', 'wp-realestate' ),
			'parent_item'       => __( 'Parent Type', 'wp-realestate' ),
			'parent_item_colon' => __( 'Parent Type:', 'wp-realestate' ),
			'edit_item'         => __( 'Edit', 'wp-realestate' ),
			'update_item'       => __( 'Update', 'wp-realestate' ),
			'add_new_item'      => __( 'Add New', 'wp-realestate' ),
			'new_item_name'     => __( 'New Type', 'wp-realestate' ),
			'menu_name'         => __( 'Types', 'wp-realestate' ),
		);

		$rewrite_slug = get_option('wp_realestate_property_type_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'property-type', 'Property type slug - resave permalinks after changing this', 'wp-realestate' );
		}
		$rewrite = array(
			'slug'         => $rewrite_slug,
			'with_front'   => false,
			'hierarchical' => false,
		);
		register_taxonomy( 'property_type', 'property', array(
			'labels'            => apply_filters( 'wp_realestate_taxomony_property_type_labels', $labels ),
			'hierarchical'      => true,
			'rewrite'           => $rewrite,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'		=> true
		) );
	}

	public static function add_fields_form($taxonomy) {
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Color', 'wp-realestate' ); ?></label>
			<?php self::color_field(); ?>
		</div>
		<?php
	}

	public static function edit_fields_form( $term, $taxonomy ) {
			$color_value = get_term_meta( $term->term_id, '_color', true );
		?>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php esc_html_e( 'Color', 'wp-realestate' ); ?></label></th>
				<td>
					<?php self::color_field($color_value); ?>
				</td>
			</tr>

		<?php
	}

	public static function color_field( $val = '' ) {
		?>
		<input class="tax_color_input" name="wp_realestate_color" type="text" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	public static function save( $term_id ) {
	    if ( isset( $_POST['wp_realestate_color'] ) ) {
	    	update_term_meta( $term_id, '_color', $_POST['wp_realestate_color'] );
	    }
	}

	public static function tax_columns( $columns ) {
		$new_columns = array();
		foreach ($columns as $key => $value) {
			if ( $key == 'name' ) {
				$new_columns['color'] = esc_html__( 'Color', 'wp-realestate' );
			}
			$new_columns[$key] = $value;
		}
		return $new_columns;
	}

	public static function tax_column( $columns, $column, $id ) {
		if ( $column == 'color' ) {
			$color = get_term_meta( $id, '_color', true );
			if ( $color ) {
				?>
				<div style="background: <?php echo trim($color); ?>; height: 30px; width: 50%; border-radius: 5px;"></div>
				<?php
			}
		}
		return $columns;
	}
}

WP_RealEstate_Taxonomy_Property_Type::init();