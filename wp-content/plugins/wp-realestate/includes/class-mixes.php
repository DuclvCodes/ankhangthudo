<?php
/**
 * Mixes
 *
 * @package    wp-realestate
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_Mixes {
	
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'track_post_views' ) );

		add_action( 'login_form', array( __CLASS__, 'social_login_before' ), 1 );
		add_action( 'login_form', array( __CLASS__, 'social_login_after' ), 30 );

        add_filter( 'wp_realestate_filter_distance_type', array( __CLASS__, 'set_distance_type' ), 10 );
        
        add_filter( 'wp_realestate_cmb2_field_taxonomy_location_number', array( __CLASS__, 'set_location_number' ), 10 );
        add_filter( 'wp_realestate_cmb2_field_taxonomy_location_field_name_1', array( __CLASS__, 'set_first_location_label' ), 10 );
        add_filter( 'wp_realestate_cmb2_field_taxonomy_location_field_name_2', array( __CLASS__, 'set_second_location_label' ), 10 );
        add_filter( 'wp_realestate_cmb2_field_taxonomy_location_field_name_3', array( __CLASS__, 'set_third_location_label' ), 10 );
        add_filter( 'wp_realestate_cmb2_field_taxonomy_location_field_name_4', array( __CLASS__, 'set_fourth_location_label' ), 10 );
	}

	public static function set_post_views($post_id, $prefix) {
	    $count_key = $prefix.'views_count';
	    $count = get_post_meta($post_id, $count_key, true);
	    if ( $count == '' ) {
	        $count = 0;
	        delete_post_meta($post_id, $count_key);
	        add_post_meta($post_id, $count_key, '0');
	    } else {
	        $count++;
	        update_post_meta($post_id, $count_key, $count);
	    }
	}

	public static function track_post_views() {
	    if ( is_singular('property') || is_singular('agent') || is_singular('agency') ) {
	        global $post;
	        $post_id = $post->ID;
	        $prefix = WP_REALESTATE_PROPERTY_PREFIX;
	        if ( is_singular('agent') ) {
	        	$prefix = WP_REALESTATE_AGENT_PREFIX;
	        } elseif ( is_singular('agency') ) {
                $prefix = WP_REALESTATE_AGENCY_PREFIX;
            }
		    self::set_post_views($post_id, $prefix);
		}
	}
	
	/**
	 * Formats number by currency settings
	 *
	 * @access public
	 * @param $price
	 * @return bool|string
	 */
	public static function format_number( $price ) {
		if ( empty( $price ) || ! is_numeric( $price ) ) {
			return 0;
		}
            
		$money_decimals = wp_realestate_get_option('money_decimals');
		$money_thousands_separator = wp_realestate_get_option('money_thousands_separator');
		$money_dec_point = wp_realestate_get_option('money_dec_point');

		$price_parts_dot = explode( '.', $price );
		$price_parts_col = explode( ',', $price );

		if ( count( $price_parts_dot ) > 1 || count( $price_parts_col ) > 1 ) {
			$decimals = ! empty( $money_decimals ) ? $money_decimals : '0';
		} else {
			$decimals = 0;
		}

		$dec_point = ! empty( $money_dec_point ) ? $money_dec_point : '.';
		$thousands_separator = ! empty( $money_thousands_separator ) ? $money_thousands_separator : '';

		$price = number_format( $price, $decimals, $dec_point, $thousands_separator );

		return $price;
	}

	public static function is_allowed_to_remove( $user_id, $item_id ) {
		$item = get_post( $item_id );

		if ( ! empty( $item->post_author ) ) {
			return $item->post_author == $user_id ;
		}

		return false;
	}
	
	public static function redirect($redirect_url) {
		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/' );
		}

		wp_redirect( $redirect_url );
		exit();
	}

	public static function sort_array_by_priority( $a, $b ) {
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
	}
	
      
	public static function get_image_mime_types() {
		return apply_filters( 'wp-realestate-get-image-mime-types', array(
			'jpg'         => 'image/jpeg',
			'jpeg'        => 'image/jpeg',
			'jpe'         => 'image/jpeg',
			'gif'         => 'image/gif',
			'png'         => 'image/png',
			'bmp'         => 'image/bmp',
			'tif|tiff'    => 'image/tiff',
			'ico'         => 'image/x-icon',
		));
	}

	public static function get_socials_network() {
		return apply_filters( 'wp-realestate-get-socials-network', array(
			'fab fa-facebook-f' => esc_html__('Facebook', 'wp-realestate'),
			'fab fa-twitter' => esc_html__('Twitter', 'wp-realestate'),
			'fab fa-linkedin-in' => esc_html__('Linkedin', 'wp-realestate'),
			'fab fa-dribbble' => esc_html__('Dribbble', 'wp-realestate'),
		));
	}

	public static function get_all_countries() {
        $countries = array(
            'af' => 'Afghanistan',
            'ax' => 'Islands',
            'al' => 'Albania',
            'dz' => 'Algeria',
            'as' => 'American Samoa',
            'ad' => 'Andorra',
            'ao' => 'Angola',
            'ai' => 'Anguilla',
            'aq' => 'Antarctica',
            'ag' => 'Antigua and Barbuda',
            'ar' => 'Argentina',
            'am' => 'Armenia',
            'aw' => 'Aruba',
            'au' => 'Australia',
            'at' => 'Austria',
            'az' => 'Azerbaijan',
            'bs' => 'Bahamas',
            'bh' => 'Bahrain',
            'bd' => 'Bangladesh',
            'bb' => 'Barbados',
            'by' => 'Belarus',
            'be' => 'Belgium',
            'bz' => 'Belize',
            'bj' => 'Benin',
            'bm' => 'Bermuda',
            'bt' => 'Bhutan',
            'bo' => 'Bolivia, Plurinational State of',
            'bq' => 'Bonaire, Sint Eustatius and Saba',
            'ba' => 'Bosnia and Herzegovina',
            'bw' => 'Botswana',
            'bv' => 'Bouvet Island',
            'br' => 'Brazil',
            'io' => 'British Indian Ocean Territory',
            'bn' => 'Brunei Darussalam',
            'bg' => 'Bulgaria',
            'bf' => 'Burkina Faso',
            'bi' => 'Burundi',
            'kh' => 'Cambodia',
            'cm' => 'Cameroon',
            'ca' => 'Canada',
            'cv' => 'Cape Verde',
            'ky' => 'Cayman Islands',
            'cf' => 'Central African Republic',
            'td' => 'Chad',
            'cl' => 'Chile',
            'cn' => 'China',
            'cx' => 'Christmas Island',
            'cc' => 'Cocos (Keeling) Islands',
            'co' => 'Colombia',
            'km' => 'Comoros',
            'cg' => 'Congo',
            'cd' => 'Congo, the Democratic Republic of the',
            'ck' => 'Cook Islands',
            'cr' => 'Costa Rica',
            'ci' => 'Côte d\'Ivoire',
            'hr' => 'Croatia',
            'cu' => 'Cuba',
            'cw' => 'Curaçao',
            'cy' => 'Cyprus',
            'cz' => 'Czech Republic',
            'dk' => 'Denmark',
            'dj' => 'Djibouti',
            'dm' => 'Dominica',
            'do' => 'Dominican Republic',
            'ec' => 'Ecuador',
            'eg' => 'Egypt',
            'sv' => 'El Salvador',
            'gq' => 'Equatorial Guinea',
            'er' => 'Eritrea',
            'ee' => 'Estonia',
            'et' => 'Ethiopia',
            'fk' => 'Falkland Islands (Malvinas)',
            'fo' => 'Faroe Islands',
            'fj' => 'Fiji',
            'fi' => 'Finland',
            'fr' => 'France',
            'gf' => 'French Guiana',
            'pf' => 'French Polynesia',
            'tf' => 'French Southern Territories',
            'ga' => 'Gabon',
            'gm' => 'Gambia',
            'ge' => 'Georgia',
            'de' => 'Germany',
            'gh' => 'Ghana',
            'gi' => 'Gibraltar',
            'gr' => 'Greece',
            'gl' => 'Greenland',
            'gd' => 'Grenada',
            'gp' => 'Guadeloupe',
            'gu' => 'Guam',
            'gt' => 'Guatemala',
            'gg' => 'Guernsey',
            'gn' => 'Guinea',
            'gw' => 'Guinea-Bissau',
            'gy' => 'Guyana',
            'ht' => 'Haiti',
            'hm' => 'Heard Island and McDonald Islands',
            'va' => 'Holy See (Vatican City State)',
            'hn' => 'Honduras',
            'hk' => 'Hong Kong',
            'hu' => 'Hungary',
            'is' => 'Iceland',
            'in' => 'India',
            'id' => 'Indonesia',
            'ir' => 'Iran, Islamic Republic of',
            'iq' => 'Iraq',
            'ie' => 'Ireland',
            'im' => 'Isle of Man',
            'il' => 'Israel',
            'it' => 'Italy',
            'jm' => 'Jamaica',
            'jp' => 'Japan',
            'je' => 'Jersey',
            'jo' => 'Jordan',
            'kz' => 'Kazakhstan',
            'ke' => 'Kenya',
            'ki' => 'Kiribati',
            'kp' => 'Korea, Democratic People\'s Republic of',
            'kr' => 'Korea, Republic of',
            'kw' => 'Kuwait',
            'kg' => 'Kyrgyzstan',
            'la' => 'Lao People\'s Democratic Republic',
            'lv' => 'Latvia',
            'lb' => 'Lebanon',
            'ls' => 'Lesotho',
            'lr' => 'Liberia',
            'ly' => 'Libya',
            'li' => 'Liechtenstein',
            'lt' => 'Lithuania',
            'lu' => 'Luxembourg',
            'mo' => 'Macao',
            'mk' => 'Macedonia, the Former Yugoslav Republic of',
            'mg' => 'Madagascar',
            'mw' => 'Malawi',
            'my' => 'Malaysia',
            'mv' => 'Maldives',
            'ml' => 'Mali',
            'mt' => 'Malta',
            'mh' => 'Marshall Islands',
            'mq' => 'Martinique',
            'mr' => 'Mauritania',
            'mu' => 'Mauritius',
            'yt' => 'Mayotte',
            'mx' => 'Mexico',
            'fm' => 'Micronesia, Federated States of',
            'md' => 'Moldova, Republic of',
            'mc' => 'Monaco',
            'mn' => 'Mongolia',
            'me' => 'Montenegro',
            'ms' => 'Montserrat',
            'ma' => 'Morocco',
            'mz' => 'Mozambique',
            'mm' => 'Myanmar',
            'na' => 'Namibia',
            'nr' => 'Nauru',
            'np' => 'Nepal',
            'nl' => 'Netherlands',
            'nc' => 'New Caledonia',
            'nz' => 'New Zealand',
            'ni' => 'Nicaragua',
            'ne' => 'Niger',
            'ng' => 'Nigeria',
            'nu' => 'Niue',
            'nf' => 'Norfolk Island',
            'mp' => 'Northern Mariana Islands',
            'no' => 'Norway',
            'om' => 'Oman',
            'pk' => 'Pakistan',
            'pw' => 'Palau',
            'ps' => 'Palestine, State of',
            'pa' => 'Panama',
            'pg' => 'Papua New Guinea',
            'py' => 'Paraguay',
            'pe' => 'Peru',
            'ph' => 'Philippines',
            'pn' => 'Pitcairn',
            'pl' => 'Poland',
            'pt' => 'Portugal',
            'pr' => 'Puerto Rico',
            'qa' => 'Qatar',
            're' => 'Réunion',
            'ro' => 'Romania',
            'ru' => 'Russian Federation',
            'rw' => 'Rwanda',
            'bl' => 'Saint Barthélemy',
            'sh' => 'Saint Helena, Ascension and Tristan da Cunha',
            'kn' => 'Saint Kitts and Nevis',
            'lc' => 'Saint Lucia',
            'mf' => 'Saint Martin (French part)',
            'pm' => 'Saint Pierre and Miquelon',
            'vc' => 'Saint Vincent and the Grenadines',
            'ws' => 'Samoa',
            'sm' => 'San Marino',
            'st' => 'Sao Tome and Principe',
            'sa' => 'Saudi Arabia',
            'sn' => 'Senegal',
            'rs' => 'Serbia',
            'sc' => 'Seychelles',
            'sl' => 'Sierra Leone',
            'sg' => 'Singapore',
            'sx' => 'Sint Maarten (Dutch part)',
            'sk' => 'Slovakia',
            'si' => 'Slovenia',
            'sb' => 'Solomon Islands',
            'so' => 'Somalia',
            'za' => 'South Africa',
            'gs' => 'South Georgia and the South Sandwich Islands',
            'ss' => 'South Sudan',
            'es' => 'Spain',
            'lk' => 'Sri Lanka',
            'sd' => 'Sudan',
            'sr' => 'Suriname',
            'sj' => 'Svalbard and Jan Mayen',
            'sz' => 'Swaziland',
            'se' => 'Sweden',
            'ch' => 'Switzerland',
            'sy' => 'Syrian Arab Republic',
            'tw' => 'Taiwan, Province of China',
            'tj' => 'Tajikistan',
            'tz' => 'Tanzania, United Republic of',
            'th' => 'Thailand',
            'tl' => 'Timor-Leste',
            'tg' => 'Togo',
            'tk' => 'Tokelau',
            'to' => 'Tonga',
            'tt' => 'Trinidad and Tobago',
            'tn' => 'Tunisia',
            'tr' => 'Turkey',
            'tm' => 'Turkmenistan',
            'tc' => 'Turks and Caicos Islands',
            'tv' => 'Tuvalu',
            'ug' => 'Uganda',
            'ua' => 'Ukraine',
            'ae' => 'United Arab Emirates',
            'gb' => 'United Kingdom',
            'us' => 'United States',
            'um' => 'United States Minor Outlying Islands',
            'uy' => 'Uruguay',
            'uz' => 'Uzbekistan',
            'vu' => 'Vanuatu',
            've' => 'Venezuela, Bolivarian Republic of',
            'vn' => 'Viet Nam',
            'vg' => 'Virgin Islands, British',
            'vi' => 'Virgin Islands, U.S.',
            'wf' => 'Wallis and Futuna',
            'eh' => 'Western Sahara',
            'ye' => 'Yemen',
            'zm' => 'Zambia',
            'zw' => 'Zimbabwe'
        );

        return apply_filters( 'wp-realestate-default-countries', $countries );
    }

	public static function get_properties_page_url() {
		if ( is_post_type_archive('property') ) {
			$url = get_post_type_archive_link( 'property' );
        } elseif (is_tax()) {
            $url = '';
            $taxs = ['type', 'status', 'material', 'location', 'label', 'amenity'];
            foreach ($taxs as $tax) {
                if ( is_tax('property_'.$tax) ) {
                    global $wp_query;
                    $term = $wp_query->queried_object;
                    if ( isset( $term->slug) ) {
                        $url = get_term_link($term, 'property_'.$tax);
                    }
                }
            }
		} else {
			global $post;
			if ( is_page() && is_object($post) && basename( get_page_template() ) == 'page-properties.php' ) {
				$url = get_permalink($post->ID);
			} else {
				$properties_page_id = wp_realestate_get_option('properties_page_id');
				if ( $properties_page_id ) {
					$url = get_permalink($properties_page_id);
				} else {
					$url = get_post_type_archive_link( 'property' );
				}
			}
		}
		return apply_filters( 'wp-realestate-get-properties-page-url', $url );
	}

	public static function get_agents_page_url() {
		if ( is_post_type_archive('agent') ) {
			$url = get_post_type_archive_link( 'agent' );
		} elseif (is_tax()) {
            $url = '';
            $taxs = ['category', 'location'];
            foreach ($taxs as $tax) {
                if ( is_tax('agent_'.$tax) ) {
                    global $wp_query;
                    $term = $wp_query->queried_object;
                    if ( isset( $term->slug) ) {
                        $url = get_term_link($term, 'agent_'.$tax);
                    }
                }
            }
        } else {
			global $post;
			if ( is_page() && is_object($post) && basename( get_page_template() ) == 'page-agents.php' ) {
				$url = get_permalink($post->ID);
			} else {
				$agents_page_id = wp_realestate_get_option('agents_page_id');
				if ( $agents_page_id ) {
					$url = get_permalink($agents_page_id);
				} else {
					$url = get_post_type_archive_link( 'agent' );
				}
			}
		}
		return apply_filters( 'wp-realestate-get-agents-page-url', $url );
	}

    public static function get_agencies_page_url() {
        if ( is_post_type_archive('agency') ) {
            $url = get_post_type_archive_link( 'agency' );
        } elseif (is_tax()) {
            $url = '';
            $taxs = ['category', 'location'];
            foreach ($taxs as $tax) {
                if ( is_tax('agency_'.$tax) ) {
                    global $wp_query;
                    $term = $wp_query->queried_object;
                    if ( isset( $term->slug) ) {
                        $url = get_term_link($term, 'agency_'.$tax);
                    }
                }
            }
        } else {
            global $post;
            if ( is_page() && is_object($post) && basename( get_page_template() ) == 'page-agencies.php' ) {
                $url = get_permalink($post->ID);
            } else {
                $agencies_page_id = wp_realestate_get_option('agencies_page_id');
                if ( $agencies_page_id ) {
                    $url = get_permalink($agencies_page_id);
                } else {
                    $url = get_post_type_archive_link( 'agency' );
                }
            }
        }
        return apply_filters( 'wp-realestate-get-agencies-page-url', $url );
    }

	public static function custom_pagination( $args = array() ) {
        global $wp_rewrite;
        
        $args = wp_parse_args( $args, array(
            'prev_text' => '<i class="flaticon-left-arrow"></i>'.esc_html__('Prev', 'wp-realestate'),
            'next_text' => esc_html__('Next','wp-realestate').'<i class="flaticon-right-arrow"></i>',
            'max_num_pages' => 10,
            'echo' => true,
            'class' => '',
        ));

        if ( !empty($args['wp_query']) ) {
            $wp_query = $args['wp_query'];
        } else {
            global $wp_query;
        }

        if ( $wp_query->max_num_pages < 2 ) {
            return;
        }

        $pages = $args['max_num_pages'];

        $current = !empty($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 1 ? $wp_query->query_vars['paged'] : 1;
        if ( empty($pages) ) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if ( !$pages ) {
                $pages = 1;
            }
        }
        $pagination = array(
            'base' => @add_query_arg('paged','%#%'),
            'format' => '',
            'total' => $pages,
            'current' => $current,
            'prev_text' => $args['prev_text'],
            'next_text' => $args['next_text'],
            'type' => 'array'
        );

        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $query_args   = array();
        $url_parts    = explode( '?', $pagenum_link );

        if ( isset( $url_parts[1] ) ) {
            wp_parse_str( $url_parts[1], $query_args );
        }

        $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
        $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

        $format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

        $add_args = array();
        if ( !empty($query_args) ) {
            foreach ($query_args as $key => $value) {
                if ( is_array($value) ) {
                    $add_args[$key] = array_map( 'urlencode', $value );
                } else {
                    $add_args[$key] = $value;
                }
            }
        }

        $pagination['base'] = $pagenum_link;
        $pagination['format'] = $format;
        $pagination['add_args'] = $add_args;
        

        $sq = '';
        if ( isset($_GET['s']) ) {
            $cq = $_GET['s'];
            $sq = str_replace(" ", "+", $cq);
        }
        
        if ( !empty($wp_query->query_vars['s']) && isset($_GET['s']) ) {
            $pagination['add_args'] = array( 's' => $sq);
        }
        $pagination = apply_filters( 'wp-realestate-custom-pagination', $pagination );

        $paginations = paginate_links( $pagination );
        $output = '';
        if ( !empty($paginations) ) {
            $output .= '<ul class="pagination '.esc_attr( $args["class"] ).'">';
                foreach ($paginations as $key => $pg) {
                    $output .= '<li>'. $pg .'</li>';
                }
            $output .= '</ul>';
        }
        
        if ( $args["echo"] ) {
            echo wp_kses_post($output);
        } else {
            return $output;
        }
    }

    public static function paginate_links( $args = array() ) {
        global $wp_rewrite;

        $defaults = array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'total' => 1,
            'current' => 1,
            'add_fragment' => '#comments',
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'type'      => 'list',
            'end_size'  => 3,
            'mid_size'  => 3
        );
        if ( $wp_rewrite->using_permalinks() )
            $defaults['base'] = user_trailingslashit(trailingslashit(get_permalink()) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged');

        $args = wp_parse_args( $args, $defaults );
        ?>
        <nav class="manager-pagination pagination-links">
            <?php echo paginate_links( $args ); ?>
        </nav>
        <?php
    }

    public static function query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
		if ( is_null( $values ) ) {
			$values = $_GET; // WPCS: input var ok, CSRF ok.
		} elseif ( is_string( $values ) ) {
			$url_parts = wp_parse_url( $values );
			$values    = array();

			if ( ! empty( $url_parts['query'] ) ) {
				parse_str( $url_parts['query'], $values );
			}
		}
		$html = '';

		foreach ( $values as $key => $value ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			if ( $current_key ) {
				$key = $current_key . '[' . $key . ']';
			}
			if ( is_array( $value ) ) {
				$html .= self::query_string_form_fields( $value, $exclude, $key, true );
			} else {
				$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
			}
		}

		if ( $return ) {
			return $html;
		}

		echo $html; // WPCS: XSS ok.
	}

	public static function is_ajax_request() {
	    if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
	        return true;
	    }
	    return false;
	}

	public static function get_full_current_url() {
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
		    $link = "https"; 
		} else {
		    $link = "http"; 
		}
		  
		// Here append the common URL characters. 
		$link .= "://"; 
		  
		// Append the host(domain name, ip) to the URL. 
		$link .= $_SERVER['HTTP_HOST']; 
		  
		// Append the requested resource location to the URL 
		$link .= $_SERVER['REQUEST_URI']; 
		      
		// Print the link 
		return $link; 
	}

	public static function check_social_login_enable() {
		$facebook = WP_RealEstate_Social_Facebook::get_instance();
		$google = WP_RealEstate_Social_Google::get_instance();
		$linkedin = WP_RealEstate_Social_Linkedin::get_instance();
		$twitter = WP_RealEstate_Social_Twitter::get_instance();
		if ( $facebook->is_facebook_login_enabled() || $google->is_google_login_enabled() || $linkedin->is_linkedin_login_enabled() || $twitter->is_twitter_login_enabled() ) {
			return true;
		}
		return false;
	}

	public static function social_login_before(){
		if ( self::check_social_login_enable() ) {
	        echo '<div class="wrapper-social-login"><div class="inner-social">';
	    }
    }
	
	public static function social_login_after(){
		if ( self::check_social_login_enable() ) {
            echo '<div class="line-header"><span>'.esc_html__('or', 'wp-realestate').'</span></div>';
	        echo '</div></div>';
	    }
    }

    public static function set_distance_type($distance_unit) {
        $unit = wp_realestate_get_option('search_distance_unit', 'miles');
        if ( in_array($unit, array('miles', 'km')) ) {
            $distance_unit = $unit;
        }
        return $distance_unit;
    }

    public static function random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }
    
    public static function set_location_number($nb) {
        $nb_fields = wp_realestate_get_option('location_nb_fields', 1);
        return $nb_fields;
    }

    public static function set_first_location_label($nb) {
        return wp_realestate_get_option('location_1_field_label', 'Country');
    }

    public static function set_second_location_label($nb) {
        return wp_realestate_get_option('location_2_field_label', 'State');
    }

    public static function set_third_location_label($nb) {
        return wp_realestate_get_option('location_3_field_label', 'City');
    }

    public static function set_fourth_location_label($nb) {
        return wp_realestate_get_option('location_4_field_label', 'District');
    }

    public static function required_add_label($obj) {
        if ( !empty($obj['name']) ) {
            return $obj['name'].' <span class="required">*</span>';
        }
        return '';
    }
}

WP_RealEstate_Mixes::init();
