<?php
/**
 * functions for apus framework
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Framework
 */

function apus_framework_add_param() {
    if ( function_exists('vc_add_shortcode_param') ) {
        vc_add_shortcode_param('googlemap', 'apus_framework_parram_googlemap', get_template_directory_uri().'/js/admin/googlemap.js');
    }
}

function apus_framework_parram_googlemap($settings, $value) {
    return apply_filters( 'apus_framework_parram_googlemap', '
        <div id="apus-element-map">
            <div class="map_canvas" style="height:200px;"></div>

            <div class="vc_row-fluid googlefind">
                <input id="geocomplete" type="text" class="apus-location" placeholder="Type in an address" size="90" />
                <button class="button-primary find">'.esc_html__('Find', 'apus-framework').'</button>
            </div>

            <div class="row-fluid mapdetail">
                <div class="span6">
                    <div class="wpb_element_label">'.esc_html__('Latitude', 'apus-framework').'</div>
                    <input name="lat" class="apus-latgmap" type="text" value="">
                </div>

                <div class="span6">
                    <div class="wpb_element_label">'.esc_html__('Longitude', 'apus-framework').'</div>
                    <input name="lng" class="apus-lnggmap" type="text" value="">
                </div>
            </div>
        </div>
        ');
}

function apus_framework_register_post_types() {
    $post_types = apply_filters( 'apus_framework_register_post_types', array('footer', 'brand', 'testimonial', 'megamenu') );
    if ( !empty($post_types) ) {
        foreach ($post_types as $post_type) {
            if ( file_exists( APUS_FRAMEWORK_DIR . 'classes/post-types/'.$post_type.'.php' ) ) {
                require APUS_FRAMEWORK_DIR . 'classes/post-types/'.$post_type.'.php';
            }
        }
    }
}

function apus_framework_widget_init() {
    $widgets = apply_filters( 'apus_framework_register_widgets', array() );
    if ( !empty($widgets) ) {
        foreach ($widgets as $widget) {
            if ( file_exists( APUS_FRAMEWORK_DIR . 'classes/widgets/'.$widget.'.php' ) ) {
                require APUS_FRAMEWORK_DIR . 'classes/widgets/'.$widget.'.php';
            }
        }
    }
}

function apus_framework_reg_widget($class_name) {
    register_widget($class_name);
}

function apus_framework_get_widget_locate( $name, $plugin_dir = APUS_FRAMEWORK_DIR ) {
    $template = '';
    
    // Child theme
    if ( ! $template && ! empty( $name ) && file_exists( get_stylesheet_directory() . "/widgets/{$name}" ) ) {
        $template = get_stylesheet_directory() . "/widgets/{$name}";
    }

    // Original theme
    if ( ! $template && ! empty( $name ) && file_exists( get_template_directory() . "/widgets/{$name}" ) ) {
        $template = get_template_directory() . "/widgets/{$name}";
    }

    // Plugin
    if ( ! $template && ! empty( $name ) && file_exists( $plugin_dir . "/templates/widgets/{$name}" ) ) {
        $template = $plugin_dir . "/templates/widgets/{$name}";
    }

    // Nothing found
    if ( empty( $template ) ) {
        throw new Exception( "Template /templates/widgets/{$name} in plugin dir {$plugin_dir} not found." );
    }

    return $template;
}

function apus_framework_display_svg_image( $url, $class = '', $wrap_as_img = true, $attachment_id = null ) {
    if ( ! empty( $url ) && is_string( $url ) ) {

        // we try to inline svgs
        if ( substr( $url, - 4 ) === '.svg' ) {

            //first let's see if we have an attachment and inline it in the safest way - with readfile
            //include is a little dangerous because if one has short_open_tags active, the svg header that starts with <? will be seen as PHP code
            if ( ! empty( $attachment_id ) && false !== @readfile( get_attached_file( $attachment_id ) ) ) {
                //all good
            } elseif ( false !== ( $svg_code = get_transient( md5( $url ) ) ) ) {
                //now try to get the svg code from cache
                echo $svg_code;
            } else {

                //if not let's get the file contents using WP_Filesystem
                require_once( ABSPATH . 'wp-admin/includes/file.php' );

                WP_Filesystem();

                global $wp_filesystem;
                
                $svg_code = $wp_filesystem->get_contents( $url );

                if ( ! empty( $svg_code ) ) {
                    set_transient( md5( $url ), $svg_code, 12 * HOUR_IN_SECONDS );

                    echo $svg_code;
                }
            }

        } elseif ( $wrap_as_img ) {

            if ( ! empty( $class ) ) {
                $class = ' class="' . $class . '"';
            }

            echo '<img src="' . $url . '"' . $class . ' alt="" />';

        } else {
            echo $url;
        }
    }
}

function apus_framework_get_file_contents($url, $use_include_path, $context) {
    return @file_get_contents($url, false, $context);
}

function apus_framework_scrape_instagram( $username ) {

    $username = strtolower( $username );
    $username = str_replace( '@', '', $username );

    if ( false === ( $instagram = get_transient( 'instagram-a5-'.sanitize_title_with_dashes( $username ) ) ) ) {

        $remote = wp_remote_get( 'http://instagram.com/'.trim( $username ) );

        if ( is_wp_error( $remote ) )
            return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'apus-framework' ) );

        if ( 200 != wp_remote_retrieve_response_code( $remote ) )
            return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'apus-framework' ) );

        $shards = explode( 'window._sharedData = ', $remote['body'] );
        $insta_json = explode( ';</script>', $shards[1] );
        $insta_array = json_decode( $insta_json[0], TRUE );
        
        if ( ! $insta_array )
            return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'apus-framework' ) );

        if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
            $images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
        } else {
            return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'apus-framework' ) );
        }

        if ( ! is_array( $images ) )
            return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'apus-framework' ) );

        $instagram = array();

        foreach ( $images as $dimage ) {
            $image = $dimage['node'];

            $image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
            $image['display_src'] = preg_replace( '/^https?\:/i', '', $image['display_url'] );

            // handle both types of CDN url
            if ( ( strpos( $image['thumbnail_src'], 's640x640' ) !== false ) ) {
                $image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
                $image['small'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
            } else {
                $urlparts = wp_parse_url( $image['thumbnail_src'] );
                $pathparts = explode( '/', $urlparts['path'] );
                array_splice( $pathparts, 3, 0, array( 's160x160' ) );
                $image['thumbnail'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
                $pathparts[3] = 's320x320';
                $image['small'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
            }
            if ( is_array($image['thumbnail_resources']) ) {
                foreach ($image['thumbnail_resources'] as $resource) {
                    if ( $resource['config_width'] == 150 ) {
                        $image['small'] = $resource['src'];
                    } elseif( $resource['config_width'] == 480 ) {
                        $image['thumbnail'] = $resource['src'];
                    }
                }
            }

            $image['large'] = $image['thumbnail_src'];
            if ( empty($image['small']) ) {
                $image['small'] = $image['thumbnail_src'];
            }
            if ( empty($image['thumbnail']) ) {
                $image['thumbnail'] = $image['thumbnail_src'];
            }

            if ( $image['is_video'] == true ) {
                $type = 'video';
            } else {
                $type = 'image';
            }

            $caption = esc_html__( 'Instagram Image', 'apus-framework' );
            if ( ! empty( $image['caption'] ) ) {
                $caption = $image['caption'];
            }
    
            $instagram[] = array(
                'description'   => $caption,
                'link'          => trailingslashit( '//instagram.com/p/' . $image['shortcode'] ),
                'time'          => $image['taken_at_timestamp'],
                'comments'      => $image['edge_media_to_comment']['count'],
                'likes'         => $image['edge_liked_by']['count'],
                'thumbnail'     => $image['thumbnail'],
                'small'         => $image['small'],
                'large'         => $image['large'],
                'original'      => $image['display_src'],
                'type'          => $type
            );
        }
        // do not set an empty transient - should help catch private or empty accounts
        if ( ! empty( $instagram ) ) {
            $instagram = base64_encode( serialize( $instagram ) );
            set_transient( 'instagram-a5-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
        }
    }

    if ( ! empty( $instagram ) ) {
        return unserialize( base64_decode( $instagram ) );
    } else {
        return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'apus-framework' ) );
    }
}

function apus_framework_images_only( $media_item ) {
    if ( $media_item['type'] == 'image' )
        return true;
    return false;
}

function apus_framework_encode( $string ) {
    return base64_encode($string);
}

function apus_framework_decode( $string ) {
    return base64_decode($string);
}

function apus_framework_image_srcset($size_array, $src, $image_meta, $attachment_id) {
    return wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment_id );
}