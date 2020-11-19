<?php

if ( ! function_exists( 'homeo_post_tags' ) ) {
	function homeo_post_tags() {
		$posttags = get_the_tags();
		if ( $posttags ) {
			echo '<span class="entry-tags-list"><strong>'.esc_html__( 'Tags: ' , 'homeo' ).'</strong> ';
			$i = 1;
			$size = count( $posttags );
			foreach ( $posttags as $tag ) {
				echo '<a href="' . get_tag_link( $tag->term_id ) . '">';
				echo esc_attr($tag->name);
				echo '</a>';
				if($i < $size){
					echo ', ';
				}
				$i ++;
			}
			echo '</span>';
		}
	}
}

if ( !function_exists('homeo_get_page_title') ) {
	function homeo_get_page_title() {
		$title = '';
		if ( !is_front_page() || is_paged() ) {
			global $post;
			$homeLink = esc_url( home_url() );

			if ( is_home() ) {
				$title = esc_html__( 'Blog', 'homeo' );
			} elseif (is_category()) {
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$title = $cat_obj->name;
			} elseif (is_day()) {
				$title = get_the_time('d');
			} elseif (is_month()) {
				$title = get_the_time('F');
			} elseif (is_year()) {
				$title = get_the_time('Y');
			} elseif (is_single() && !is_attachment()) {
				if ( get_post_type() != 'post' ) {
					$title = get_the_title();
				} else {
					$title = esc_html__( 'Blog', 'homeo' );
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_author() && !is_search() ) {
				$post_type = get_post_type_object(get_post_type());

				if ( is_tax('property_status') || is_tax('property_type') || is_tax('property_location') || is_tax('property_amenity') || is_tax('property_label') || is_tax('property_material') ) {
					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$title = $cat_obj->name;
				} elseif( is_post_type_archive('property') ) {
					$title = esc_html__('Properties', 'homeo');
				} elseif ( is_tax('agency_category') || is_tax('agency_location') ) {
					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$title = $cat_obj->name;
				} elseif ( is_post_type_archive('agency') ) {
					$title = esc_html__('Agencies', 'homeo');
				} elseif ( is_tax('agent_category') || is_tax('agent_location') ) {
					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$title = $cat_obj->name;
				} elseif ( is_post_type_archive('agent') ) {
					$title = esc_html__('Agents', 'homeo');
				} elseif ( is_object($post_type) ) {
					$title = $post_type->labels->singular_name;
				}
			} elseif (is_404()) {
				$title = esc_html__('Error 404', 'homeo');
			} elseif (is_attachment()) {
				$title = get_the_title();
			} elseif ( is_page() && !$post->post_parent ) {
				$title = get_the_title();
			} elseif ( is_page() && $post->post_parent ) {
				$title = get_the_title();
			} elseif ( is_search() ) {
				$title = sprintf(esc_html__('Search results for "%s"', 'homeo'), get_search_query());
			} elseif ( is_tag() ) {
				$title = sprintf(esc_html__('Posts tagged "%s"', 'homeo'), single_tag_title('', false) );
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				$title = $userdata->display_name;
			} elseif ( is_404() ) {
				$title = esc_html__('Error 404', 'homeo');
			}
		}else{
			$title = get_the_title();
		}
		return $title;
	}
}

if ( ! function_exists( 'homeo_breadcrumbs' ) ) {
	function homeo_breadcrumbs() {

		$delimiter = ' ';
		$home = esc_html__('Home', 'homeo');
		$before = '<li><span class="active">';
		$after = '</span></li>';
		
		if ( !is_front_page() || is_paged()) {
			global $post;
			$homeLink = esc_url( home_url() );
			
			echo '<div class="left-inner"><ol class="breadcrumb">';
			echo '<li><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . '</li> ';

			if (is_category()) {
				global $wp_query;
				$cat_obj = $wp_query->get_queried_object();
				$thisCat = $cat_obj->term_id;
				$thisCat = get_category($thisCat);
				$parentCat = get_category($thisCat->parent);
				echo '<li>';
				if ($thisCat->parent != 0)
					echo get_category_parents($parentCat, TRUE, '</li><li>');
				echo '<span class="active">'.single_cat_title('', false) . $after;
			} elseif (is_day()) {
				echo '<li><a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo '<li><a href="' . esc_url( get_month_link(get_the_time('Y'),get_the_time('m')) ) . '">' . get_the_time('F') . '</a></li> ' . $delimiter . ' ';
				echo trim($before) . get_the_time('d') . $after;
			} elseif (is_month()) {
				echo '<a href="' . esc_url( get_year_link(get_the_time('Y')) ) . '">' . get_the_time('Y') . '</a></li> ' . $delimiter . ' ';
				echo trim($before) . get_the_time('F') . $after;
			} elseif (is_year()) {
				echo trim($before) . get_the_time('Y') . $after;
			} elseif (is_single() && !is_attachment()) {

				if ( get_post_type() == 'property' ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_properties_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Properties', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
					echo trim($before) . get_the_title() . $after;
				} elseif ( get_post_type() == 'agent' ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agents_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agents', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
					echo trim($before) . get_the_title() . $after;
				} elseif ( get_post_type() == 'agency' ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agencies_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agencies', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
					echo trim($before) . get_the_title() . $after;
				} elseif ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					
					echo '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li> ' . $delimiter . ' ';
					echo trim($before) . get_the_title() . $after;
				} elseif ( get_post_type() == 'post' ) {
					global $post;
					$cat = get_the_category(); $cat = $cat[0];
					echo '<li>'.get_category_parents($cat, TRUE, '</li><li>');
					echo '<span class="active">'. $post->post_title . $after;
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					echo '<li>'.get_category_parents($cat, TRUE, '</li>');
				}
			} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_author() && !is_search()) {

				$post_type = get_post_type_object(get_post_type());
				if ( is_tax('property_status') || is_tax('property_type') || is_tax('property_location') || is_tax('property_amenity') || is_tax('property_label') || is_tax('property_material') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_properties_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Properties', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}

					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$parentCat = get_term($cat_obj->parent, $cat_obj->taxonomy);
					echo '<li>';
					if ( ! empty( $parentCat ) && ! is_wp_error( $parentCat ) ) {
						echo homeo_get_taxonomy_parents($parentCat->term_id, $cat_obj->taxonomy, TRUE, '</li><li>');
					}

					echo '<span class="active">'.single_cat_title('', false) . $after;
				} elseif( is_post_type_archive('property') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_properties_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Properties', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
				} elseif ( is_tax('agency_category') || is_tax('agency_location') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agencies_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agencies', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}

					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$parentCat = get_term($cat_obj->parent, $cat_obj->taxonomy);
					echo '<li>';
					if ( ! empty( $parentCat ) && ! is_wp_error( $parentCat ) ) {
						echo homeo_get_taxonomy_parents($parentCat->term_id, $cat_obj->taxonomy, TRUE, '</li><li>');
					}

					echo '<span class="active">'.single_cat_title('', false) . $after;
				} elseif ( is_post_type_archive('agency') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agencies_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agencies', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
				} elseif ( is_tax('agent_category') || is_tax('agent_location') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agents_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agents', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}

					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					$parentCat = get_term($cat_obj->parent, $cat_obj->taxonomy);
					echo '<li>';
					if ( ! empty( $parentCat ) && ! is_wp_error( $parentCat ) ) {
						echo homeo_get_taxonomy_parents($parentCat->term_id, $cat_obj->taxonomy, TRUE, '</li><li>');
					}

					echo '<span class="active">'.single_cat_title('', false) . $after;
				} elseif ( is_post_type_archive('agent') ) {
					if ( class_exists('WP_RealEstate_Mixes') ) {
						$url = WP_RealEstate_Mixes::get_agents_page_url();
						echo '<li><a href="' . esc_url($url) . '">' . esc_html__('Agents', 'homeo') . '</a></li> ' . $delimiter . ' ';
					}
				} elseif (is_object($post_type)) {
					echo trim($before) . $post_type->labels->singular_name . $after;
				}
			} elseif (is_404()) {
				echo trim($before) .esc_html__('Error 404', 'homeo') . $after;
			} elseif (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				echo '<li>';
				if ( !empty($cat) ) {
					$cat = $cat[0];
					echo get_category_parents($cat, TRUE, '</li><li>');
				}
				if ( !empty($parent) ) {
					echo '<a href="' . esc_url( get_permalink($parent) ) . '">' . $parent->post_title . '</a></li><li>';
				}
				echo '<span class="active">'.get_the_title() . $after;
			} elseif ( is_page() && !$post->post_parent ) {
				echo trim($before) . get_the_title() . $after;
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<li><a href="' . esc_url( get_permalink($page->ID) ) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) {
					echo trim($crumb) . ' ' . $delimiter . ' ';
				}
				echo trim($before) . get_the_title() . $after;
			} elseif ( is_search() ) {
				echo trim($before) . sprintf(esc_html__('Search results for "%s"','homeo'), get_search_query()) . $after;
			} elseif ( is_tag() ) {
				echo trim($before) . sprintf(esc_html__('Posts tagged "%s"', 'homeo'), single_tag_title('', false)) . $after;
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo trim($before) . esc_html__('Articles posted by ', 'homeo') . $userdata->display_name . $after;
			} elseif ( is_404() ) {
				echo trim($before) . esc_html__('Error 404', 'homeo') . $after;
			} elseif ( is_home() ) {
				echo trim($before) . esc_html__('The Blogs', 'homeo') . $after;
			}

			echo '</ol></div>';
		}
	}
}

