<?php
/**
 * widget base for apus framework
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Framework
 */

if (!class_exists('Apus_Megamenu_Config')) {
    class Apus_Megamenu_Config extends Walker_Nav_Menu_Edit  {
        
        public function start_lvl( &$output, $depth = 0, $args = array() ) {}
        
        public function end_lvl( &$output, $depth = 0, $args = array() ) {}

        public function start_el(&$output, $item, $depth=0, $args=array(),$current_object_id=0) {

            $item_output = '';

            parent::start_el( $item_output, $item, $depth, $args, $current_object_id );

            $output .= preg_replace(
                // NOTE: Check this regex from time to time!
                '/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
                $this->get_fields( $item, $depth, $args ),
                $item_output
            );

        }

        protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
            ob_start();

            /**
             * Get menu item custom fields from plugins/themes
             *
             * @since 0.1.0
             * @since 1.0.0 Pass correct parameters.
             *
             * @param int    $item_id  Menu item ID.
             * @param object $item     Menu item data object.
             * @param int    $depth    Depth of menu item. Used for padding.
             * @param array  $args     Menu item args.
             * @param int    $id       Nav menu ID.
             *
             * @return string Custom fields HTML.
             */
            
            if( $depth == 0 ) {
                do_action('apus_megamenu_item_config_toplevel', $item->ID, $item, $depth, $args, $id );
            }

            do_action('apus_megamenu_item_config', $item->ID, $item, $depth, $args, $id );

            return ob_get_clean();
        }
    }
}