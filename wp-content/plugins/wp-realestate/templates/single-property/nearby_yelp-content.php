<?php

foreach ($terms as $term) {
    $cate_title = !empty($term['yelp_title']) ? $term['yelp_title'] : $term['yelp_category'];
    $cate_icon = !empty($term['yelp_icon']) ? $term['yelp_icon'] : '';
    $cate_term = '';

    if (array_key_exists($term['yelp_category'], $all_cats)) {
        $cate_term = str_replace('-', '+', $term['yelp_category']);
    }
    $yelp = WP_RealEstate_Property_Yelp::get_instance();
    $businesses_data = $yelp->query_api($cate_term, '', $latitude, $longitude);
    if ( $businesses_data ) {
       	
    	?>
		<div class="yelp-list">
            <div class="yelp-list-cat">
				<div class="yelp-cat-title">
					<?php if ( !empty($cate_icon) ) { ?>
						<img src="<?php echo esc_url($cate_icon); ?>" alt="">
					<?php } ?>
					<?php echo esc_html($cate_title); ?>
				</div>
				<div class="yelp-cat-content">
					<ul class="yelp-list-sub">
						<?php
			            foreach ($businesses_data as $data_business) {
			                $business_url = isset($data_business->url) ? $data_business->url : '';
			                $business_name = isset($data_business->name) ? $data_business->name : '';
			                $business_total_reviews = isset($data_business->review_count) ? $data_business->review_count : '';
			                $business_rating = isset($data_business->rating) ? $data_business->rating : '';
			                $business_distance = isset($data_business->distance) ? $data_business->distance : '';

			                $distance_unit = wp_realestate_get_option('api_settings_yelp_distance_unit');
			                if ( $business_distance ) {
			                	if ( $distance_unit == 'km' ) {
			                		$business_distance = round(($business_distance * 0.001), 2);
			                		$distance_text = __('km', 'wp-realestate');
			                	} else {
			                		$business_distance = round(($business_distance * 0.001  * 0.621371192), 2);
			                		$distance_text = __('miles', 'wp-realestate');
			                	}
			                	
			                }

			                ?>
			                <li>
			                	<div class="yelp-list-inner">
				                    <div class="inner-left">
										<div class="yelp-item-title">
				                            <a href="<?php echo esc_url_raw($business_url); ?>" target="_blank"><?php echo esc_html($business_name); ?></a>
				                            <?php if ( !empty($business_distance) ) { ?>
				                            	<span><?php echo sprintf('(%s %s)', $business_distance, $distance_text); ?></span>
				                            <?php } ?>
					                    </div>
									</div>
					                <div class="inner-right">
				                        <div class="rating">
				                            <div class="average-rating">
				                            	<div class="average-inner" style="width: <?php echo round(($business_rating/5 * 100), 2).'%'; ?>"></div>
				                            </div>
				                        </div>
				                        <span class="rating-count"><?php echo sprintf(_n('%d Review', '%d Reviews', absint($business_total_reviews), 'wp-realestate'), absint($business_total_reviews)); ?></span>
					                </div>
				                </div>
			                </li>
			            <?php
			            }
			            ?>
            		</ul>
            	</div>
            </div>
        </div>
    <?php
    }
    ?>
    
<?php
}