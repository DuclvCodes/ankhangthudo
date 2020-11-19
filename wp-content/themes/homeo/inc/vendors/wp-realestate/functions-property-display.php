<?php

function homeo_property_display_image($post, $size = 'thumbnail') {
	?>
    <div class="image-thumbnail">
        <a class="property-image" href="<?php echo esc_url( get_permalink($post) ); ?>">
        	<?php
        	if ( has_post_thumbnail($post->ID) ) {
        		$post_thumbnail_id = get_post_thumbnail_id($post->ID);
        		echo homeo_get_attachment_thumbnail( $post_thumbnail_id, $size );
        	} else {
        		?>
        		<img src="<?php echo esc_url(homeo_placeholder_img_src()); ?>" alt="<?php echo esc_attr($post->post_title); ?>">
        		<?php
        	}
        	?>
        </a>
    </div>
    <?php
}

function homeo_property_display_gallery($post, $size = 'thumbnail') {
	if ( !homeo_get_config('properties_gallery', true) ) {
		return;
	}
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$gallery = $obj_property_meta->get_post_meta( 'gallery' );
	if ( has_post_thumbnail() || $gallery ) {
		$images = [];
		if ( has_post_thumbnail() ) {
            $images[] = get_the_post_thumbnail_url($post, $size);
        }

        if ( empty($gallery) ) {
        	return;
        }
        foreach ( $gallery as $id => $src ) {
        	$img = wp_get_attachment_image_url($id, $size);
        	if ( $img ) {
        		$images[] = $img;
        	}
        }

        echo 'data-images="'.esc_attr(json_encode($images)).'"';
	}
}

