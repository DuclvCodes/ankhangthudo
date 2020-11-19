<?php
/**
 * WPML
 *
 * @package    wp-job-board
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_RealEstate_WPML {
	
	public static function init() {
		// add_action('save_post', array(__CLASS__, 'wpml_translate_post'), 1000, 1);
	}

	public static function get_icl_object_id($post_id, $post_type) {
		if (function_exists('icl_object_id') && function_exists('wpml_init_language_switcher')) {
			global $sitepress;
            $current_lang = $sitepress->get_current_language();
            $icl_post_id = icl_object_id($post_id, $post_type, false, $current_lang);

            if ($icl_post_id > 0) {
                $post_id = $icl_post_id;
            }
        }
        return $post_id;
	}

	public static function get_all_translations_object_id($post_id) {
		if ( function_exists('icl_object_id') && function_exists('wpml_init_language_switcher') ) {
			global $sitepress;
			$trid = $sitepress->get_element_trid($post_id);
			$translations = $sitepress->get_element_translations($trid);
			$post_ids = array();
			foreach ($translations as $key => $translation) {
				$post_ids[] = $translation->element_id;
			}
		} else {
			$post_ids = array($post_id);
		}
		
        return $post_ids;
	}
	
	public static function wpml_translate_post( $post_id ) {
 
	    global $iclTranslationManagement, $sitepress, $ICL_Pro_Translation;
	    if ( !isset( $iclTranslationManagement ) ) {
	        if(!class_exists('TranslationManagement')) {
	        	$file_management = ABSPATH.'wp-content/plugins/sitepress-multilingual-cms/inc/translation-management/translation-management.class.php';
	        	$file_translation = ABSPATH.'wp-content/plugins/sitepress-multilingual-cms/inc/translation-management/pro-translation.class.php';
	        	if ( file_exists($file_management) && file_exists($file_translation) ) {
	        		include($file_management);
	            	include($file_translation);
	        	} else {
	        		return;
	        	}
	            
	        }
	        $iclTranslationManagement = new TranslationManagement;
	        $ICL_Pro_Translation      = new ICL_Pro_Translation();
	    }
	 
	    // don't save for autosave
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return $post_id;
	    }
	    // save only for campaign
	    $post_type = get_post_type($post_id);
	    if ( !in_array($post_type, array('property', 'agent', 'agency')) ) {
	        return $post_id;
	    }
	 	
	    // get languages
	    $langs = $sitepress->get_active_languages();
	    unset($langs[$sitepress->get_default_language()]);
	 	
	    // unhook this function so it doesn't loop infinitely
	 	remove_action('save_post', array(__CLASS__, 'wpml_translate_post'), 1000, 1);

	    //now lets create duplicates of all new posts in all languages used for translations
	    foreach($langs as $language_code => $v){
	        $iclTranslationManagement->make_duplicate($post_id, $language_code);
	    }
	    //add_action('save_post', array(__CLASS__, 'wpml_translate_post'), 1000, 1);
	}
}

WP_RealEstate_WPML::init();