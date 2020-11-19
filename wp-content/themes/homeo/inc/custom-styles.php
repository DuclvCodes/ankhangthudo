<?php
if ( !function_exists ('homeo_custom_styles') ) {
	function homeo_custom_styles() {
		global $post;	
		
		ob_start();	
		?>
		
			<?php
				$main_font = homeo_get_config('main_font');
				$main_font_family = isset($main_font['font-family']) ? $main_font['font-family'] : false;
				$main_font_size = isset($main_font['font-size']) ? $main_font['font-size'] : false;
				$main_font_weight = isset($main_font['font-weight']) ? $main_font['font-weight'] : false;
			?>
			<?php if ( $main_font_family ): ?>
				/* Main Font */
				.btn,
				body
				{
					font-family:  <?php echo '\'' . $main_font_family . '\','; ?> sans-serif;
				}
			<?php endif; ?>
			<?php if ( $main_font_size ): ?>
				/* Main Font Size */
				body
				{
					font-size: <?php echo esc_html($main_font_size); ?>;
				}
			<?php endif; ?>
			<?php if ( $main_font_weight ): ?>
				/* Main Font Weight */
				body
				{
					font-weight: <?php echo esc_html($main_font_weight); ?>;
				}
			<?php endif; ?>


			<?php
				$heading_font = homeo_get_config('heading_font');
				$heading_font_family = isset($heading_font['font-family']) ? $heading_font['font-family'] : false;
				$heading_font_weight = isset($heading_font['font-weight']) ? $heading_font['font-weight'] : false;
			?>
			<?php if ( $heading_font_family ): ?>
				/* Heading Font */
				h1, h2, h3, h4, h5, h6
				{
					font-family:  <?php echo '\'' . $heading_font_family . '\','; ?> sans-serif;
				}			
			<?php endif; ?>

			<?php if ( $heading_font_weight ): ?>
				/* Heading Font Weight */
				h1, h2, h3, h4, h5, h6
				{
					font-weight: <?php echo esc_html($heading_font_weight); ?>;
				}			
			<?php endif; ?>


			<?php if ( homeo_get_config('main_color') != "" ) : ?>
				/* seting background main */

				#compare-sidebar .compare-sidebar-btn,#properties-google-maps .marker-cluster::before,
				.valuation-item .progress-bar,
				.map-popup .icon-wrapper::before,
				.property-grid-slider .bottom-label [class*="btn"][class*="added"], .property-grid-slider .bottom-label [class*="btn"][class*="remove"], .property-grid-slider .bottom-label [class*="btn"]:hover, .property-grid-slider .bottom-label [class*="btn"]:focus,
				.buttons-group-center [class|="btn"]:hover i, .buttons-group-center [class|="btn"]:focus i,
				.buttons-group-center [class|="btn"][class*="added"] i, .buttons-group-center [class|="btn"][class*="remove"] i, .buttons-group-center [class|="btn"]:hover i, .buttons-group-center [class|="btn"]:focus i,
				.property-item .bottom-label [class*="btn"]:hover, .property-item .bottom-label [class*="btn"]:focus,
				.property-item .bottom-label [class*="btn"][class*="added"], .property-item .bottom-label [class*="btn"][class*="remove"],
				.property-item .bottom-label [class*="btn"]:hover, .property-item .bottom-label [class*="btn"]:focus,
				.tagcloud a:hover, .tagcloud a:focus, .tagcloud a.active,
				.tabs-v1 .nav-tabs > li > a::before,
				.post-navigation .nav-links > * > a:hover .meta-nav,
				.pagination > span:focus, .pagination > span:hover, .pagination > a:focus, .pagination > a:hover, .apus-pagination > span:focus, .apus-pagination > span:hover, .apus-pagination > a:focus, .apus-pagination > a:hover,
				.entry-content-detail .categories-name,
				.detail-post .entry-tags-list a:hover, .detail-post .entry-tags-list a:focus, .detail-post .entry-tags-list a.active,
				.pagination > span.current, .pagination > a.current, .apus-pagination > span.current, .apus-pagination > a.current,
				.member-thumbnail-wrapper .nb-property,.btn-readmore::before,
				.post-layout .top-image .categories-name,
				.nav-member > li > a::before,
				.nav-table > li > a:hover, .nav-table > li > a:focus,
				.nav-table > li.active > a:hover, .nav-table > li.active > a:focus, .nav-table > li.active > a,
				.ui-slider-horizontal .ui-slider-range,
				.widget-property-search-form .nav-tabs > li.active > a,
				.video-wrapper-inner .popup-video::before,
				.video-wrapper-inner .popup-video,
				.pagination .next:hover::before, .pagination .next:focus::before, .pagination .prev:hover::before, .pagination .prev:focus::before, .apus-pagination .next:hover::before, .apus-pagination .next:focus::before, .apus-pagination .prev:hover::before, .apus-pagination .prev:focus::before,
				.pagination li > span.current, .pagination li > a.current, .apus-pagination li > span.current, .apus-pagination li > a.current,
				.pagination li > span:focus, .pagination li > span:hover, .pagination li > a:focus, .pagination li > a:hover, .apus-pagination li > span:focus, .apus-pagination li > span:hover, .apus-pagination li > a:focus, .apus-pagination li > a:hover,
				.bg-theme, .property-item .top-label > *.featured-property, .details-product .apus-social-share a:hover, .details-product .apus-social-share a:active, .slick-carousel .slick-arrow:hover, .slick-carousel .slick-arrow:focus
				{
					background-color: <?php echo esc_html( homeo_get_config('main_color') ) ?> ;
				}
				.property-action-detail [class*="btn"][class*="added"], .property-action-detail [class*="btn"][class*="remove"],
				.property-action-detail [class*="btn"]:hover, .property-action-detail [class*="btn"]:focus,
				.bg-theme{
					background-color: <?php echo esc_html( homeo_get_config('main_color') ) ?> !important;
				}
				/* setting color */
				.compare-tables .type-property a,#properties-google-maps .marker-cluster > div,
				.subwoo-inner .price,.my-properties-item .property-price,.user-transactions .woocommerce-Price-amount,
				.tabs-v1 .nav-tabs > li.active > a,.product-categories li.current-cat-parent > a, .product-categories li.current-cat > a, .product-categories li:hover > a,.woocommerce ul.product_list_widget .woocommerce-Price-amount,
				.widget_pages ul li:hover > a, .widget_pages ul li.current-cat-parent > a, .widget_pages ul li.current-cat > a, .widget_nav_menu ul li:hover > a, .widget_nav_menu ul li.current-cat-parent > a, .widget_nav_menu ul li.current-cat > a, .widget_meta ul li:hover > a, .widget_meta ul li.current-cat-parent > a, .widget_meta ul li.current-cat > a, .widget_archive ul li:hover > a, .widget_archive ul li.current-cat-parent > a, .widget_archive ul li.current-cat > a, .widget_recent_entries ul li:hover > a, .widget_recent_entries ul li.current-cat-parent > a, .widget_recent_entries ul li.current-cat > a, .widget_categories ul li:hover > a, .widget_categories ul li.current-cat-parent > a, .widget_categories ul li.current-cat > a,
				.woocommerce table.shop_table td.product-price,.woocommerce table.shop_table tbody .product-subtotal,.woocommerce table.shop_table tbody .order-total .woocommerce-Price-amount,.woocommerce-order-details .amount, #order_review .amount,.woocommerce ul.order_details li .amount,.woocommerce-table--order-details tfoot .woocommerce-Price-amount,
				.btn-readmore,.woocommerce div.product p.price, .woocommerce div.product span.price,
				.detail-metas-top .type-property,.attachment-item .icon_type,.agent-item .property-job,.top-detail-member .property-job,
				.agency-item .category-agency,
				.attachment-item .candidate-detail-attachment i,.columns-gap li.yes::before,
				.property-list-simple .property-price,
				.apus-breadscrumb .breadcrumb .active,
				.post-layout .col-content .list-categories a,
				.property-item .type-property,
				a:focus,a:hover, .mm-menu .mm-listview > li.active > a, .mm-menu .mm-listview > li > a:hover, .mm-menu .mm-listview > li > a:focus, .property-grid-slider .property-price, .mm-menu .menu a:hover, .mm-menu .menu a:focus, .menu-item.current_page_item a , .type-banner-property.style3 .icon, .megamenu .dropdown-menu li:hover > a, .megamenu .dropdown-menu li.current-menu-item > a, .megamenu .dropdown-menu li.open > a, .megamenu .dropdown-menu li.active > a, .featured-property, .top-detail-member .agency-socials a:hover, .top-detail-member .agency-socials a:focus, .elementor-accordion .elementor-tab-title.elementor-active a, .widget-search .btn:hover, .widget-search .btn:focus, .list-options-action [type="radio"]:checked + label, .megamenu > li:hover > a, .megamenu > li.active > a
				{
					color: <?php echo esc_html( homeo_get_config('main_color') ) ?>;
				}
				.nav-member > li:hover > a, .nav-member > li.active > a, .nav-member > li:focus > a,
				.map-popup .icon-wrapper,
				.text-theme {
					color: <?php echo esc_html( homeo_get_config('main_color') ) ?> !important;
				}

				/* setting border color */
				#compare-sidebar,#properties-google-maps .marker-cluster,
				.pagination > span.current, .pagination > a.current, .apus-pagination > span.current, .apus-pagination > a.current,
				.pagination > span:focus, .pagination > span:hover, .pagination > a:focus, .pagination > a:hover, .apus-pagination > span:focus, .apus-pagination > span:hover, .apus-pagination > a:focus, .apus-pagination > a:hover,
				.pagination li > span.current, .pagination li > a.current, .apus-pagination li > span.current, .apus-pagination li > a.current,
				.pagination li > span:focus, .pagination li > span:hover, .pagination li > a:focus, .pagination li > a:hover, .apus-pagination li > span:focus, .apus-pagination li > span:hover, .apus-pagination li > a:focus, .apus-pagination li > a:hover,
				.border-theme, .mm-menu .mm-listview > li.active > .mm-next:after
				{
					border-color: <?php echo esc_html( homeo_get_config('main_color') ) ?>;
				}
				.elementor-accordion .elementor-tab-title.elementor-active{
					border-color: <?php echo esc_html( homeo_get_config('main_color') ) ?> !important;
				}
				.widget-property-search-form .nav-tabs > li.active > a::before {
				    border-color: <?php echo esc_html( homeo_get_config('main_color') ) ?> transparent transparent;
				}

			<?php endif; ?>

			<?php if ( homeo_get_config('button_color') != "" ) : ?>
				
				.btn-theme{
					border-color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
					background-color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
				}
				.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled], .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce #respond input#submit, .woocommerce input.button, .woocommerce button.button, .woocommerce a.button,
				.btn-theme.btn-outline{
					border-color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
					color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
				}
				.product-block.grid .add-cart .button,
				.btn-theme.btn-inverse, .phone-wrapper.phone-hide .phone-show span, .btn-theme-second, .add-fix-top{
					border-color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
					background-color: <?php echo esc_html( homeo_get_config('button_color') ) ?> ;
				}
			<?php endif; ?>

			<?php if ( homeo_get_config('button_hover_color') != "" ) : ?>
				/* seting background main */
				.subwoo-inner:hover a.button,
				.subwoo-inner .add-cart .added_to_cart,
				.product-block.grid .add-cart .added_to_cart,
				.woocommerce input.button:disabled:hover, .woocommerce input.button:disabled:focus, .woocommerce input.button:disabled[disabled]:hover, .woocommerce input.button:disabled[disabled]:focus, .woocommerce #respond input#submit.alt:hover, .woocommerce #respond input#submit.alt:focus, .woocommerce a.button.alt:hover, .woocommerce a.button.alt:focus, .woocommerce button.button.alt:hover, .woocommerce button.button.alt:focus, .woocommerce input.button.alt:hover, .woocommerce input.button.alt:focus, .woocommerce #respond input#submit:hover, .woocommerce #respond input#submit:focus, .woocommerce input.button:hover, .woocommerce input.button:focus, .woocommerce button.button:hover, .woocommerce button.button:focus, .woocommerce a.button:hover, .woocommerce a.button:focus,
				.btn-theme:hover,
				.btn-theme:focus,
				.btn-theme.btn-outline:hover,
				.btn-theme.btn-outline:focus, .phone-wrapper.phone-hide .phone-show span, .btn-theme-second, .add-fix-top{
					border-color: <?php echo esc_html( homeo_get_config('button_hover_color') ) ?> ;
					background-color: <?php echo esc_html( homeo_get_config('button_hover_color') ) ?> ;
				}
				.btn-theme.btn-inverse:hover,
				.btn-theme.btn-inverse:focus{
					color: <?php echo esc_html( homeo_get_config('button_hover_color') ) ?> ;
					border-color: <?php echo esc_html( homeo_get_config('button_hover_color') ) ?> ;
				}
				.btn.btn-view-all-photos {
					color: <?php echo esc_html( homeo_get_config('button_hover_color') ) ?> ;
				}
			<?php endif; ?>

			<?php if ( homeo_get_config('header_mobile_color') != "" ) : ?>
				
				.header-mobile{
					background-color: <?php echo esc_html( homeo_get_config('header_mobile_color') ) ?>;
					border-color: <?php echo esc_html( homeo_get_config('header_mobile_color') ) ?>;
				}
			<?php endif; ?>

	<?php
		$content = ob_get_clean();
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$lines = explode("\n", $content);
		$new_lines = array();
		foreach ($lines as $i => $line) {
			if (!empty($line)) {
				$new_lines[] = trim($line);
			}
		}
		
		return implode($new_lines);
	}
}