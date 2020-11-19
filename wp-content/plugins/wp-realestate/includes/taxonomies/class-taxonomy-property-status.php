<?php
/**
 * Statuses
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WP_RealEstate_Taxonomy_Property_Status{

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 1 );

		add_filter( "manage_edit-property_status_columns", array( __CLASS__, 'tax_columns' ) );
		add_filter( "manage_property_status_custom_column", array( __CLASS__, 'tax_column' ), 10, 3 );

		add_action( "property_status_add_form_fields", array( __CLASS__, 'add_fields_form' ) );
		add_action( "property_status_edit_form_fields", array( __CLASS__, 'edit_fields_form' ), 10, 2 );

		add_action( 'create_term', array( __CLASS__, 'save' )  );
		add_action( 'edit_term', array( __CLASS__, 'save' ) );
	}

	/**
	 *
	 */
	public static function definition($args) {
		$labels = array(
			'name'              => __( 'Statuses', 'wp-realestate' ),
			'singular_name'     => __( 'Status', 'wp-realestate' ),
			'search_items'      => __( 'Search Statuses', 'wp-realestate' ),
			'all_items'         => __( 'All Statuses', 'wp-realestate' ),
			'parent_item'       => __( 'Parent Status', 'wp-realestate' ),
			'parent_item_colon' => __( 'Parent Status:', 'wp-realestate' ),
			'edit_item'         => __( 'Edit', 'wp-realestate' ),
			'update_item'       => __( 'Update', 'wp-realestate' ),
			'add_new_item'      => __( 'Add New', 'wp-realestate' ),
			'new_item_name'     => __( 'New Status', 'wp-realestate' ),
			'menu_name'         => __( 'Statuses', 'wp-realestate' ),
		);

		$rewrite_slug = get_option('wp_realestate_property_status_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'property-status', 'Statuses slug - resave permalinks after changing this', 'wp-realestate' );
		}
		$rewrite = array(
			'slug'         => $rewrite_slug,
			'with_front'   => false,
			'hierarchical' => false,
		);
		register_taxonomy( 'property_status', 'property', array(
			'labels'            => apply_filters( 'wp_realestate_taxomony_property_status_labels', $labels ),
			'hierarchical'      => true,
			'rewrite'           => $rewrite,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'		=> true
		) );
	}

	public static function add_fields_form($taxonomy) {
		global $apus_cityo_listing_type;
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Background Color', 'wp-realestate' ); ?></label>
			<?php self::color_field('bg_color'); ?>
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Text Color', 'wp-realestate' ); ?></label>
			<?php self::color_field('text_color'); ?>
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Order', 'wp-realestate' ); ?></label>
			<?php self::input_field(); ?>
		</div>
		<?php
	}

	public static function edit_fields_form( $term, $taxonomy ) {
		$bg_color = get_term_meta( $term->term_id, 'bg_color', true );
		$text_color = get_term_meta( $term->term_id, 'text_color', true );
		$menu_order = get_term_meta( $term->term_id, 'menu_order', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Background Color', 'wp-realestate' ); ?></label></th>
			<td>
				<?php self::color_field('bg_color', $bg_color); ?>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Text Color', 'wp-realestate' ); ?></label></th>
			<td>
				<?php self::color_field('text_color', $text_color); ?>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Order', 'wp-realestate' ); ?></label></th>
			<td>
				<?php self::input_field($menu_order); ?>
			</td>
		</tr>
		<?php
	}

	public static function color_field( $name, $val = '' ) {
		?>
		<input class="tax_color_input" name="<?php echo esc_attr($name); ?>" type="text" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	public static function input_field( $val = '' ) {
		?>
		<input name="menu_order" type="number" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	public static function save( $term_id ) {
	    if ( isset( $_POST['bg_color'] ) ) {
	    	update_term_meta( $term_id, 'bg_color', $_POST['bg_color'] );
	    }

	    if ( isset( $_POST['text_color'] ) ) {
	    	update_term_meta( $term_id, 'text_color', $_POST['text_color'] );
	    }
	    
	    if ( isset( $_POST['menu_order'] ) ) {
	    	update_term_meta( $term_id, 'menu_order', $_POST['menu_order'] );
	    } else {
	    	update_term_meta( $term_id, 'menu_order', 0 );
	    }
	}

	public static function tax_columns( $columns ) {
		$new_columns = array();
		foreach ($columns as $key => $value) {
			if ( $key == 'name' ) {
				$new_columns['color'] = esc_html__( 'Color', 'wp-realestate' );
			}
			if ( $key == 'posts' ) {
				$new_columns['menu_order'] = esc_html__( 'Order', 'wp-realestate' );
			}
			$new_columns[$key] = $value;
		}
		return $new_columns;
	}

	public static function tax_column( $columns, $column, $id ) {
		if ( $column == 'color' ) {
			$term = get_term($id);
			$bg_color = get_term_meta( $id, 'bg_color', true );
			$text_color = get_term_meta( $id, 'text_color', true );
			$styles = array();
			if ( !empty($bg_color) ) {
				$styles[] = 'background-color: '.$bg_color;
			}
			if ( !empty($text_color) ) {
				$styles[] = 'color: '.$text_color;
			}
			if ( !empty($styles) ) {
				$styles[] = 'padding: 10px; display: inline-block;';
				?>
				<div style="<?php echo esc_attr(implode(';', $styles)); ?>"><?php echo $term->name; ?></div>
				<?php
			}
		} elseif ( $column == 'menu_order' ) {
			$menu_order = get_term_meta( $id, 'menu_order', true );
			echo intval($menu_order);
		}
		return $columns;
	}
}

WP_RealEstate_Taxonomy_Property_Status::init();