function homeo_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {
    $chain = '';
    $parent = get_term( $id, $taxonomy );
    if ( is_wp_error( $parent ) ) {
        return $parent;
    }
    if ( $nicename ) {
        $name = $parent->slug;
    } else {
        $name = $parent->name;
    }

    if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
        $visited[] = $parent->parent;
        $chain .= homeo_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
    }

    if ( $link ) {
        $chain .= '<a href="' . esc_url( get_term_link( $parent,$taxonomy ) ) . '" title="' . esc_attr( sprintf( esc_html__( 'View all posts in %s', 'homeo' ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
 	} else {
        $chain .= $name.$separator;
    }
    return $chain;
}

if ( ! function_exists( 'homeo_render_breadcrumbs' ) ) {
	function homeo_render_breadcrumbs($additional_html = '') {
		global $post;
		$has_bg = '';
		$show = true;
		$style = $classes = array();
		$breadcrumb_style = '';
		$full_width = 'container';
		if ( is_page() && is_object($post) ) {
			$show = get_post_meta( $post->ID, 'apus_page_show_breadcrumb', true );
			if ( $show == 'no' ) {
				return ''; 
			}
			$bgimage = get_post_meta( $post->ID, 'apus_page_breadcrumb_image', true );
			$bgcolor = get_post_meta( $post->ID, 'apus_page_breadcrumb_color', true );
			$style = array();
			if ( $bgcolor ) {
				$style[] = 'background-color:'.$bgcolor;
			}
			if ( $bgimage ) { 
				$style[] = 'background-image:url(\''.esc_url($bgimage).'\')';
				$has_bg = 1;
			}
			$bstyle = get_post_meta( $post->ID, 'apus_page_breadcrumb_style', true );
			if ( empty($bstyle) ) {
				$breadcrumb_style = 'horizontal';
			} else {
				$breadcrumb_style = $bstyle;
			}
			$full_width = apply_filters('homeo_page_content_class', 'container');
		} elseif ( is_singular('post') || is_category() || is_home() || is_search() ) {
			$show = homeo_get_config('show_blog_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = homeo_get_config('blog_breadcrumb_image');
	        $breadcrumb_color = homeo_get_config('blog_breadcrumb_color');
	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img['url']).'\')';
	            $has_bg = 1;
	        }
	        $breadcrumb_style = homeo_get_config('blog_breadcrumb_style', 'horizontal');
	        
	        $full_width = apply_filters('homeo_blog_content_class', 'container');
		} elseif ( is_post_type_archive('property') || is_tax('property_type') || is_tax('property_staus') || is_tax('property_location') || is_tax('property_amenity') || is_tax('property_label') || is_tax('property_material') ) {
			$show = homeo_get_config('show_property_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = homeo_get_config('property_breadcrumb_image');
	        $breadcrumb_color = homeo_get_config('property_breadcrumb_color');
	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img['url']).'\')';
	            $has_bg = 1;
	        }
	        $breadcrumb_style = homeo_get_config('property_breadcrumb_style', 'vertical');

	        $full_width = apply_filters('homeo_property_content_class', 'container');
		} elseif ( is_post_type_archive('agency') || is_tax('agency_category') || is_tax('agency_location')  ) {
			$show = homeo_get_config('show_agency_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = homeo_get_config('agency_breadcrumb_image');
	        $breadcrumb_color = homeo_get_config('agency_breadcrumb_color');
	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img['url']).'\')';
	            $has_bg = 1;
	        }
	        $breadcrumb_style = homeo_get_config('agency_breadcrumb_style', 'vertical');

	        $full_width = apply_filters('homeo_agency_content_class', 'container');
		} elseif ( is_post_type_archive('agent') || is_tax('agent_location') || is_tax('agent_category')  ) {
			$show = homeo_get_config('show_agent_breadcrumbs', true);
			if ( !$show || is_front_page() ) {
				return ''; 
			}
			$breadcrumb_img = homeo_get_config('agent_breadcrumb_image');
	        $breadcrumb_color = homeo_get_config('agent_breadcrumb_color');
	        $style = array();
	        if ( $breadcrumb_color ) {
	            $style[] = 'background-color:'.$breadcrumb_color;
	        }
	        if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) ) {
	            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img['url']).'\')';
	            $has_bg = 1;
	        }
	        $breadcrumb_style = homeo_get_config('agent_breadcrumb_style', 'vertical');
	        
	        $full_width = apply_filters('homeo_agent_content_class', 'container');
		}
		$estyle = !empty($style)? ' style="'.implode(";", $style).'"':"";
		$classes[] = $has_bg ? 'has_bg' :'';
		$classes[] = $breadcrumb_style;

		$title = homeo_get_page_title();

		echo '<section id="apus-breadscrumb" class="breadcrumb-page apus-breadscrumb '.implode(' ', $classes).'"'.$estyle.'><div class="'.$full_width.'"><div class="wrapper-breads'. ( (!empty($additional_html))?' flex-middle-sm':'' ) .'">
		<div class="wrapper-breads-inner">';
			if ( $breadcrumb_style == 'horizontal' ) {
				echo '<div class="breadscrumb-inner clearfix">';
				echo '<h2 class="bread-title">'.$title.'</h2>';
				echo '</div>';
				homeo_breadcrumbs();
			} else {
				homeo_breadcrumbs();
				echo '<div class="breadscrumb-inner clearfix">';
				echo '<h2 class="bread-title">'.$title.'</h2>';
				echo '</div>';
			}
		echo '</div>';
		if(!empty($additional_html)){
			echo '<div class="ali-right"><div class="flex-middle">'.trim($additional_html).'</div></div>';
		}
		echo '</div></div></section>';
	}
}

