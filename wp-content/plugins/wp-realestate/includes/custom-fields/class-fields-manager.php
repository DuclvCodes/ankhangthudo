<?php
/**
 * Fields Manager
 *
 * @package    wp-realestate
 * @author     Habq
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
class WP_RealEstate_Fields_Manager {

	public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_page' ), 1 );
        add_action( 'init', array(__CLASS__, 'init_hook'), 10 );
	}

    public static function register_page() {
        add_submenu_page( 'edit.php?post_type=property', __( 'Fields Manager', 'wp-realestate' ), __( 'Fields Manager', 'wp-realestate' ), 'manage_options', 'property-manager-fields-manager', array( __CLASS__, 'output' ) );
    }

    public static function init_hook() {
        // Ajax endpoints.
        add_action( 'wre_ajax_wp_realestate_custom_field_html', array( __CLASS__, 'custom_field_html' ) );

        add_action( 'wre_ajax_wp_realestate_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );

        // compatible handlers.
        // custom fields
        add_action( 'wp_ajax_wp_realestate_custom_field_html', array( __CLASS__, 'custom_field_html' ) );
        add_action( 'wp_ajax_nopriv_wp_realestate_custom_field_html', array( __CLASS__, 'custom_field_html' ) );

        add_action( 'wp_ajax_wp_realestate_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );
        add_action( 'wp_ajax_nopriv_wp_realestate_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );

        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ), 1 );
    }

    public static function scripts() {
        // icon
        wp_enqueue_style('jquery-fonticonpicker', WP_REALESTATE_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.min.css', array(), '1.0');
        wp_enqueue_style('jquery-fonticonpicker-bootstrap', WP_REALESTATE_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.bootstrap.min.css', array(), '1.0');
        wp_enqueue_script('jquery-fonticonpicker', WP_REALESTATE_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.min.js', array(), '1.0', true);

        wp_enqueue_style('wp-realestate-custom-field-css', WP_REALESTATE_PLUGIN_URL . 'assets/admin/style.css');
        wp_register_script('wp-realestate-custom-field', WP_REALESTATE_PLUGIN_URL.'assets/admin/functions.js', array('jquery', 'wp-color-picker'), '', true);

        $args = array(
            'plugin_url' => WP_REALESTATE_PLUGIN_URL,
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        wp_localize_script('wp-realestate-custom-field', 'wp_realestate_customfield_common_vars', $args);
        wp_enqueue_script('wp-realestate-custom-field');

        wp_enqueue_script('jquery-ui-sortable');
    }

    public static function output() {
        $prefix = WP_REALESTATE_PROPERTY_PREFIX;
        self::save();
        ?>
        <h1><?php echo esc_html__('Fields manager', 'wp-realestate'); ?></h1>

        <form class="property-manager-options" method="post" action="admin.php?page=property-manager-fields-manager">
            
            <button type="submit" class="button button-primary" name="updatePropertyFieldManager"><?php esc_html_e('Update', 'wp-realestate'); ?></button>
            
            <?php

            $rand_id = rand(123, 9878787);
            $default_fields = self::get_all_field_types();

            $available_fields = self::get_all_types_fields_available();
            $required_types = self::get_all_types_fields_required();

            $custom_all_fields_saved_data = self::get_custom_fields_data();

            ?>
            <div class="custom-fields-wrapper clearfix">
                            
                <div class="wp-realestate-custom-field-form" id="wp-realestate-custom-field-form-<?php echo esc_attr($rand_id); ?>">
                    <div class="box-wrapper">
                        <h3 class="title"><?php echo esc_html('List of Fields', 'wp-realestate'); ?></h3>
                        <ul id="foo<?php echo esc_attr($rand_id); ?>" class="block__list block__list_words"> 
                            <?php

                            $count_node = 1000;
                            $output = '';
                            $all_fields_name_count = 0;
                            $disabled_fields = array();

                            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
                                $field_names_counter = 0;
                                $types = self::get_all_field_type_keys();
                                foreach ($custom_all_fields_saved_data as $key => $custom_field_saved_data) {
                                    $all_fields_name_count++;
                                    
                                    $li_rand_id = rand(454, 999999);

                                    $output .= '<li class="custom-field-class-' . $li_rand_id . '">';

                                    $fieldtype = $custom_field_saved_data['type'];

                                    $delete = true;
                                    $drfield_values = self::get_field_id($fieldtype, $required_types);
                                    $dvfield_values = self::get_field_id($fieldtype, $available_fields);
                                    if ( !empty($drfield_values) ) {
                                        $count_node ++;
                                        
                                        $delete = false;
                                        $field_values = wp_parse_args( $custom_field_saved_data, $drfield_values);
                                        if ( in_array( $fieldtype, array( $prefix.'title', $prefix.'expiry_date', $prefix.'featured' ) ) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_available_simple_html', $fieldtype, $count_node, $field_values);
                                        } elseif ( in_array( $fieldtype, array( $prefix.'description' ) ) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_available_description_html', $fieldtype, $count_node, $field_values);
                                        } else {
                                            $output .= apply_filters('wp_realestate_custom_field_available_'.$fieldtype.'_html', $fieldtype, $count_node, $field_values);
                                        }
                                    } elseif ( !empty($dvfield_values) ) {
                                        $count_node ++;
                                        $field_values = wp_parse_args( $custom_field_saved_data, $dvfield_values);

                                        $dtypes = apply_filters( 'wp_realestate_list_simple_type', array( $prefix.'featured', $prefix.'year_built', $prefix.'property_id', $prefix.'address', $prefix.'map_location', $prefix.'price', $prefix.'price_prefix', $prefix.'price_suffix', $prefix.'price_custom', $prefix.'rooms', $prefix.'beds', $prefix.'baths', $prefix.'garages', $prefix.'home_area', $prefix.'lot_dimensions', $prefix.'lot_area', $prefix.'video', $prefix.'virtual_tour', $prefix.'parent_property', $prefix.'valuation_group', $prefix.'public_facilities_group', $prefix.'floor_plans_group', $prefix.'location', $prefix.'energy_class', $prefix.'energy_index' ) );

                                        if ( in_array( $fieldtype, $dtypes) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_available_simple_html', $fieldtype, $count_node, $field_values);
                                        } elseif ( in_array( $fieldtype, apply_filters( 'wp_realestate_list_tax_type', array( $prefix.'status', $prefix.'type', $prefix.'material', $prefix.'amenity', $prefix.'label' ) ) ) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_available_tax_html', $fieldtype, $count_node, $field_values);
                                        } elseif ( in_array($fieldtype, apply_filters( 'wp_realestate_list_files_type', array( $prefix.'featured_image', $prefix.'gallery', $prefix.'attachments' ) ) )) {
                                            $output .= apply_filters('wp_realestate_custom_field_available_files_html', $fieldtype, $count_node, $field_values);
                                        } else {
                                            $output .= apply_filters('wp_realestate_custom_field_available_'.$fieldtype.'_html', $fieldtype, $count_node, $field_values);
                                        }
                                        $disabled_fields[] = $fieldtype;
                                    } elseif ( in_array($fieldtype, $types) ) {
                                        $count_node ++;
                                        if ( in_array( $fieldtype, array('text', 'textarea', 'wysiwyg', 'number', 'url', 'email', 'checkbox') ) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_text_html', $fieldtype, $count_node, $custom_field_saved_data);
                                        } elseif ( in_array( $fieldtype, array('select', 'multiselect', 'radio') ) ) {
                                            $output .= apply_filters('wp_realestate_custom_field_opts_html', $fieldtype, $count_node, $custom_field_saved_data);
                                        } else {
                                            $output .= apply_filters('wp_realestate_custom_field_'.$fieldtype.'_html', $fieldtype, $count_node, $custom_field_saved_data);
                                        }
                                    }

                                    $output .= apply_filters('wp_realestate_custom_field_actions_html', $li_rand_id, $count_node, $fieldtype, $delete);
                                    $output .= '</li>';
                                }
                            } else {
                                foreach ($required_types as $field_values) {
                                    $count_node ++;
                                    $li_rand_id = rand(454, 999999);
                                    $output .= '<li class="custom-field-class-' . $li_rand_id . '">';
                                    $output .= apply_filters('wp_realestate_custom_field_available_simple_html', $field_values['id'], $count_node, $field_values);

                                    $output .= apply_filters('wp_realestate_custom_field_actions_html', $li_rand_id, $count_node, $field_values['id'], false);
                                    $output .= '</li>';
                                }
                            }
                            echo force_balance_tags($output);
                            ?>
                        </ul>

                        <button type="submit" class="button button-primary" name="updatePropertyFieldManager"><?php esc_html_e('Update', 'wp-realestate'); ?></button>

                        <div class="input-field-types">
                            <h3><?php esc_html_e('Create a custom field', 'wp-realestate'); ?></h3>
                            <div class="input-field-types-wrapper">
                                <select name="field-types" class="wp-realestate-field-types">
                                    <?php foreach ($default_fields as $group) { ?>
                                        <optgroup label="<?php echo esc_attr($group['title']); ?>">
                                            <?php foreach ($group['fields'] as $value => $label) { ?>
                                                <option value="<?php echo esc_attr($value); ?>"><?php echo $label; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </select>
                                <button type="button" class="button btn-add-field" data-randid="<?php echo esc_attr($rand_id); ?>"><?php esc_html_e('Create', 'wp-realestate'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wp-realestate-form-field-list wp-realestate-list">
                    <h3 class="title"><?php esc_html_e('Available Fields', 'wp-realestate'); ?></h3>
                    <?php if ( !empty($available_fields) ) { ?>
                        <ul>
                            <?php foreach ($available_fields as $field) { ?>
                                <li class="<?php echo esc_attr($field['id']); ?> <?php echo esc_attr(in_array($field['id'], $disabled_fields) ? 'disabled' : ''); ?>">
                                    <a class="wp-realestate-custom-field-add-available-field" data-fieldtype="<?php echo esc_attr($field['id']); ?>" data-randid="<?php echo esc_attr($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_attr($field['name']); ?>">
                                        <span class="icon-wrapper">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <?php echo esc_html($field['name']); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="clearfix" style="clear: both;"></div>
            </div>

            <script>
                var global_custom_field_counter = <?php echo intval($all_fields_name_count); ?>;
                jQuery(document).ready(function () {
                    
                    jQuery('#foo<?php echo esc_attr($rand_id); ?>').sortable({
                        group: "words",
                        animation: 150,
                        handle: ".field-intro",
                        cancel: ".form-group-wrapper"
                    });
                });
            </script>
        </form>
        <?php
    }

    public static function get_field_id($id, $fields) {
        if ( !empty($fields) && is_array($fields) ) {
            foreach ($fields as $field) {
                if ( $field['id'] == $id ) {
                    return $field;
                }
            }
        }
        return array();
    }

    public static function get_all_field_types() {
        $fields = apply_filters( 'wp_realestate_get_default_field_types', array(
            array(
                'title' => esc_html__('Direct Input', 'wp-realestate'),
                'fields' => array(
                    'text' => esc_html__('Text', 'wp-realestate'),
                    'textarea' => esc_html__('Textarea', 'wp-realestate'),
                    'wysiwyg' => esc_html__('WP Editor', 'wp-realestate'),
                    'date' => esc_html__('Date', 'wp-realestate'),
                    'number' => esc_html__('Number', 'wp-realestate'),
                    'url' => esc_html__('Url', 'wp-realestate'),
                    'email' => esc_html__('Email', 'wp-realestate'),
                )
            ),
            array(
                'title' => esc_html__('Choices', 'wp-realestate'),
                'fields' => array(
                    'select' => esc_html__('Select', 'wp-realestate'),
                    'multiselect' => esc_html__('Multiselect', 'wp-realestate'),
                    'checkbox' => esc_html__('Checkbox', 'wp-realestate'),
                    'radio' => esc_html__('Radio Buttons', 'wp-realestate'),
                )
            ),
            array(
                'title' => esc_html__('Form UI', 'wp-realestate'),
                'fields' => array(
                    'heading' => esc_html__('Heading', 'wp-realestate')
                )
            ),
            array(
                'title' => esc_html__('Others', 'wp-realestate'),
                'fields' => array(
                    'file' => esc_html__('File', 'wp-realestate')
                )
            ),
        ));
        
        return $fields;
    }

    public static function get_all_field_type_keys() {
        $fields = self::get_all_field_types();
        $return = array();
        foreach ($fields as $group) {
            foreach ($group['fields'] as $key => $value) {
                $return[] = $key;
            }
        }

        return apply_filters( 'wp_realestate_get_all_field_types', $return );
    }

    public static function get_all_types_fields_required() {
        $prefix = WP_REALESTATE_PROPERTY_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Property Title', 'wp-realestate' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'disable_check' => true,
                'required' => true,
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_input'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Property Description', 'wp-realestate' ),
                'id'                => $prefix . 'description',
                'type'              => 'textarea',
                'options'           => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                        'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
                        'toolbar2'                      => '',
                        'toolbar3'                      => '',
                        'toolbar4'                      => ''
                    ),
                    'quicktags' => false
                ),
                'disable_check' => true,
                'required' => true,
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Expiry Date', 'wp-realestate' ),
                'id'                => $prefix . 'expiry_date',
                'type'              => 'text_date',
                'date_format'       => 'Y-m-d',
                'disable_check' => true,
                'show_in_submit_form' => false,
                'show_in_admin_edit' => true,
            ),
            array(
                'name'              => __( 'Featured', 'wp-realestate' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured properties will be sticky during searches, and can be styled differently.', 'wp-realestate' ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_checkbox'),
                'show_compare'      => true
            ),
        );
        return apply_filters( 'wp-realestate-type-required-fields', $fields );
    }

    public static function get_all_types_fields_available() {
        $currency_symbol = wp_realestate_get_option('currency_symbol', '$');
        $area_unit = wp_realestate_get_option('measurement_unit_area', 'sqft');

        $prefix = WP_REALESTATE_PROPERTY_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Property ID', 'wp-realestate' ),
                'id'                => $prefix . 'property_id',
                'type'              => 'text',
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Year built', 'wp-realestate' ),
                'id'                => $prefix . 'year_built',
                'type'              => 'text',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_year_built_range_slider'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Featured Image', 'wp-realestate' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_realestate_file',
                'ajax'              => true,
                'multiple_files'    => false,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'jpg|jpeg|jpe', 'png' ),
            ),
            array(
                'name'              => __( 'Gallery', 'wp-realestate' ),
                'id'                => $prefix . 'gallery',
                'type'              => 'wp_realestate_file',
                'ajax'              => true,
                'multiple_files'          => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'jpg|jpeg|jpe', 'png' ),
            ),
            array(
                'name'              => __( 'Attachments', 'wp-realestate' ),
                'id'                => $prefix . 'attachments',
                'type'              => 'wp_realestate_file',
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'pdf', 'docx', 'doc' ),
                'show_compare'      => false
            ),

            // location
            array(
                'name'              => __( 'Location', 'wp-realestate' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpre_taxonomy_location',
                'taxonomy'          => 'property_location',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_location_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-realestate' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_input'),
                'show_compare'      => true
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Map Location', 'wp-realestate' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
            ),

            // price
            array(
                'name'              => sprintf(__( 'Price (%s)', 'wp-realestate' ), $currency_symbol),
                'id'                => $prefix . 'price',
                'type'              => 'text',
                'placeholder'       => __( 'e.g. 1000', 'wp-realestate' ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_property_price'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Price Prefix', 'wp-realestate' ),
                'id'                => $prefix . 'price_prefix',
                'type'              => 'text',
                'description'       => __('Any text shown before price (for example: from).', 'wp-realestate'),
            ),
            array(
                'name'              => __( 'Price Suffix', 'wp-realestate' ),
                'id'                => $prefix . 'price_suffix',
                'type'              => 'text',
                'description'       => __('Any text shown after price (for example: per night).', 'wp-realestate'),
            ),
            array(
                'name'              => __( 'Price Custom', 'wp-realestate' ),
                'id'                => $prefix . 'price_custom',
                'type'              => 'text',
                'description'       => __('Any text instead of price (for example: by agreement). Prefix and Suffix will be ignored.', 'wp-realestate'),
            ),

            // property detail
            array(
                'name'              => __( 'Rooms', 'wp-realestate' ),
                'id'                => $prefix . 'rooms',
                'type'              => 'text',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_number_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Beds', 'wp-realestate' ),
                'id'                => $prefix . 'beds',
                'type'              => 'text',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_number_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Baths', 'wp-realestate' ),
                'id'                => $prefix . 'baths',
                'type'              => 'text',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_number_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Garages', 'wp-realestate' ),
                'id'                => $prefix . 'garages',
                'type'              => 'text',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_number_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => sprintf(__( 'Home area (%s)', 'wp-realestate' ), $area_unit),
                'id'                => $prefix . 'home_area',
                'type'              => 'text',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_range_slider'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Lot dimensions', 'wp-realestate' ),
                'id'                => $prefix . 'lot_dimensions',
                'type'              => 'text',
                'description'       => __('e.g. 20x30, 20x30x40, 20x30x40x50', 'wp-realestate'),
            ),
            array(
                'name'              => sprintf(__( 'Lot area (%s)', 'wp-realestate' ), $area_unit),
                'id'                => $prefix . 'lot_area',
                'type'              => 'text',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_range_slider'),
                'show_compare'      => true
            ),

            array(
                'name'              => __( 'Video link', 'wp-realestate' ),
                'id'                => $prefix . 'video',
                'type'              => 'text',
                'description'       => __( 'Enter Youtube or Vimeo url.', 'wp-realestate' ),
            ),

            array(
                'name'              => __( 'Virtual Tour', 'wp-realestate' ),
                'id'                => $prefix . 'virtual_tour',
                'type'              => 'textarea_code',
                'description'       => __( 'Input iframe to show 360° Virtual Tour.', 'wp-realestate' ),
            ),

            array(
                'name'              => __( 'Parent property', 'wp-realestate' ),
                'desc'              => __( 'Useful for types like Condominium', 'wp-realestate' ),
                'id'                => $prefix . 'parent_property',
                'type'              => 'select',
                // 'multiple'          => true,
                // 'query_args'        => array(
                //     'post_type'         => array( 'property' ),
                //     'posts_per_page'    => -1,
                //     'author'            => get_current_user_id(),
                // )
            ),

            // Floor plans
            array(
                'name'              => __( 'Floor Plans', 'wp-realestate' ),
                'id'                => $prefix . 'floor_plans_group',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Floor Group {#}', 'wp-realestate' ),
                    'add_button'        => __( 'Add Floor', 'wp-realestate' ),
                    'remove_button'     => __( 'Remove Floor', 'wp-realestate' ),
                    'sortable'          => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'id'                => 'name',
                        'name'              => __( 'Name', 'wp-realestate' ),
                        'type'              => 'text',
                    ),
                    array(
                        'id'                => 'rooms',
                        'name'              => __( 'Rooms', 'wp-realestate' ),
                        'type'              => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        )
                    ),
                    array(
                        'id'                => 'baths',
                        'name'              => __( 'Baths', 'wp-realestate' ),
                        'type'              => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        )
                    ),
                    array(
                        'id'                => 'size',
                        'name'              => __( 'Size', 'wp-realestate' ),
                        'type'              => 'text',
                    ),
                    array(
                        'id'                => 'content',
                        'name'              => __( 'Content', 'wp-realestate' ),
                        'type'              => 'textarea',
                    ),
                    array(
                        'name'              => __( 'Preview Image', 'wp-realestate' ),
                        'id'                => 'image',
                        'type'              => 'wp_realestate_file',
                        'ajax'              => true,
                        'multiple_files'    => false,
                    ),
                ),
            ),

            // Taxonomies
            array(
                'name'              => __( 'Status', 'wp-realestate' ),
                'id'                => $prefix . 'status',
                'type'              => 'pw_taxonomy_select',
                'taxonomy'          => 'property_status',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Label', 'wp-realestate' ),
                'id'                => $prefix . 'label',
                'type'              => 'pw_taxonomy_select',
                'taxonomy'          => 'property_label',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Type', 'wp-realestate' ),
                'id'                => $prefix . 'type',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'property_type',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Material', 'wp-realestate' ),
                'id'                => $prefix . 'material',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'property_material',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Amenities', 'wp-realestate' ),
                'id'                => $prefix . 'amenity',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'property_amenity',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
                'show_compare'      => true
            ),
            
            
            // valuation group
            array(
                'name'              => __( 'Valuation', 'wp-realestate' ),
                'id'                => $prefix . 'valuation_group',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Group {#}', 'wp-realestate' ),
                    'add_button'        => __( 'Add Group', 'wp-realestate' ),
                    'remove_button'     => __( 'Remove Group', 'wp-realestate' ),
                    'sortable'          => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'id'                => 'valuation_key',
                        'name'              => __( 'Key', 'wp-realestate' ),
                        'type'              => 'text',
                    ),
                    array(
                        'id'                => 'valuation_value',
                        'name'              => __( 'Value', 'wp-realestate' ),
                        'type'              => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        )
                    ),
                ),
                'show_compare'      => true
            ),

            // Public facilities
            array(
                'name'              => __( 'Facilities', 'wp-realestate' ),
                'id'                => $prefix . 'public_facilities_group',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Group {#}', 'wp-realestate' ),
                    'add_button'        => __( 'Add Group', 'wp-realestate' ),
                    'remove_button'     => __( 'Remove Group', 'wp-realestate' ),
                    'sortable'          => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'id'                => 'public_facilities_key',
                        'name'              => __( 'Key', 'wp-realestate' ),
                        'type'              => 'text',
                    ),
                    array(
                        'id'                => 'public_facilities_value',
                        'name'              => __( 'Value', 'wp-realestate' ),
                        'type'              => 'text',
                    ),
                ),
                'show_compare'      => true
            ),

            array(
                'name'              => sprintf(__( 'Energy Class', 'wp-realestate' ), $area_unit),
                'id'                => $prefix . 'energy_class',
                'type'              => 'select',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_select'),
                'show_compare'      => true,
                'options'           => array(
                    'A+' => esc_html__('A+', 'wp-realestate'),
                    'A' => esc_html__('A', 'wp-realestate'),
                    'B' => esc_html__('B', 'wp-realestate'),
                    'C' => esc_html__('C', 'wp-realestate'),
                    'D' => esc_html__('D', 'wp-realestate'),
                    'E' => esc_html__('E', 'wp-realestate'),
                    'F' => esc_html__('F', 'wp-realestate'),
                    'G' => esc_html__('G', 'wp-realestate'),
                    'H' => esc_html__('H', 'wp-realestate'),
                )
            ),
            array(
                'name'              => sprintf(__( 'Energy Index in kWh/m2a', 'wp-realestate' ), $area_unit),
                'id'                => $prefix . 'energy_index',
                'type'              => 'text',
                'field_call_back' => array( 'WP_RealEstate_Abstract_Filter', 'filter_field_range_slider'),
                'show_compare'      => true
            ),
        );
        return apply_filters( 'wp-realestate-type-available-fields', $fields );
    }

    public static function get_custom_fields_data() {
        return apply_filters( 'wp-realestate-get-custom-fields-data', get_option('wp_realestate_fields_data', array()) );
    }

    public static function get_display_hooks() {
        $hooks = array(
            '' => esc_html__('Choose a position', 'wp-realestate'),
            'wp-realestate-single-property-description' => esc_html__('Single Property - Description', 'wp-realestate'),
            'wp-realestate-single-property-details' => esc_html__('Single Property - Details', 'wp-realestate'),
            'wp-realestate-single-property-amenities' => esc_html__('Single Property - Amenities Box', 'wp-realestate'),
            'wp-realestate-single-property-floor-plan' => esc_html__('Single Property - Floor Plans', 'wp-realestate'),
            'wp-realestate-single-property-contact-form' => esc_html__('Single Property - Contact Form', 'wp-realestate'),
            'wp-realestate-single-property-video' => esc_html__('Single Property - Video', 'wp-realestate'),
            'wp-realestate-single-property-agent-detail' => esc_html__('Single Property - Agent Details', 'wp-realestate'),
            'wp-realestate-single-property-facilities' => esc_html__('Single Property - Facilities', 'wp-realestate'),
            'wp-realestate-single-property-valuation' => esc_html__('Single Property - Valuation', 'wp-realestate'),
            'wp-realestate-single-property-attachments' => esc_html__('Single Property - Attachments', 'wp-realestate'),
        );
        return apply_filters( 'wp-realestate-get-custom-fields-display-hooks', $hooks );
    }

    public static function save() {
        if ( isset( $_POST['updatePropertyFieldManager'] ) ) {

            $custom_field_final_array = $counts = array();
            $field_index = 0;

            foreach ($_POST['wp-realestate-custom-fields-type'] as $field_type) {
                $custom_fields_id = isset($_POST['wp-realestate-custom-fields-id'][$field_index]) ? $_POST['wp-realestate-custom-fields-id'][$field_index] : '';
                $counter = 0;
                if ( isset($counts[$field_type]) ) {
                    $counter = $counts[$field_type];
                }
                $custom_field_final_array[] = self::custom_field_ready_array($counter, $field_type, $custom_fields_id);
                $counter++;
                $counts[$field_type] = $counter;
                $field_index++;
            }
            
            update_option('wp_realestate_fields_data', $custom_field_final_array);
            
        }
    }

    public static function custom_field_ready_array($array_counter = 0, $field_type = '', $custom_fields_id = '') {
        $custom_field_element_array = array();
        $custom_field_element_array['type'] = $field_type;
        if ( !empty($_POST["wp-realestate-custom-fields-{$field_type}"]) ) {
            foreach ($_POST["wp-realestate-custom-fields-{$field_type}"] as $field => $value) {
                if ( isset($value[$custom_fields_id]) ) {
                    $custom_field_element_array[$field] = $value[$custom_fields_id];
                } elseif ( isset($value[$array_counter]) ) {
                    $custom_field_element_array[$field] = $value[$array_counter];
                }
            }
        }
        return $custom_field_element_array;
    }

    public static function custom_field_html() {
        $fieldtype = $_POST['fieldtype'];
        $global_custom_field_counter = $_REQUEST['global_custom_field_counter'];
        $li_rand_id = rand(454, 999999);
        $html = '<li class="custom-field-class-' . $li_rand_id . '">';
        $types = self::get_all_field_type_keys();
        if ( in_array($fieldtype, $types) ) {
            if ( in_array( $fieldtype, array('text', 'textarea', 'wysiwyg', 'number', 'url', 'email', 'checkbox') ) ) {
                $html .= apply_filters( 'wp_realestate_custom_field_text_html', $fieldtype, $global_custom_field_counter, '' );
            } elseif ( in_array( $fieldtype, array('select', 'multiselect', 'radio') ) ) {
                $html .= apply_filters( 'wp_realestate_custom_field_opts_html', $fieldtype, $global_custom_field_counter, '' );
            } else {
                $html .= apply_filters('wp_realestate_custom_field_'.$fieldtype.'_html', $fieldtype, $global_custom_field_counter, '');
            }
        }
        // action btns
        $html .= apply_filters('wp_realestate_custom_field_actions_html', $li_rand_id, $global_custom_field_counter, $fieldtype);
        $html .= '</li>';
        echo json_encode( array('html' => $html) );
        wp_die();
    }

    public static function custom_field_available_html() {
        $prefix = WP_REALESTATE_PROPERTY_PREFIX;
        $fieldtype = $_POST['fieldtype'];
        $global_custom_field_counter = $_REQUEST['global_custom_field_counter'];
        $li_rand_id = rand(454, 999999);
        $html = '<li class="custom-field-class-' . $li_rand_id . '">';
        $types = self::get_all_types_fields_available();

        $dfield_values = self::get_field_id($fieldtype, $types);
        if ( !empty($dfield_values) ) {

            $dtypes = apply_filters( 'wp_realestate_list_simple_type', array( $prefix.'featured', $prefix.'year_built', $prefix.'address', $prefix.'property_id', $prefix.'map_location', $prefix.'price', $prefix.'price_prefix', $prefix.'price_suffix', $prefix.'price_custom', $prefix.'rooms', $prefix.'beds', $prefix.'baths', $prefix.'garages', $prefix.'home_area', $prefix.'lot_dimensions', $prefix.'lot_area', $prefix.'video', $prefix.'virtual_tour', $prefix.'parent_property', $prefix.'valuation_group', $prefix.'public_facilities_group', $prefix.'floor_plans_group', $prefix.'location', $prefix.'energy_class', $prefix.'energy_index' ) );

            if ( in_array( $fieldtype, $dtypes ) ) {
                $html .= apply_filters( 'wp_realestate_custom_field_available_simple_html', $fieldtype, $global_custom_field_counter, $dfield_values );
            } elseif ( in_array( $fieldtype, apply_filters( 'wp_realestate_list_tax_type', array($prefix.'status', $prefix.'type', $prefix.'material', $prefix.'amenity', $prefix.'label') ) ) ) {
                
                $html .= apply_filters( 'wp_realestate_custom_field_available_tax_html', $fieldtype, $global_custom_field_counter, $dfield_values );

            } elseif ( in_array( $fieldtype, apply_filters( 'wp_realestate_list_file_type', array($prefix.'featured_image', $prefix.'gallery', $prefix.'attachments') ) ) ) {
                
                $html .= apply_filters( 'wp_realestate_custom_field_available_file_html', $fieldtype, $global_custom_field_counter, $dfield_values );
            
            } else {
                $html .= apply_filters( 'wp_realestate_custom_field_available_'.$fieldtype.'_html', $fieldtype, $global_custom_field_counter, $dfield_values );
            }
        }

        // action btns
        $html .= apply_filters('wp_realestate_custom_field_actions_html', $li_rand_id, $global_custom_field_counter, $fieldtype);
        $html .= '</li>';
        echo json_encode(array('html' => $html));
        wp_die();
    }

}

WP_RealEstate_Fields_Manager::init();


