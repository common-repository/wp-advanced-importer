<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

include_once ( plugin_dir_path(__FILE__) . '../includes/class-uci-helper.php' );

class WpXMLSmackUCIAdmin extends WpXMLSmackUCIHelper {

	public function __construct() {
		self::initializing_scheduler();
	}

	public static function show_admin_menus() {
		$is_author_can_import = get_option('XML_SM_UCI_pro_settings', null);
		$is_author_can_import = isset($is_author_can_import['author_editor_access']) ? $is_author_can_import['author_editor_access'] : '';
		if ( apply_filters( 'XML_SM_UCI_enable_setup_wizard', true ) && is_user_logged_in() &&  current_user_can( 'administrator' ) ) {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menus' ) );
		}
		if ( is_user_logged_in() && ( current_user_can( 'author') || current_user_can('editor') ) && $is_author_can_import == 'on' ) {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menus_for_other_roles' ) );
		}

		#add_filter( 'custom_menu_order', array( __CLASS__, 'smack_uci_reorder_admin_menu' ) );
		#add_filter( 'menu_order', array( __CLASS__, 'smack_uci_reorder_admin_menu' ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public static function admin_menus() {
		global $submenu;
		add_menu_page(XML_SM_UCI_SETTINGS, XML_SM_UCI_NAME, 'manage_options', XML_SM_UCI_SLUG, array(__CLASS__, 'XML_SM_UCI_screens'), plugins_url("assets/images/wp-ultimate-csv-importer.png", dirname(__FILE__)));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME,  esc_html__('Dashboard', 'wp-advanced-importer'), 'manage_options', 'smack-uci-dashboard', array(__CLASS__, 'XML_SM_UCI_screens'));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME,  esc_html__('Import/Update', 'wp-advanced-importer'), 'manage_options', 'smack-uci-import', array(__CLASS__, 'XML_SM_UCI_screens'));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME,  esc_html__('Settings', 'wp-advanced-importer'), 'manage_options', 'smack-uci-settings', array(__CLASS__, 'XML_SM_UCI_screens'));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME,  esc_html__('Support', 'wp-advanced-importer'), 'manage_options', 'smack-uci-support', array(__CLASS__, 'XML_SM_UCI_screens'));
		unset($submenu[XML_SM_UCI_SLUG][0]);
	}

	public static function admin_menus_for_other_roles() {
		global $submenu;
		add_menu_page(XML_SM_UCI_SETTINGS, XML_SM_UCI_NAME, '2', XML_SM_UCI_SLUG, array(__CLASS__, 'XML_SM_UCI_screens'),plugins_url("assets/images/wp-ultimate-csv-importer.png",dirname(__FILE__)));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME, esc_html__('Dashboard', 'wp-advanced-importer'), '2', 'smack-uci-dashboard', array(__CLASS__, 'XML_SM_UCI_screens'));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME, esc_html__('Import/Update', 'wp-advanced-importer'), '2', 'smack-uci-import', array(__CLASS__, 'XML_SM_UCI_screens'));
		add_submenu_page(XML_SM_UCI_SLUG, XML_SM_UCI_NAME, esc_html__('Support', 'wp-advanced-importer'), '2', 'smack-uci-support', array(__CLASS__, 'XML_SM_UCI_screens'));
		unset($submenu[XML_SM_UCI_SLUG][0]);
	}

	/**
	 * Re-order left admin menu
	 * @param $menu_ord
	 *
	 * @return array
	 */
	public static function smack_uci_reorder_admin_menu( $menu_ord ) {
		if (!$menu_ord) return true;

		return array(
			'index.php', // Dashboard
			'edit.php?post_type=page', // Pages
			'edit.php', // Posts
			'upload.php', // Media
			'wp-advanced-importer/index.php', // CSV Importer
			'wp-leads-builder-any-crm-pro/index.php', // Leads Builder
			'wp-helpdesk-integration-pro/index.php', // HelpDesk Integration
			'wordpress-echosign-pro/echosign.php', // Echo Sign
			'themes.php', // Appearance
			/* 'separator1', // --Space--
			'edit-comments.php', // Comments
			'users.php', // Users
			'separator2', // --Space--
			'plugins.php', // Plugins
			'tools.php', // Tools
			'options-general.php', // Settings */
		);
	}

	public static function XML_SM_UCI_screens() {
		global $xml_uci_admin;
		$xml_uci_admin->show_top_navigation_menus();
		switch (sanitize_title($_REQUEST['page'])) {
			case 'smack-uci-dashboard':
				$xml_uci_admin->show_uci_dashboard();
				break;
			case 'smack-uci-import':
				$xml_uci_admin->show_import_screen();
				break;
			case 'smack-uci-settings':
				$xml_uci_admin->show_settings_screen();
				break;
			case 'smack-uci-support':
				$xml_uci_admin->show_support_screen();
				break;
			default:
				break;
		}
		return false;
	}

	public function show_top_navigation_menus() {
		echo '<div class="menu_bar wp_ultimate_csv_importer_pro">
                <h2 class="nav-tab-wrapper">
                        <a href="'. esc_url (admin_url() .'admin.php?page=smack-uci-dashboard') .'" class="nav-tab nav-tab-active" id = "menu1">'.esc_html__('Dashboard','wp-advanced-importer').'</a>
                        <a href="'. esc_url (admin_url() .'admin.php?page=smack-uci-import') . '" class="nav-tab" id = "menu2">'.esc_html__('Import','wp-advanced-importer').'</a>
                        <a href="'. esc_url (admin_url() .'admin.php?page=smack-uci-settings') . '" class="nav-tab" id = "menu5">'.esc_html__('Settings','wp-advanced-importer').'</a>
                        <a href="'. esc_url (admin_url() .'admin.php?page=smack-uci-support') . '" class="nav-tab" id = "menu6">'.esc_html__('Support','wp-advanced-importer').'</a>
                </h2>
		        </div>
				<div id="notification_wp_csv"></div>';

		$myDir = XML_SM_UCI_DEFAULT_UPLOADS_DIR;
		if(is_dir($myDir)) {
			echo "<input type='hidden' id='is_found' name='is_found' value='dir found'/>";
		} else {
			echo "<input type='hidden' id='is_found' name='is_found' value='dir not found'/>";
		} if(is_writable($myDir)) {
			echo "<input type='hidden' id='is_perm_found' name='is_perm_found' value='perm found'/>";
		} else {
			echo "<input type='hidden' id='is_perm_found' name='is_perm_found' value='perm not found'/>";
		}
	}

	public function show_import_screen() {
		global $xml_uci_admin;
		$parserObj = new WpXMLSmackCSVParser();
		$xml_uci_admin->show_notices($parserObj);
		$step = isset($_REQUEST['step']) ? sanitize_title($_REQUEST['step']) : '';
		switch ($step) {
			case 'import_file':     // Step one
				include ( 'views/xml-form-file-import-method.php' );
				break;
			case 'mapping_config':  // Step two
				if(isset($_REQUEST['eventKey']) ? sanitize_key($_REQUEST['eventKey']):'' ) :
					if(isset($_POST) && !empty($_POST)) :
						$records['import_mode'] = sanitize_text_field($_POST['import_mode']);   
						$records['posttype'] = sanitize_text_field($_POST['posttype']);
						$records['uploaded_name'] = sanitize_text_field($_POST['uploaded_name']);
						$records['file_name'] = sanitize_text_field($_POST['file_name']);
						$records['file_extension'] = sanitize_text_field($_POST['file_extension']);
						$records['import_method'] = sanitize_text_field($_POST['import_method']);
						$records['file_version'] = sanitize_text_field($_POST['file_version']);
						$records['upload_max'] = sanitize_text_field($_POST['upload_max']);
					
						$parserObj->screenData = array('import_file' => $records);

						$eventKey = sanitize_key($_REQUEST['eventKey']);
						update_option($eventKey, $parserObj->screenData);
					else:
						$eventKey = sanitize_key($_REQUEST['eventKey']);
						$parserObj->screenData = get_option($eventKey);
					endif;
				endif;
				if(empty($parserObj->screenData)):
					$parserObj->wp_session = "Your mapping configuration may lost. Please configure your mapping again!";
				endif;
				if(isset($_REQUEST['mapping_type'])) {
					$mapping_type = sanitize_text_field($_REQUEST['mapping_type']);
				} else {
					$mapping_type = '';
				}
				switch($mapping_type) {
					case 'advanced':
					default:
						include ( 'views/xml-form-advanced-mapping-configuration.php' );
						break;
				}
				break;
			case 'import_config':
				include ( 'views/xml-form-import-configuration.php' );
				break;
			case 'confirm':
				include ( 'views/xml-form-ignite-import.php' );
				break;
			default:
				include ( 'views/xml-form-file-import-method.php' );
				break;
		}
		return true;
	}

	public function show_manager_screen() {
		include ('views/form-manager-view.php' );
		return true;
	}
	
	public function show_uci_dashboard() {
               include ('views/xml-form-dashboard-view.php');
        }

	public function show_settings_screen() {
                include ('views/xml-form-settings-view.php' );
                return true;
	}

	public function show_support_screen() {
		include ('views/xml-form-support-view.php' );
		return true;
	}

	public function show_notices($parseObj) {

	}

	public static function PieChart(){
		echo '<canvas id="uci_pro_pieStats"></canvas>';
	}

	public static function LineChart(){
		echo '<canvas id="uci_pro_lineStats" style="height:250px;"></canvas>';
	}

	public function get_mapping_widgets_order ($import_type) {
		$widgets = array();
		global $xml_uci_admin;
		$customposts = $xml_uci_admin->get_import_custom_post_types();
		if (in_array($import_type, get_taxonomies())) {
			$import_type = 'Taxonomies';
		}
		if (in_array($import_type, $customposts)) {
			$import_type = 'CustomPosts';
		}
		switch ($import_type) {
			case 'Posts':
			case 'Pages':
			case 'CustomPosts':
			case 'Events':
			case 'ticket':
				$widgets = array('Core Fields', 'WordPress Custom Fields', 'ACF Pro Fields', 'ACF Fields', 'ACF Repeater Fields',
					'Types Custom Fields', 'PODS Custom Fields', 'CCTM Custom Fields', 'All-in-One SEO Fields',
					'Yoast SEO Fields', 'Terms and Taxonomies', 'Custom-Field-Suite Fields');
				break;
			case 'Users':
				$widgets = array('Core Fields','WordPress Custom Fields', 'Custom Fields by WP-Members', 'Billing And Shipping Information',
					'ACF Pro Fields', 'ACF Fields', 'ACF Repeater Fields', 'Types Custom Fields', 'PODS Custom Fields', 'CCTM Custom Fields');
				break;
			case 'Comments':
				$widgets = array('Core Fields');
				break;
			case 'WooCommerce':
			case 'MarketPress':
			case 'WPeCommerce':
			case 'eShop':
				$widgets = array('Core Fields', 'Product Meta Fields', 'WordPress Custom Fields', 'ACF Pro Fields',
					'ACF Fields', 'ACF Repeater Fields', 'Types Custom Fields', 'PODS Custom Fields', 'CCTM Custom Fields',
					'All-in-One SEO Fields', 'Yoast SEO Fields', 'Terms and Taxonomies');
				if($import_type == 'WPeCommerce')
					$widgets = array_merge($widgets,array('WP e-Commerce Custom Fields'));
				break;
			case 'WPeCommerceCoupons':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'WooCommerceVariations':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'WooCommerceOrders':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'WooCommerceCoupons':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'WooCommerceRefunds':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'MarketPressVariations':
				$widgets = array('Core Fields', 'Product Meta Fields');
				break;
			case 'Taxonomies':
				$widgets = array('Core Fields');
				break;
			case 'Tags':
				$widgets = array('Core Fields');
				break;
			case 'Categories':
				$widgets = array('Core Fields');
				break;
			case 'CustomerReviews':
				$widgets = array('Core Fields');
				break;
			default:
				$widgets = array('Core Fields');
				break;
		}
		return $widgets;
	}

	public function available_widgets ($import_type, $importAs) {

		global $xml_uci_admin;
		$widgets = array();
		$widgets_order = $xml_uci_admin->get_mapping_widgets_order($import_type);
		$supported_widgets = $xml_uci_admin->get_available_widgets($import_type, $importAs);
		if(!empty($widgets_order) && !empty($supported_widgets)) :
			foreach($widgets_order as $item) {
				# TODO: Need to check whether this item (widget) to be related with the $import_type
				if(array_key_exists($item, $supported_widgets)) :
					$widgets[$item] = $supported_widgets[$item];
				endif;
			}
		endif;
		return $widgets;
	}

	public function get_available_widgets ($import_type, $importAs) {
		$possible_widgets = array();
		global $xml_uci_admin;
		$customposts = $xml_uci_admin->get_import_custom_post_types();
		if (in_array($import_type, $customposts)) {
			$import_type = 'CustomPosts';
		}
		if (in_array($import_type, get_taxonomies())) {
			$import_type = 'Taxonomies';
		}
		$possible_widgets['Core Fields'] = 'CORE';
		if($import_type == 'Posts' || $import_type == 'Pages' || $import_type == 'CustomPosts' ||
		   $import_type == 'WooCommerce' || $import_type == 'MarketPress' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type == 'Users') {
			$possible_widgets['WordPress Custom Fields'] = 'CORECUSTFIELDS';
		}

		$active_plugins = $this->get_active_plugins();
		if(!empty($active_plugins)) {
			foreach($active_plugins as $plugin) {
				switch ($plugin) {
					case 'advanced-custom-fields-pro/acf.php':
						$possible_widgets['ACF Pro Fields'] = 'ACF';
						$possible_widgets['ACF Repeater Fields'] = 'RF';
						break;
					case 'advanced-custom-fields/acf.php':
						/** ACF PRO version 5.3.7 */
						$acf_pro_pluginPath = WP_PLUGIN_DIR . '/advanced-custom-fields/pro';
						if(is_dir($acf_pro_pluginPath))
							$possible_widgets['ACF Pro Fields'] = 'ACF';
						else
							$possible_widgets['ACF Fields'] = 'ACF';
						break;
					case 'acf-repeater/acf-repeater.php':
						$possible_widgets['ACF Repeater Fields'] = 'RF';
						break;
					case 'custom-content-type-manager/index.php':
						$possible_widgets['CCTM Custom Fields'] = 'CCTM';
						break;
					case 'types/wpcf.php':
						$possible_widgets['Types Custom Fields'] = 'TYPES';
						break;
					case 'pods/init.php':
						$possible_widgets['PODS Custom Fields'] = 'PODS';
						break;
					case 'all-in-one-seo-pack/all_in_one_seo_pack.php':
						$possible_widgets['All-in-One SEO Fields'] = 'AIOSEO';
						break;
					//allinseo_customization
					case 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php':
						$possible_widgets['All-in-One SEO Fields'] = 'AIOSEO';
						break;
					//allinseo_customization
					//cmb2_customization
					case 'cmb2/init.php':
						$possible_widgets['CMB2 Fields'] = 'CMB2';
						break;
					//cmb2_customization
					// Custom Field Suite Support
					case 'custom-field-suite/cfs.php':
						$possible_widgets['Custom-Field-Suite Fields'] = 'CFS';
						break;
					// Custom Field Suite Support
					case 'wordpress-seo/wp-seo.php':
						$possible_widgets['Yoast SEO Fields'] = 'YOASTSEO';
						break;
					case 'wordpress-seo-premium/wp-seo-premium.php':
						$possible_widgets['Yoast SEO Fields'] = 'YOASTSEO';
						break;
					case 'wp-e-commerce-custom-fields/custom-fields.php':
						$possible_widgets['WP e-Commerce Custom Fields'] = 'WPECOMMETA';
						break;
					case 'wp-members/wp-members.php':
						if($import_type == 'Users') {
							$possible_widgets['Custom Fields by WP-Members'] = 'WPMEMBERS';
						}
						break;
					//ultimate Member
					case 'ultimate-member/index.php':
						if($import_type == 'Users') {
							$possible_widgets['Custom Fields by Ultimate Member'] = 'ULTIMATEMEMBER';
						}
						break;
					//ultimate Member
					case 'woocommerce/woocommerce.php':
					case 'marketpress/marketpress.php':
					case 'wordpress-ecommerce/marketpress.php':
					case 'wp-e-commerce/wp-shopping-cart.php':
					case 'eshop/eshop.php':
						$possible_widgets['Product Meta Fields'] = 'ECOMMETA';
						break;
					case 'sitepress-multilingual-cms/sitepress.php':
						$possible_widgets['Multi Lingual Support'] = 'WPML';
						break;
				}
			}
			if($import_type == 'Users') {
				if( in_array('marketpress/marketpress.php', $this->get_active_plugins()) || in_array('wordpress-ecommerce/marketpress.php', $this->get_active_plugins()) || in_array('woocommerce/woocommerce.php', $this->get_active_plugins()) ) {
					$possible_widgets['Billing And Shipping Information'] = 'BSI';
				}
			}
			if($import_type != 'Pages')
				$possible_widgets['Terms and Taxonomies'] = 'TERMS';
		}
		return $possible_widgets;
	}

	/**
	 * @param $import_type
	 * @param $mode
	 *
	 * @return array
	 */
	public function get_update_fields($import_type,$mode){
		if($mode == 'existing_items') {
			$fields = array( post_title, ID, post_name);
			if($import_type == 'WooCommerce' || $import_type == 'WPeCommerce' || $import_type == 'eShop' || $import_type == 'MarketPress'){
				array_push($fields,"PRODUCTSKU");
			}
		}
		else {
			$fields = array( post_title, ID, post_name, post_content );
		}
		return $fields;
	}

	public function get_widget_fields($widget_name, $import_type, $importAs, $mode = null) {
		$fields = array();
		switch ($widget_name) {
			case 'Core Fields':
				if($importAs === 'event' || $importAs === 'location' || $importAs === 'event-recurring') {
					$fields = $this->coreFields($importAs);
				} else {
					$fields = $this->coreFields($import_type);
				}
				break;
			case 'Product Meta Fields':
				if($import_type === 'WPeCommerce') :
					$fields = $this->ecommerceMetaFields($import_type);
				elseif ($import_type == 'WooCommerce'):
					$fields = $this->ecommerceMetaFields($import_type);
					if(in_array('woocommerce-chained-products/woocommerce-chained-products.php', $this->get_active_plugins())) :
						$fields['ECOMMETA'] = array_merge($fields['ECOMMETA'], $this->WooCommerceChainedProductFields());
					endif;
					if(in_array('woocommerce-product-retailers/woocommerce-product-retailers.php', $this->get_active_plugins())) :
						$fields['ECOMMETA'] = array_merge($fields['ECOMMETA'], $this->WooCommerceProductRetailerFields());
					endif;
					if(in_array('woocommerce-product-addons/product-addons.php', $this->get_active_plugins())) :
						$fields['ECOMMETA'] = array_merge($fields['ECOMMETA'], $this->WooCommerceProductAddOnsFields());
					endif;
					if(in_array('woocommerce-warranty/woocommerce-warranty.php', $this->get_active_plugins())) :
						$fields['ECOMMETA'] = array_merge($fields['ECOMMETA'], $this->WooCommerceWarrantyFields());
					endif;
					if(in_array('woocommerce-pre-orders/woocommerce-pre-orders.php', $this->get_active_plugins())) :
						$fields['ECOMMETA'] = array_merge($fields['ECOMMETA'], $this->WooCommercePreOrderFields());
					endif;
				else:
					$fields = $this->ecommerceMetaFields($import_type);
				endif;
				break;
			case 'WordPress Custom Fields':
				$fields = $this->WPCustomFields($import_type, $importAs, $mode);
				break;
			case 'ACF Pro Fields':
				//$fields = $this->ACFProCustomFields($import_type, $importAs, $mode, 'ACF');
				break;
			case 'ACF Fields':
				//$fields = $this->ACFCustomFields($import_type, $importAs, $mode);
				break;
			case 'ACF Repeater Fields':
				// if(in_array('advanced-custom-fields-pro/acf.php', $this->get_active_plugins())) {
				// 	$fields = $this->ACFProCustomFields($import_type, $importAs, $mode, 'RF');
				// } else {
				// 	$fields = $this->ACFRepeaterFields( $import_type, $importAs, $mode );
				// }
				break;
			case 'Types Custom Fields':
				$fields = $this->TypesCustomFields($import_type, $importAs, $mode);
				break;
			case 'PODS Custom Fields':
				$fields = $this->PODSCustomFields($import_type, $importAs, $mode);
				break;
			case 'CCTM Custom Fields':
				$fields = $this->CCTMCustomFields();
				break;
			case 'All-in-One SEO Fields':
				$fields = $this->AIOSEOFields();
				break;
			case 'Yoast SEO Fields':
				$fields = $this->YoastSEOFields();
				break;
			//cmb2_customization
			case 'CMB2 Fields':
				$fields = $this->CMB2Fields();
				break;
			//cmb2_customization
			case 'Custom-Field-Suite Fields':
				$fields = $this->CFSFields();
				break;
			case 'Billing And Shipping Information':
				$fields = $this->billing_information_for_users();
				break;
			case 'WP e-Commerce Custom Fields':
				$fields = $this->WPeCommerceCustomFields();
				break;
			case 'Custom Fields by WP-Members':
				$fields = $this->custom_fields_by_wp_members();
				break;
			case 'Custom Fields by Ultimate Member':
				$fields = $this->custom_fields_by_ultimate_member();
				break;
			case 'Terms and Taxonomies':
				$fields = $this->terms_and_taxonomies($import_type, $importAs, $mode);
				break;
		}
		return $fields;
	}
	
	public function get_available_groups($type) {
		$groups = array();
		if ($type == 'Posts') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'CORECUST', 'TERMS', 'TAXO');
		}
		if ($type == 'Pages') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'CORECUST', 'TAXO');
		}
		if ($type == 'WooCommerce') {
			$groups = array('CORE', 'CCTM', 'ACF', 'RF', 'TYPES', 'PODS', 'AIOSEO', 'YOASTSEO', 'ECOMMETA', 'CORECUST', 'TERMS', 'TAXO');
		}
		return $groups;
	}

	public static function initializing_scheduler() {
		if( !wp_next_scheduled( 'smack_uci_cron_scheduler' ) ) {
			wp_schedule_event(time(), 'wp_ultimate_csv_importer_scheduled_csv_data', 'smack_uci_cron_scheduler');
		}
		if( !wp_next_scheduled( 'smack_uci_cron_scheduled_export' ) ) {
			wp_schedule_event(time(), 'wp_ultimate_csv_importer_scheduled_csv_data', 'smack_uci_cron_scheduled_export');
		}
		if( !wp_next_scheduled( 'smack_uci_image_scheduler' )) {
			wp_schedule_event(time(), 'wp_ultimate_csv_importer_scheduled_images', 'smack_uci_image_scheduler');
		}
		if( !wp_next_scheduled( 'smack_uci_email_scheduler' )) {
			wp_schedule_event(time(), 'wp_ultimate_csv_importer_scheduled_emails', 'smack_uci_email_scheduler');
		}
		if( !wp_next_scheduled( 'smack_uci_replace_inline_images' )) {
			wp_schedule_event(time(), 'wp_ultimate_csv_importer_replace_inline_images', 'smack_uci_replace_inline_images');
		}
	}
}
add_action('init', array('WpXMLSmackUCIAdmin', 'show_admin_menus'));
global $xml_uci_admin;
$xml_uci_admin = new WpXMLSmackUCIAdmin();