function homeo_property_display_author($post, $display_type = 'logo', $echo = true) {
	$author_id = $post->post_author;
	ob_start();
	if ( $author_id ) {
		$author_url = '';
		if ( WP_RealEstate_User::is_agent($author_id) ) {
		    $agent_id = WP_RealEstate_User::get_agent_by_user_id($author_id);
		    if ( has_post_thumbnail($agent_id) ) {
		        $post_thumbnail_id = get_post_thumbnail_id($agent_id);
        		$logo = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
		    }
		    $title = get_the_title($agent_id);
		    $author_url = get_permalink($agent_id);
		} elseif ( WP_RealEstate_User::is_agency($author_id) ) {
		    $agency_id = WP_RealEstate_User::get_agency_by_user_id($author_id);
		    if ( has_post_thumbnail($agency_id) ) {
		        $post_thumbnail_id = get_post_thumbnail_id($agency_id);
        		$logo = homeo_get_attachment_thumbnail( $post_thumbnail_id, 'thumbnail' );
		    }
		    $title = get_the_title($agency_id);
		    $author_url = get_permalink($agency_id);
		} else {
			$user_info = get_userdata($author_id);
		    $logo = get_avatar( $author_id, 80 );
		    $title = $user_info->display_name;
		}
		?>
	        <div class="avatar-wrapper flex-middle">
            	<?php if ($display_type == 'logo') { ?>
					<div class="avatar-img">
						<?php if ( $author_url ) { ?>
							<a href="<?php echo esc_url($author_url); ?>">
						<?php } ?>
							<?php echo trim($logo); ?>
						<?php if ( $author_url ) { ?>
							</a>
						<?php } ?>
					</div>
				<?php } ?>
				<div class="name-author">
					<?php if ( $author_url ) { ?>
						<a href="<?php echo esc_url($author_url); ?>">
					<?php } ?>
                		<?php echo trim($title); ?>
                	<?php if ( $author_url ) { ?>
						</a>
					<?php } ?>
                </div>
	        </div>
	    <?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_label($post, $echo = true) {
	$labels = get_the_terms( $post->ID, 'property_label' );
	ob_start();
	if ( $labels ) {
		foreach ($labels as $term) {
			$text_color = get_term_meta( $term->term_id, 'text_color', true );
			$bg_color = get_term_meta( $term->term_id, 'bg_color', true );
			$style = '';
			if ( $bg_color ) {
				$style .= 'background: '.$bg_color.';';
			}
			if ( $text_color ) {
				$style .= 'color: '.$text_color.';';
			}
			?>
            	<a class="label-property-label" href="<?php echo esc_url(get_term_link($term)); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
        	<?php
    	}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_status_label($post, $echo = true, $color = true) {
	$statuses = get_the_terms( $post->ID, 'property_status' );
	ob_start();
	if ( $statuses ) {
		foreach ($statuses as $term) {
			$text_color = get_term_meta( $term->term_id, 'text_color', true );
			$bg_color = get_term_meta( $term->term_id, 'bg_color', true );
			$style = '';
			if ( $color ) {
				if ( $bg_color ) {
					$style .= 'background: '.$bg_color.';';
				}
				if ( $text_color ) {
					$style .= 'color: '.$text_color.';';
				}
			}
			?>
            	<a class="status-property-label" href="<?php echo esc_url(get_term_link($term)); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
        	<?php
    	}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_status($post, $display_type = 'no-title', $echo = true) {
	$statuses = get_the_terms( $post->ID, 'property_status' );
	ob_start();
	$i = 1;
	if ( $statuses ) {
		?>
		<div class="status-property">
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="property-status with-title">
					<strong><?php esc_html_e('Status:', 'homeo'); ?></strong>
				<?php
			} elseif ($display_type == 'icon') {
				?>
				<div class="property-status with-icon">
					<i class="ti-home"></i>
			<?php
			} else {
				?>
				<div class="property-status">
				<?php
			}
				foreach ($statuses as $term) {
					$text_color = get_term_meta( $term->term_id, 'text_color', true );
					$bg_color = get_term_meta( $term->term_id, 'bg_color', true );
					$style = '';
					if ( $bg_color ) {
						$style .= 'background: '.$bg_color.';';
					}
					if ( $text_color ) {
						$style .= 'color: '.$text_color.';';
					}
					?>
		            	<a class="status-property" href="<?php echo esc_url(get_term_link($term)); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a><?php if( $i < count($statuses) ) { ?> ,<?php } ?>
		        	<?php
		        	$i++;
		    	}
	    	?>
	    	</div>
	    </div>
    	<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_type($post, $display_type = 'no-title', $echo = true) {
	$types = get_the_terms( $post->ID, 'property_type' );
	ob_start();
	$number = 1;
	if ( $types ) {
		?>
		<div class="property-type">
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="property-type with-title">
					<strong><?php esc_html_e('Property Type:', 'homeo'); ?></strong>
				<?php
			} elseif ($display_type == 'icon') {
				?>
				<div class="property-type with-icon">
					<i class="ti-calendar"></i>
			<?php
			} else {
				?>
				<div class="property-type with-no-title">
				<?php
			}
				foreach ($types as $term) {
					$color = get_term_meta( $term->term_id, '_color', true );
					$style = '';
					if ( $color ) {
						$style = 'color: '.$color;
					}
					?>
		            	<a class="type-property" href="<?php echo esc_url(get_term_link($term)); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a><?php if($number < count($types)) echo trim(', ');?>
		        	<?php  $number++;
		    	}
	    	?>
	    	</div>
	    </div>
    	<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_short_location($post, $echo = true) {
	$locations = get_the_terms( $post->ID, 'property_location' );
	ob_start();
	if ( $locations ) {
		?>
		<div class="property-location">
            <i class="flaticon-location-pin"></i>
            <?php $i=1; foreach ($locations as $term) { ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a><?php echo esc_html( $i < count($locations) ? ', ' : '' ); ?>
            <?php $i++; } ?>
        </div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_full_location($post, $display_type = 'no-icon-title', $echo = true) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$location = $obj_property_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_property_meta->get_post_meta( 'map_location_address' );
	}
	ob_start();
	if ( $location ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="property-location with-icon"><i class="flaticon-maps-and-flags"></i> <a href="<?php echo esc_url( '//maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>" target="_blank"><?php echo esc_html($location); ?></a></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="property-location with-title">
				<strong><?php esc_html_e('Location:', 'homeo'); ?></strong> <a href="<?php echo esc_url( '//maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>" target="_blank"><?php echo esc_html($location); ?></a>
			</div>
			<?php
		} else {
			?>
			<div class="property-location"><a href="<?php echo esc_url( '//maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>" target="_blank"><?php echo esc_html($location); ?></a></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_full_location_without_url($post_id, $display_type = 'no-icon-title', $echo = true) {
	if ( is_object($post_id) ) {
		$post_id = $post_id->ID;
	}
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post_id);

	$location = $obj_property_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_property_meta->get_post_meta( 'map_location_address' );
	}
	ob_start();
	if ( $location ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="property-location with-icon"><i class="flaticon-maps-and-flags"></i> <?php echo esc_html($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="property-location with-title">
				<strong><?php esc_html_e('Location:', 'homeo'); ?></strong> <?php echo esc_html($location); ?>
			</div>
			<?php
		} else {
			?>
			<div class="property-location"><<?php echo esc_html($location); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_location_map_icon($post, $echo = true) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$location = $obj_property_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_property_meta->get_post_meta( 'map_location_address' );
	}
	ob_start();
	if ( $location ) {
		?>
		<a class="btn-location" href="<?php echo esc_url( '//maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>">
			<i class="flaticon-maps-and-flags"></i>
		</a>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_price($post_id, $display_type = 'no-icon-title', $echo = true) {
	if ( is_object($post_id) ) {
		$post_id = $post_id->ID;
	}
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post_id);
	$price = $obj_property_meta->get_price_html();
	ob_start();
	if ( $price ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="property-price with-icon"><i class="ti-credit-card"></i> <?php echo trim($price); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="property-price with-title">
				<strong><?php esc_html_e('Price:', 'homeo'); ?></strong> <span><?php echo trim($price); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="property-price"><?php echo trim($price); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_postdate($post, $display_type = 'no-icon-title', $format = 'normal', $echo = true) {
	ob_start();
	if ( $format == 'ago' ) {
		$post_date = sprintf(esc_html__('%s ago', 'homeo'), human_time_diff(get_the_time('U'), current_time('timestamp')) );
	} else {
		$post_date = get_the_time(get_option('date_format'));
	}
	if ( $display_type == 'icon' ) {
		?>
		<div class="property-postdate with-icon"><i class="ti-credit-card"></i> <?php echo trim($post_date); ?></div>
		<?php
	} elseif ( $display_type == 'title' ) {
		?>
		<div class="property-postdate with-title">
			<strong><?php esc_html_e('Date:', 'homeo'); ?></strong> <?php echo trim($post_date); ?>
		</div>
		<?php
	} else {
		?>
		<div class="property-postdate"><?php echo trim($post_date); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_display_featured_icon($post, $echo = true) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$featured = $obj_property_meta->get_post_meta( 'featured' );
	ob_start();
	if ( $featured ) {
		?>
        <span class="featured-property"><?php esc_attr_e('Featured', 'homeo'); ?></span>
	    <?php
	}

    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_single_display_featured_icon($post, $echo = true) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$featured = $obj_property_meta->get_post_meta( 'featured' );
	ob_start();
	if ( $featured ) {
		?>
        <span class="label label-success featured-property"><?php esc_attr_e('Featured', 'homeo'); ?></span>
	    <?php
	}

    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_item_map_meta($post) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$latitude = $obj_property_meta->get_post_meta( 'map_location_latitude' );
	$longitude = $obj_property_meta->get_post_meta( 'map_location_longitude' );

	$thumbnail_url = '';
	if ( has_post_thumbnail($post->ID) ) {
		$thumbnail_url = get_the_post_thumbnail_url( $post, 'homeo-property-grid' );
	}
	
	echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($thumbnail_url).'"';
}


function homeo_property_display_meta($post, $meta_key, $icon = '', $title = '', $suffix = '', $echo = false) {
	$obj_property_meta = WP_RealEstate_Property_Meta::get_instance($post->ID);

	$value = $obj_property_meta->get_post_meta( $meta_key );
	
	ob_start();
	if ( $value ) {
		?>
		<div class="property-meta with-<?php echo esc_attr($title ? 'icon-title' : 'icon'); ?>">

			<div class="property-meta">

				<?php if ( !empty($title) ) { ?>
					<span class="title-meta">
						<?php echo esc_html($title); ?>
					</span>
				<?php } ?>

				<?php if ( !empty($icon) ) { ?>
					<i class="<?php echo esc_attr($icon); ?>"></i>
				<?php } ?>
				<?php echo esc_html($value); ?>
				<?php echo trim($suffix); ?>
			</div>

		</div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function homeo_property_compare_field_featured($value, $key, $post_id) {
	switch ($key) {
	 	case 'featured':
	 		$classes = 'no';
			if ( $value == 'on' ) {
				$classes = 'yes';
			}
			$value = '<span class="'.$classes.'"><i class="fas fa-star"></i><span>';
	 		break;
 		case 'valuation_group':
			if ( is_array( $value ) && count( $value[0] ) > 0 ) {
				ob_start();
			?>
			    <div class="property-section property-valuation">
			        <?php foreach ( $value as $group ) : ?>
			            <div class="valuation-item clearfix">
			                <div class="clearfix">
			                    <div class="valuation-label pull-left"><?php echo empty( $group['valuation_key'] ) ? '' : esc_attr( $group['valuation_key'] ); ?></div>
			                    <span class="percentage-valuation pull-right"><?php echo empty( $group['valuation_value'] ) ? '' : esc_attr( $group['valuation_value'] ); ?> <?php esc_html_e('%', 'homeo'); ?></span>
			                </div>
			                <div class="property-valuation-item progress" >
			                    <div class="bar-valuation progress-bar progress-bar-success progress-bar-striped"
			                         style="width: <?php echo esc_attr( $group[ 'valuation_value' ] ); ?>%"
			                         data-percentage="<?php echo empty( $group['valuation_value'] ) ? '' : esc_attr( $group['valuation_value'] ); ?>">
			                    </div>
			                </div><!-- /.property-valuation-item -->
			                
			            </div>
			        <?php endforeach; ?>
			    </div><!-- /.property-valuation -->
			<?php
				$value = ob_get_clean();
			}
	 		break;
 		case 'public_facilities_group':
			if ( $value ) {
				ob_start();
			?>
			    <div class="property-section property-public-facilities">
			        <div class="clearfix">
			            <?php foreach ( $value as $facility ) : ?>
			                <div class="property-public-facility-wrapper">
			                    <div class="property-public-facility">
			                        <div class="property-public-facility-title">
			                            <span><?php echo empty( $facility['public_facilities_key'] ) ? '' : esc_attr( $facility['public_facilities_key'] ); ?></span>
			                        </div>
			                        <div class="property-public-facility-info">
			    						<?php echo empty( $facility['public_facilities_value'] ) ? '' : esc_attr( $facility['public_facilities_value'] ); ?>
			                        </div>
			                    </div>
			                </div>
			            <?php endforeach; ?>
			        </div>
			    </div>
			<?php
				$value = ob_get_clean();
			}
	 		break;
	 }
	return $value;
}
add_filter('wp-realestate-compare-field-value', 'homeo_property_compare_field_featured', 10, 3);


function homeo_property_print_btn($post, $show_title = false) {
	if ( homeo_get_config('listing_enable_printer', true) ) {
        ?>
        <a href="javascript:void(0);" class="btn-print-property" data-property_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'homeo-printer-property-nonce' )); ?>" data-toggle="tooltip" title="<?php esc_attr_e('Print', 'homeo'); ?>"><i class="flaticon-printer"></i>
        	<?php if ( $show_title ) { ?>
        		<span><?php esc_html_e('Print', 'homeo'); ?></span>
        	<?php } ?>
        </a>
        <?php
    }
}