if ( ! function_exists( 'homeo_paging_nav' ) ) {
	function homeo_paging_nav() {
		global $wp_query, $wp_rewrite;

		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

		$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
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

		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $wp_query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 1,
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => '<i class=" ti-angle-left"></i>',
			'next_text' => '<i class=" ti-angle-right"></i>',
		) );

		if ( $links ) :

		?>
		<nav class="navigation paging-navigation" role="navigation">
			<h1 class="screen-reader-text hidden"><?php esc_html_e( 'Posts navigation', 'homeo' ); ?></h1>
			<div class="apus-pagination">
				<?php echo trim($links); ?>
			</div><!-- .pagination -->
		</nav><!-- .navigation -->
		<?php
		endif;
	}
}

if ( !function_exists('homeo_comment_form') ) {
	function homeo_comment_form($arg, $class = 'btn-theme ') {
		global $post;
		if ('open' == $post->comment_status) {
			ob_start();
	      	comment_form($arg);
	      	$form = ob_get_clean();
	      	?>
	      	<div class="commentform reset-button-default">
		    	<div class="clearfix">
			    	<?php
			      	echo str_replace('id="submit"','id="submit" class="btn '.$class.'"', $form);
			      	?>
		      	</div>
	      	</div>
	      	<?php
	      }
	}
}

