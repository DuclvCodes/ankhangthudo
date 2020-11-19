<?php
/**
 * Widget: Agent Filter
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Widget_Agent_Filter extends WP_Widget {
	/**
	 * Initialize widget
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct(
			'agent_filter_widget',
			__( 'Agent Filter', 'wp-realestate' ),
			array(
				'description' => __( 'Filter for filtering agents.', 'wp-realestate' ),
			)
		);
	}

	/**
	 * Frontend
	 *
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		include WP_RealEstate_Template_Loader::locate( 'widgets/agent-filter' );
	}

	/**
	 * Update
	 *
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Backend
	 *
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : '';
		$sort = ! empty( $instance['sort'] ) ? $instance['sort'] : '';

		$_id = rand(100, 100000);
		?>
		<div id="filter-agent-<?php echo esc_attr($_id); ?>">
			<!-- TITLE -->
			<p>
			    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			        <?php echo __( 'Title', 'wp-realestate' ); ?>
			    </label>

			    <input  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>

			<!-- BUTTON TEXT -->
			<p>
			    <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>">
			        <?php echo __( 'Button text', 'wp-realestate' ); ?>
			    </label>

			    <input  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
			</p>

			<h3><?php _e('Filter Fields', 'wp-realestate'); ?></h3>
			<ul class="wp-realestate-filter-fields wp-realestate-filter-agent-fields">
				<?php

				$fields = $all_fields = WP_RealEstate_Agent_Filter::get_fields();
				
				if ( ! empty( $sort ) ) {
					$filtered_keys = array_filter( explode( ',', $sort ) );
					$fields = array_replace( array_flip( $filtered_keys ), $all_fields );
				}
				
				?>
				<input type="hidden" value="<?php echo esc_attr( $sort ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort' ) ); ?>" value="<?php echo esc_attr( $sort ); ?>">

				<?php foreach ( $fields as $key => $value ) :
					if ( !empty($all_fields[$key]) ) {
				?>
					<li data-field-id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $instance[ 'hide_' . $key ] ) ) : ?>class="invisible"<?php endif; ?>>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'hide_' . $key ) ); ?>">
								<?php echo esc_attr( $value['name'] ); ?>
							</label>

							<span class="visibility">
								<input 	type="checkbox" class="checkbox field-visibility" <?php echo ! empty( $instance[ 'hide_'. $key ] ) ? 'checked="checked"' : ''; ?> name="<?php echo esc_attr( $this->get_field_name( 'hide_' . $key ) ); ?>">

								<i class="dashicons dashicons-visibility"></i>
							</span>
						</p>
					</li>
				<?php } ?>
				<?php endforeach ?>
			</ul>

		</div>

		<script type="text/javascript">
			
			jQuery(document).ready(function($) {
				var self = $("body #filter-agent-<?php echo esc_attr($_id); ?>");

				$('.wp-realestate-filter-agent-fields', self).each(function() {
					var el = $(this);

					el.sortable({
						update: function(event, ui) {
							var data = el.sortable('toArray', {
								attribute: 'data-field-id'
							});

							$('#<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>').attr('value', data);
						}
					});

					$(this).find('input[type=checkbox]').on('change', function() {
						if ($(this).is(':checked')) {
							$(this).closest('li').addClass('invisible');
						} else {
							$(this).closest('li').removeClass('invisible');
						}
					});
				});
			});
		</script>

		<?php
	}
}
register_widget('WP_RealEstate_Widget_Agent_Filter');