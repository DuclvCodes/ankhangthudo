<?php
/**
 * Importer for apus themer
 *
 * @package    apus-framework
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Themer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Apus_Import {
	
	public $errors = array();
	public $sucess = array();
	public $steps = array(
			'first_settings' => 'content',
			'content' => 'widgets',
			'widgets' => 'settings',
			'settings' => 'revslider',
			'revslider' => 'done'
		);
	public function __construct() {

		define( 'APUS_IMPORT_CONFIG_DIR', get_template_directory() . '/inc/samples/'  );

		$demo_data_file_path = APUS_IMPORT_CONFIG_DIR . 'sample-data.php';
		if ( is_file( $demo_data_file_path ) ) {
			require $demo_data_file_path;
		}
		if ( isset($import_steps) ) {
			$this->steps = $import_steps;
		}
		if ( isset($demo_import_base_dir) ) {
    		define( 'APUS_IMPORT_SAMPLES_DIR', $demo_import_base_dir );
    	} else {
    		define( 'APUS_IMPORT_SAMPLES_DIR', get_template_directory() . '/inc/samples/' );
    	}

		define( 'APUS_RECOMMEND_MEMORY_LIMIT', 128 );
      	define( 'APUS_RECOMMEND_EXECUTION_TIME', - 1 );
     	define( 'APUS_RECOMMEND_PHP_VERSION', '5.4.0' );

		add_action('admin_menu', array( &$this, 'create_admin_menu' ) );
		add_action( 'wp_ajax_apus_import_sample', array( $this, 'import_sample' ) );
		add_action( 'admin_init', array( $this, 'get_remote_sampledata') );
	
	}

 	public function create_admin_menu() {
 		if ( apply_filters('apus_frammework_enable_one_click_import', true) ) {
			add_submenu_page(
				'tools.php',
				__( 'Apus Demo Import', 'apus-framework' ),
				__( 'Apus Demo Import', 'apus-framework' ),
				'manage_options',
				'apus-import-demo',
				array( $this, 'apus_page_content' )
			);
		}
	}

	public function get_remote_sampledata() {
 		if ( isset($_GET['doaction']) && $_GET['doaction'] == 'download-sample' ) {
			if ( !is_dir(APUS_IMPORT_SAMPLES_DIR) ) {
				mkdir(APUS_IMPORT_SAMPLES_DIR, 0777);
			}
			$theme_info = wp_get_theme();
			$source = isset($_GET['source']) ? $_GET['source'] : '';
			$theme_name = $theme_info->get( 'TextDomain' ) . (!empty($source) ? '-'.$source : '');

			if ( $theme_name ) {
				$lpackage = APUS_IMPORT_SAMPLES_DIR.'samples.zip';
				$remote_file = 'http://apusthemes.com/demosamples/'.$theme_name.'.zip';
				
				$data = file_get_contents( $remote_file );
				$file = fopen( $lpackage, "w+" );
				fputs($file, $data);
				fclose($file);

				if ( file_exists($lpackage) ) {
					WP_Filesystem();
					unzip_file( $lpackage , APUS_IMPORT_SAMPLES_DIR );
				}
				@unlink( $lpackage );
				wp_redirect( admin_url('tools.php?page=apus-import-demo') );
			}
 		}
 	}

	public function import_sample() {
		@ini_set( 'max_execution_time', '1200' );
		@ini_set( 'post_max_size', '64M');
		
		$demo_source = isset($_REQUEST['demo_source']) ? $_REQUEST['demo_source'] : '';
		$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : '';
		$ajax = isset($_REQUEST['ajax']) ? $_REQUEST['ajax'] : '';
		$res = array();
		if ( $demo_source && $import_type ) {
			$fnc_call = 'import_'.$import_type;
			$res = call_user_func(array($this, $fnc_call), $demo_source);
		}

		echo json_encode($res); die();
	}

	public function outputJson( $status, $msg, $log = '', $loop = false ) {
		$res = array(
			'status'  => $status,
			'msg' => $mgs,
			'log'     => $log,
			'loop'	  => $loop,
			'loopnumber' => 0
		);
		$import_type = isset($_REQUEST['import_type']) ? $_REQUEST['import_type'] : '';

		if ($loop) {
			$res['next'] = $import_type;
		} else {
			$res['next'] = isset($this->steps[$import_type]) ? $this->steps[$import_type] : 'error';
		}
		return $res;
	}
	/**
	 * Import first settings
	 */
	public function import_first_settings($source) {
		$file = APUS_IMPORT_SAMPLES_DIR.'data/'.$source.'/first_settings.json';
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson( false, esc_html__( 'Data is error! file: ', 'apus-framework') . $file, '' );
			}

			foreach ($datas as $key => $options) {
				if ( $key == 'page_options' ) {
					$this->import_page_options($options);
				}
			}
		}
		return $this->outputJson( true, __("Import First Settings Successful", "apus-framework"),  $log );
	}
	/**
	 * Import data sample from xml.
	 */
	public function import_content($source) {
		session_start();
		$return = apply_filters( 'apus_themer_cancel_import_content', false );
		if ( $return ) {
			$data = $this->outputJson( true, '' );
		}
		$file_name = apply_filters( 'apus_themer_get_xml_file_name', 'data.xml' );

		$path = APUS_IMPORT_SAMPLES_DIR.'data/'.$file_name;
		$source_path = APUS_IMPORT_SAMPLES_DIR.'data/'.$source.'/'.$file_name;
		if ( file_exists($source_path) ) {
			$path = $source_path;
		}
		if ( file_exists($path) ) {

			if (!class_exists('WP_Importer')) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) ) {
					require_once( $class_wp_importer );
				}
			}
			ob_start();
            
			require_once APUS_FRAMEWORK_DIR . 'importer/wordpress-importer.php';
            
			$apus_import = new WP_Import();

			if( !isset($_SESSION['importpostcount']) ){
            	$_SESSION['importpoststart'] = 0;
            	$_SESSION['importpostcount'] = 0;	
            	if( method_exists("deleteCaches", $apus_import)){
            		$apus_import->deleteCaches();
            	}
            }

			set_time_limit(0);
			
			$apus_import->fetch_attachments = true;
			$returned_value = $apus_import->import($path);

			$log = ob_get_clean();
  			$data = $this->outputJson( true, '',  $log, !$returned_value );
			$data['loopnumber'] = $_SESSION['importpostcount'];

			if( $returned_value == true ){
				unset( $_SESSION['importpoststart'] );
				unset( $_SESSION['importpoststart'] );
			}
			$this->res_json = $data;
			return $this->res_json;
		} else {
			$data = $this->outputJson( false, __("Error loading data.xml file", "apus-framework"), '' );
		}
		$this->res_json = $data;
		return $this->res_json;
	}

	public function import_widgets( $source ){
 		$file = APUS_IMPORT_SAMPLES_DIR.'data/'.$source.'/widgets.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$options = json_decode( $datas, true );
			if( $options['widgets'] ){
				foreach ( (array) $options['widgets'] as $id_widget => $widget_data ) {
					update_option( 'widget_' . $id_widget, $widget_data );
				}
				return $this->import_sidebars_widgets($options);
			}
		} else {
			return $this->outputJson( false, __("Error loading widgets.json file", "apus-framework"), '' );
		}
		return $this->outputJson( true, __("Widgets imported successfully", "apus-framework"), '' );
	}

	public function import_sidebars_widgets( $options ) { 

		$sidebars = get_option("sidebars_widgets");
		unset($sidebars['array_version']);
		
		if ( is_array($options['sidebars']) ) {
			$sidebars = array_merge( (array) $sidebars, (array) $options['sidebars'] );
			
			unset($sidebars['wp_inactive_widgets']);
			
			$sidebars = array_merge(array('wp_inactive_widgets' => array()), $sidebars);
			$sidebars['array_version'] = 2;
			wp_set_sidebars_widgets($sidebars);
		} else {
			return $this->outputJson( false, __("Missing widgets data", "apus-framework"), '' );
		}

		return $this->outputJson( true, __("Import Sidebars Widgets Successful", "apus-framework"),  $log );
	}

	/**
	 * Import data to revolutions
	 */
	public function import_revslider($source) {
		if ( ! class_exists( 'RevSliderAdmin' ) ) {
			require( RS_PLUGIN_PATH . '/admin/revslider-admin.class.php' );			
		}
		if ( is_dir(APUS_IMPORT_SAMPLES_DIR . 'data/revslider/') ) {
			$path = APUS_IMPORT_SAMPLES_DIR . 'data/revslider/';
		} else {
			$path = APUS_IMPORT_SAMPLES_DIR . 'data/' . $source . '/revslider/';
		}

		if ( is_dir($path) ) {
			$rev_files = glob( $path . '*.zip' );
			if (!empty($rev_files)) {
				ob_start();
				foreach ($rev_files as $rev_file) {
					$_FILES['import_file']['error'] = UPLOAD_ERR_OK;
					$_FILES['import_file']['tmp_name']= $rev_file;

					$slider = new RevSlider();
					$slider->importSliderFromPost( true, true );
				}
				ob_get_clean();
			}
		} else {
			return $this->outputJson( false, esc_html__( 'revslider folder is not exists! folder: ', 'apus-framework') . $path, '' );
		}
		return $this->outputJson( true, __("Import Slider", "apus-framework"),  $log );
	}
	
	public function import_settings($source) {
		$file = APUS_IMPORT_SAMPLES_DIR.'data/'.$source.'/settings.json';
		$res = array();
		if ( file_exists($file) ) {
			$datas = file_get_contents( $file );
			$datas = json_decode( $datas, true );

			if ( count( array_filter( $datas ) ) < 1 ) {
				return $this->outputJson( false, esc_html__( 'Data is error! file: ', 'apus-framework') . $file, '' );
			}

			if ( !empty($datas['page_options']) ) {
				$this->import_page_options($datas['page_options']);
			}
			if ( !empty($datas['metadata']) ) {
				$this->import_some_metadatas($datas['metadata']);
			}
			if ( !empty($datas['menu']) ) {
				$this->import_menu($datas['menu']);
			}
		} else {
			return $this->outputJson( false, esc_html__( 'File is not exists! file:', 'apus-framework') . $file, '' );
		}
		return $this->outputJson( true, __("Import Settings Successful", "apus-framework"),  $log );
	}

	public function import_menu($datas) {
		global $wpdb;
		$terms_table = $wpdb->prefix . "terms";

		if ( $datas ) { 
			$menu_array = array();
			foreach ($datas as $registered_menu => $menu_slug) {
				$term_rows = $wpdb->get_results("SELECT * FROM $terms_table where slug='{$menu_slug}'", ARRAY_A);
				if(isset($term_rows[0]['term_id'])) {
					$term_id_by_slug = $term_rows[0]['term_id'];
				} else {
					$term_id_by_slug = null;
				}
				$menu_array[$registered_menu] = (int)$term_id_by_slug;
			}

			set_theme_mod('nav_menu_locations', $menu_array );
		}
	}

	public function import_page_options($datas) {
		if ( $datas ) {
			foreach ($datas as $option_name => $page_id) {
				update_option( $option_name, $page_id);
			}
		}
	}
	
	public function import_some_metadatas($datas) {
		if ( $datas ) {
			foreach ($datas as $slug => $post_types) {
				if ( $post_types ) {
					foreach ($post_types as $post_type => $metas) {
						if ( $metas ) {
							$args = array(
			                    'name'        => $slug,
			                    'post_type'   => $post_type,
			                    'post_status' => 'publish',
			                    'numberposts' => 1
			                );
			                $posts = get_posts($args);
			                if ( $posts && isset($posts[0]) ) {
								foreach ($metas as $meta) {
									update_post_meta( $posts[0]->ID, $meta['meta_key'], $meta['meta_value'] );
									if ( $meta['meta_key'] == '_mc4wp_settings' ) {
										update_option( 'mc4wp_default_form_id', $posts[0]->ID );
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function set_error($text) {
		$this->errors[] = $text;
	}

	public function set_sucess($text) {
		$this->sucess[] = $text;
	}

	public function get_ini_configs($key) {
		$all_ini_configs = ini_get_all();
		$value = ini_get( $key );

		$arr_value = $all_ini_configs[ $key ];
		if ( isset($arr_value['local_value']) ) {
			$value = $arr_value['local_value'];
		}
		return $value;
	}

	public function apus_page_content() {
		// script
		wp_enqueue_style( 'apus-framework-backend', APUS_FRAMEWORK_URL . 'assets/backend.css', array(), APUS_FRAMEWORK_VERSION );
		wp_enqueue_script( 'apus-framework-import', APUS_FRAMEWORK_URL . 'assets/import.js', array( 'jquery' ), APUS_FRAMEWORK_VERSION, true );

		$demo_data_file_path = APUS_IMPORT_CONFIG_DIR . 'sample-data.php';
		$demo_data_dir_path  = APUS_IMPORT_CONFIG_DIR;
		if ( is_file( $demo_data_file_path ) ) {
			require $demo_data_file_path;
		} else {
			$demo_datas = array();
		}
		
		$memory_limit = $this->get_ini_configs('memory_limit');
		$max_execution_time = $this->get_ini_configs('max_execution_time');

		$is_ok = true;

		?>
		
		<div class="wrap">
			<h1><?php esc_html_e( 'ApusTheme Demo Importer', 'apus-framework' ); ?></h1>
			<div class="update-nag apus_notification">
				<p>
					<?php _e( '<strong>Warning:</strong> If you have already used this feature before and you want to try it again, your content may be duplicated. Please consider resetting your database back to defaults with <a href="//wordpress.org/plugins/wordpress-reset/">this plugin</a>.', 'apus-framework' ); ?>
				</p>
			</div>
			
			<?php
			if ( intval( $max_execution_time ) < 600 ) {
				if ( ini_get( 'safe_mode' ) ) {
					?>
					<div class="error apus_notification">
						<p>
							<?php _e( 'Please enable PHP\'s safe mode. Or contact to your server to increase "max_execution_time" to 600', 'apus-framework' ); ?>
						</p>
					</div>
					<?php
					$is_ok = false;
				}
			}
			?>
			<br/>

			<?php if ( intval( $memory_limit ) < APUS_RECOMMEND_MEMORY_LIMIT ) { ?>
				<div class="error apus_notification">
					<p>
						<?php printf( __( '<strong>Important:</strong> The Importer requires memory limit of your system >= %1$sMB.', 'apus-framework' ), APUS_RECOMMEND_MEMORY_LIMIT ); ?>
					</p>
				</div>
			<?php } ?>

			<div class="apus-demo-import-wrapper">
				<div class="themes">
					<?php
					$attr_button_import = '';
					if ( !$is_ok ) {
						$attr_button_import = 'data-disabled="true"';
					}
					?>
					<?php
					if ( isset($demo_datas) && !empty($demo_datas) ) {
						?>
						<?php if ( count($demo_datas) > 1 ) { ?>
							<label><?php esc_html_e( 'Choose a demo', 'apus-framework' ); ?></label>
							<select class="source-data" name="source">
								<?php foreach ($demo_datas as $key => $value) { ?>
									<option value="<?php echo esc_attr($key); ?>"><?php echo $value['title']; ?></option>
								<?php } ?>
							</select>
							<br>
							<br>
						<?php } else { ?>
							<select class="source-data hidden" name="source">
								<?php foreach ($demo_datas as $key => $value) { ?>
									<option value="<?php echo esc_attr($key); ?>"><?php echo $value['title']; ?></option>
								<?php } ?>
							</select>
						<?php } ?>
						<div>
							<button class="button button-primary apus-btn-import" <?php echo $attr_button_import; ?>><?php esc_html_e( 'Click Here To Import Demo Data', 'apus-framework' ); ?></button>
						</div>
						<?php
					} else {
						?>
						<div class="update-nag">
			                <?php _e( "Click to the follow buttons to get sample demo from our live sites, the package will put into Theme-Folder/inc/samples folder. <br> Please make sure this folder has writeable permision",'apus-framework' );?>
						</div>
						<br>
						<br>
						<div class="download-btn" style="text-align: left;">
							<?php
								$btn_html = '<a class="button button-primary" href="'.admin_url( 'tools.php?page=apus-import-demo', 'http' ).'&doaction=download-sample">'.esc_html__('Download Demos', 'apus-framework').'</a>';
								$download_btns = apply_filters( 'apus_themer_get_download_buttons', $btn_html );

								echo $download_btns;
							?>
						</div>
						<br>
						<br>
						<?php
					}
					?>
				</div>
				<br class="clear">
			</div>
		</div>

		<section class="apus-progress-content">
			<div class="container">
				<div class="wrapper-content">

					<h1><?php esc_html_e( 'Importing', 'apus-framework' ); ?></h1>

					<div class="row">
						<div class="apus_progress_import">
							<p class="note"><?php esc_html_e( 'The import process can take about 10 minutes. Please don\'t refresh the page.', 'apus-framework' ); ?></p>
							<ol class="steps">
							<?php
								$steps = apply_filters( 'apus_frammework_progress_import_steps', array(
									'first_settings' => array( 'default' => __('Install First Settings', 'apus-framework'), 'installing' => __('Installing First Settings ...', 'apus-framework'), 'installed' => __('Installed First Settings', 'apus-framework') ),
									'content' => array( 'default' => __('Install Demo Content', 'apus-framework'), 'installing' => __('Installing Demo Content ...', 'apus-framework'), 'installed' => __('Installed Demo Content', 'apus-framework') ),
									'widgets' => array( 'default' => __('Install Widgets', 'apus-framework'), 'installing' => __('Installing Widgets ...', 'apus-framework'), 'installed' => __('Installed Widgets', 'apus-framework') ),
									'settings' => array( 'default' => __('Install Settings', 'apus-framework'), 'installing' => __('Installing Settings ...', 'apus-framework'), 'installed' => __('Installed Settings', 'apus-framework') ),
									'revslider' => array( 'default' => __('Install Revolution Slider', 'apus-framework'), 'installing' => __('Installing Revolution Slider ...', 'apus-framework'), 'installed' => __('Installed Revolution Slider', 'apus-framework') ),
								));
								foreach ($steps as $key => $step) {
									?>
									<li class="<?php echo esc_attr($key); ?>">
										<span class="default"><?php echo $step['default']; ?></span>
										<span class="installing" style="display: none;"><?php echo $step['installing']; ?></span>
										<span class="installed" style="display: none;"><?php echo $step['installed']; ?></span>
									</li>
									<?php
								}
							?>
							</ol>
						</div>

						<div class="apus_progress_error_message">
							<div class="apus-error">
								<h4><?php esc_html_e( 'Failed to import!', 'apus-framework' ); ?></h4>
								<div class="content text_note apus_notification"></div>
							</div>
							<div class="log update-nag apus_notification">
								<h4><?php esc_html_e( 'Log', 'apus-framework' ); ?></h4>
								<div class="content text_note"></div>
							</div>
							<a class="button button-primary apus-support" href="#" target="_blank"><?php esc_html_e( 'Get support', 'apus-framework' ); ?></a>
							<a class="button button-secondary apus-visit-dashboard" href="<?php echo esc_url( get_admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'apus-framework' ); ?></a>
						</div>

						<div class="apus-complete">
							<h3 class=""><?php esc_html_e( 'Importing is successful!', 'apus-framework' ); ?></h3>
							<div class="content-message"></div>
							<p class="note"><?php esc_html_e( 'You can go to Settings > Reading to change other Home page', 'apus-framework' ); ?></p>
						</div>
						<br class="clear">
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}

new Apus_Import();