if (!function_exists('homeo_comment_item') ) {
	function homeo_comment_item($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		ob_start();
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">

			<div class="the-comment">
				<?php
					$avatar = get_avatar($comment, 80);
					if ( $avatar ) {
				?>
					<div class="avatar">
						<?php echo trim($avatar); ?>
					</div>
				<?php } ?>
				<div class="comment-box">
					<div class="clearfix">
						<div class="name-comment"><?php echo get_comment_author_link() ?></div>
						<div class="comment-author flex-middle">
								<div class="date"><?php printf(esc_html__('%1$s', 'homeo'), get_comment_date()) ?></div>
								<?php edit_comment_link('<span class="space">|</span>'.esc_html__('Edit', 'homeo'),'','') ?>
								<span class="hidden-smallest">
									<?php comment_reply_link(array_merge( $args, array( 'reply_text' => '<span class="space">|</span><span class="text-reply">'.esc_html__(' Reply', 'homeo').'</span>', 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
								</span>
						</div>
						<span class="visible-smallest">
							<?php comment_reply_link(array_merge( $args, array( 'reply_text' => '<span class="space">|</span>'.esc_html__(' Reply', 'homeo'), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
						</span>
					</div>
					<div class="comment-text">
						<?php if ($comment->comment_approved == '0') : ?>
						<em><?php esc_html_e('Your comment is awaiting moderation.', 'homeo') ?></em>
						<br />
						<?php endif; ?>
						<?php comment_text() ?>
					</div>
				</div>
			</div>
		</li>
		<?php
		$output = ob_get_clean();
		echo apply_filters('homeo_comment_item', $output, $comment, $args, $depth);
	}
}

function homeo_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}
add_filter( 'comment_form_fields', 'homeo_comment_field_to_bottom' );


function homeo_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'homeo_pingback_header' );

/*
 * create placeholder
 * var size: array( width, height )
 */
function homeo_create_placeholder($size) {
	return "data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg' viewBox%3D'0 0 ".$size[0]." ".$size[1]."'%2F%3E";
}

function homeo_display_sidebar_left( $sidebar_configs ) {
	if ( isset($sidebar_configs['left']) ) : ?>
		<div class="sidebar-wrapper <?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
		  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		  		<div class="close-sidebar-btn hidden-lg hidden-md"> <i class="ti-close"></i> <span><?php esc_html_e('Close', 'homeo'); ?></span></div>
		   		<?php if ( is_active_sidebar( $sidebar_configs['left']['sidebar'] ) ): ?>
		   			<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
		   		<?php endif; ?>
		  	</aside>
		</div>
	<?php endif;
}

function homeo_display_sidebar_right( $sidebar_configs ) {
	if ( isset($sidebar_configs['right']) ) : ?>
		<div class="sidebar-wrapper <?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
		  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
		  		<div class="close-sidebar-btn hidden-lg hidden-md"><i class="ti-close"></i> <span><?php esc_html_e('Close', 'homeo'); ?></span></div>
		   		<?php if ( is_active_sidebar( $sidebar_configs['right']['sidebar'] ) ): ?>
			   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
			   	<?php endif; ?>
		  	</aside>
		</div>
	<?php endif;
}

function homeo_before_content( $sidebar_configs ) {
	if ( isset($sidebar_configs['left']) || isset($sidebar_configs['right']) ) : ?>
		<a href="javascript:void(0)" class="mobile-sidebar-btn hidden-lg hidden-md <?php echo esc_attr( isset($sidebar_configs['left']) ? 'btn-left':'btn-right' ); ?>"><i class="ti-menu-alt"></i></a>
		<div class="mobile-sidebar-panel-overlay"></div>
	<?php endif;
}
