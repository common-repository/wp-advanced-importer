<?php
/******************************************************
 * Plugin Name: WP Advanced Importer
 * Description: A plugin that helps to import the data's from a XML file.
 * Version: 2.4.1
 * Author: smackcoders.com
 * Text Domain: wp-advanced-importer
 * Domain Path: /languages
 * Plugin URI: http://www.smackcoders.com/wp-advanced-importer.html?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer
 * Author URI: http://www.smackcoders.com/wp-advanced-importer.html?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer
 */
/********************************************************
 * WP Advanced Importer is a Tool for importing XML for the Wordpress
 * plugin developed by Smackcoders. Copyright (C) 2014 Smackcoders.
 *
 * WP Advanced Importer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Advanced
 * Importer, WP Advanced Importer DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * WP Advanced Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * WP Advanced Importer copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2015. All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WpXMLSM_WPUltimateCSVImporterPro' ) ) :
	/**
	 * Main WPUltimateCSVImporterPro Class.
	 *
	 * @class WPUltimateCSVImporterPro Class
	 * @version     5.0
	 */
	class WpXMLSM_WPUltimateCSVImporterPro {

		public $version = '2.4.1';

		/**
		 * The single instance of the class.
		 *
		 * @var $_instance
		 * @since 5.0
		 */
		protected static $_instance = null;

		/**
		 * Main WPUltimateCSVImporterPro Instance.
		 *
		 * Ensures only one instance of WPUltimateCSVImporterPro is loaded or can be loaded.
		 *
		 * @since 4.5
		 * @static
		 * @return SM_WPUltimateCSVImporterPro - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * SM_WPUltimateCSVImporterPro Constructor.
		 */
		public function __construct() {
			include_once ( 'includes/class-uci-install.php' );
			include_once ( 'uninstall.php' );

			do_action( 'wp_advanced_importer_loaded' );
			add_filter( 'cron_schedules', array('WpXMLSmackUCIInstall', 'cron_schedules'));
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array('WpXMLSmackUCIInstall', 'plugin_row_meta'), 10, 2 );

			# Custom content after plugin row meta starts
			# Custom content after plugin row meta ends

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( is_plugin_active('wp-advanced-importer/index.php') ) {
				add_action( 'admin_notices', array( 'WpXMLSmackUCIInstall', 'wp_ultimate_csv_importer_notice' ) );
				add_action( 'admin_notices', array('WpXMLSmackUCIInstall', 'important_cron_notice') );
			}
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 * @since  4.5
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( 'WpXMLSmackUCIInstall', 'install' ) );
			add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
			add_action( 'init', array( $this, 'smack_uci_enqueue_scripts') );
			add_action('wp_dashboard_setup', array($this,'uci_pro_add_dashboard_widgets'));
			add_action('smack_uci_email_scheduler', array('WpXMLSmackUCIEmailScheduler', 'send_login_credentials_to_users'));
			add_action('smack_uci_image_scheduler', array('WpXMLSmackUCIMediaScheduler', 'populateFeatureImages'));
			// add_action('smack_uci_cron_scheduler', array('SmackUCIScheduleManager', 'smack_uci_cron_scheduler'));
			register_deactivation_hook( __FILE__, array( 'WpXMLSmackUCIUnInstall', 'uninstall' ) );
		}

		/**
		 * Define SmackUCI Constants.
		 */
		public function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'XML_SM_UCI_PLUGIN_FILE', __FILE__ );
			$this->define( 'XML_SM_UCI_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'XML_SM_UCI_VERSION', $this->version );
			$this->define( 'XML_SM_UCI_DELIMITER', ',' );
			$this->define( 'XML_SM_UCI_PRO_DIR', plugin_dir_path(__FILE__));
			$this->define( 'XML_SM_UCI_PRO_URL', WP_PLUGIN_URL.'/wp-advanced-importer');
			$this->define( 'XML_SM_UCI_LOG_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/import_logs/' );
			$this->define( 'XML_SM_UCI_DEFAULT_UPLOADS_DIR', $upload_dir['basedir'] );
			$this->define( 'XML_SM_UCI_FILE_MANAGING_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/' );
			$this->define( 'XML_SM_UCI_IMPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/imports' );
			$this->define( 'XML_SM_UCI_IMPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/imports' );
			$this->define( 'XML_SM_UCI_EXPORT_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/exports/' );
			$this->define( 'XML_SM_UCI_EXPORT_URL', $upload_dir['baseurl'] . '/smack_uci_uploads/exports/' );
			$this->define( 'XML_SM_UCI_ZIP_FILES_DIR', $upload_dir['basedir'] . '/smack_uci_uploads/zip_files/' );
			$this->define( 'XML_SM_UCI_INLINE_IMAGE_DIR', $upload_dir['basedir'] . '/smack_inline_images/' );
			$this->define( 'XML_SM_UCI_SCREENS_DATA',$upload_dir['basedir'].'/smack_uci_uploads/screens_data');
			$this->define( 'XML_SM_UCI_SESSION_CACHE_GROUP', 'smack_uci_session_id' );
			$this->define( 'XML_SM_UCI_SETTINGS', 'WP Advanced Importer' );
			$this->define( 'XML_SM_UCI_NAME', 'WP Advanced Importer' );
			$this->define( 'XML_SM_UCI_SLUG', 'wp-advanced-importer' );
			$this->define( 'XML_SM_UCI_DEBUG_LOG', $upload_dir['basedir'] . '/wp-advanced-importer.log');
		}


		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name
		 * @param  string|bool $value
		 */
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			foreach ( glob( plugin_dir_path( __FILE__ ) . "helpers/*.php" ) as $file ) {
				include_once ("$file");
			}
			include_once ( 'includes/class-uci-helper.php' );
			include_once ( 'libs/parsers/SmackCSVParser.php' );
			include_once ( 'libs/parsers/SmackXMLParser.php' );
			include_once ( 'libs/parsers/SmackNewXMLParser.php' );
			include_once ( 'includes/class-uci-admin-ajax.php' );
			include_once ( 'includes/class-uci-event-logging.php' );
			// We are in admin mode
			include_once ( 'admin/xml-class-uci-admin.php' );
			include_once ( 'includes/class-uci-email-scheduler.php' );
			include_once ( 'includes/class-uci-media-scheduler.php' );
			//include_once ( 'managers/class-uci-schedulemanager.php' );
			//include_once ( 'SmackUCIWebServices.php' );
		}

		public function smack_uci_enqueue_scripts() {
			// Register / Enqueue the plugin scripts & style
			$uciPages = array('smack-uci-dashboard', 'smack-uci-import', 'smack-uci-managers',  'smack-uci-settings', 'smack-uci-support');
			if (isset($_REQUEST['page']) && in_array(sanitize_text_field($_REQUEST['page']), $uciPages)) {
				// Register & Enqueue the plugin styles
				wp_enqueue_style( 'ultimate-css', plugins_url( 'assets/css/ultimate-importer.css', __FILE__ ) );
				wp_enqueue_style( 'boot.css', plugins_url( 'assets/css/bootstrap.css', __FILE__ ) );
				wp_enqueue_style( 'Icomoon Icons', plugins_url( 'assets/css/icomoon.css', __FILE__ ) );
				wp_enqueue_style( 'Animate CSS', plugins_url( 'assets/css/animate.css', __FILE__ ) );
				wp_enqueue_style( 'jquery-fileupload.css', plugins_url( 'assets/css/jquery.fileupload.css', __FILE__ ) );
				wp_enqueue_style( 'jquery-style', plugins_url( 'assets/css/jquery-ui.css', __FILE__ ) );
				wp_enqueue_style('icheck', plugins_url('assets/css/icheck/green.css', __FILE__));
				wp_enqueue_style( 'file-tree-css', plugins_url( 'assets/css/jqueryfiletree.css', __FILE__ ) );
				// WaitMe CSS & JS for blur the page and show the progressing loader
				wp_enqueue_style('waitme-css', plugins_url('assets/css/waitMe.css', __FILE__));
				wp_enqueue_style('sweet-alert-css', plugins_url('assets/css/sweetalert.css', __FILE__));
				wp_enqueue_style('custom-style', plugins_url('assets/css/custom-style.css', __FILE__));
				//new files include
				wp_enqueue_style('custom-new-style', plugins_url('assets/css/custom-new-style.css', __FILE__));
				wp_enqueue_style( 'bootstrap-select-css', plugins_url( 'assets/css/bootstrap-select.css', __FILE__ ));
				// Register & Enqueue the plugin scripts
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'icheck-js', plugins_url( 'assets/js/icheck.min.js', __FILE__ ) );
				wp_register_script( 'ultimate-importer-js', plugins_url( 'assets/js/ultimate-importer.js', __FILE__ ) );
				wp_enqueue_script( 'ultimate-importer-js' );
				//wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_script( 'bootstrap-datepicker-js', plugins_url( 'assets/js/bootstrap-datepicker.js', __FILE__ ) );
				wp_enqueue_script( 'bootstrap-datepicker-js' );
				wp_enqueue_style( 'bootstrap-datepicker-css', plugins_url('assets/css/bootstrap-datepicker.css', __FILE__ ) );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script('jquery-ui-draggable');
				wp_enqueue_script('jquery-ui-droppable');
				wp_enqueue_script('jquery-ui-core');
				#wp_enqueue_script( 'jquery-ui-core' );
				#wp_register_script( 'uci-jquery-ui-min', plugins_url('assets/js/jquery-ui.min.js', __FILE__) );
				#wp_enqueue_script( 'uci-jquery-ui-min' );
				#wp_register_script( 'uci-jquery-min', plugins_url('assets/js/jquery.min.js', __FILE__) );
				#wp_enqueue_script( 'uci-jquery-min' );
				#wp_enqueue_script( '' );
				wp_enqueue_script( 'file-tree', plugins_url( 'assets/js/jqueryfiletree.js', __FILE__ ) );
				wp_localize_script( 'ultimate-importer-js', 'uci_importer', array(
					'adminurl' => admin_url(),
					'siteurl'  => site_url(),
					'requestpage' => sanitize_text_field($_REQUEST['page']),
					'db_orphanedMsg' => __('no of Orphaned Post/Page meta has been removed.', 'wp-advanced-importer'),
					'db_tagMsg' => __('no of Unassigned tags has been removed.', 'wp-advanced-importer'),
					'db_revisionMsg' => __('no of Post/Page revisions has been removed.', 'wp-advanced-importer'),
					'db_draftMSg' => __('no of Auto drafted Post/Page has been removed.', 'wp-advanced-importer'),
					'db_trashMsg' => __('no of Post/Page in trash has been removed.', 'wp-advanced-importer'),
					'db_spamMsg' => __('no of Spam comments has been removed.', 'wp-advanced-importer'),
					'db_commentTrashMsg' => __('no of Comments in trash has been removed.', 'wp-advanced-importer'),
					'db_unapprovedMsg' => __('no of Unapproved comments has been removed.', 'wp-advanced-importer'),
					'db_pingbackMsg' => __('no of Pingback comments has been removed.', 'wp-advanced-importer'),
					'db_trackbackMsg' => __('no of Trackback comments has been removed.', 'wp-advanced-importer'),

				) );
				wp_register_script('bootstrap-js', plugins_url('assets/js/bootstrap.js', __FILE__));
				wp_enqueue_script('bootstrap-js');
				wp_register_script('bootstrap-select-js', plugins_url('assets/js/bootstrap-select.js', __FILE__));
				wp_enqueue_script('bootstrap-select-js');
				// Sidebar Sticky JS
				wp_register_script('stickySidebar-js', plugins_url('assets/js/stickySidebar.js', __FILE__));
				wp_enqueue_script('stickySidebar-js');
				//new files include close
				wp_register_script('waitme-js', plugins_url('assets/js/waitMe.js', __FILE__));
				wp_enqueue_script('waitme-js');
				// Sweet Alert Js
				wp_register_script('sweet-alert-js', plugins_url('assets/js/sweetalert-dev.js', __FILE__));
				wp_enqueue_script('sweet-alert-js');
				// Tinymce Editor Js
				// wp_register_script('ckeditor-js', plugins_url('assets/js/ckeditor-js/ckeditor.js', __FILE__));
				// wp_enqueue_script('ckeditor-js');

				wp_register_script('ckeditor-js', plugins_url('assets/js/ckeditor5-build-classic/build/ckeditor.js', __FILE__));
				wp_enqueue_script('ckeditor-js');

				//MODAL POP UP JS
				wp_enqueue_script('pop-up',plugins_url('assets/js/modal.js',__FILE__));
				// Morris chart CSS & JS for dashboard
				if(isset($_REQUEST['page']) && sanitize_text_field($_REQUEST['page']) == 'smack-uci-dashboard') {
					wp_enqueue_script( 'chart-utils-js', plugins_url('assets/js/chart-js/utils.js', __FILE__) );
					wp_enqueue_script( 'uci-dashboard', plugins_url('assets/js/chart-js/Chart.js', __FILE__) );
					wp_enqueue_script( 'uci-dashboard-chart', plugins_url( 'assets/js/chart-js/dashchart.js', __FILE__ ) );
				}
			}
			wp_enqueue_style('style-maintenance', plugins_url('assets/css/style-maintenance.css', __FILE__));
		}

		/**
		 * Init SM_WPUltimateCSVImporterPro when WordPress Initialises.
		 */
		public function init() {
			if(is_admin()) :
				// Init action.
				do_action( 'uci_init' );
				if(is_admin()) {
					WpXMLWpXMLSmackUCIAdminAjax::smuci_ajax_events();
					remove_image_size( 'thumbnail' );
					remove_image_size( 'medium' );
					remove_image_size( 'medium_large' );
					remove_image_size( 'large' );
				}
			endif;
		}

		public function uci_pro_add_dashboard_widgets(){
			wp_enqueue_script( 'chart-utils-js', plugins_url('assets/js/chart-js/utils.js', __FILE__) );
			wp_enqueue_script( 'uci-dashboard-chart-widget', plugins_url( 'assets/js/chart-js/dashchart-widget.js', __FILE__ ) );
			// Add widget on WordPress Dashboard
			$get_current_user = wp_get_current_user();
			$role = $get_current_user->roles[0];
			if( $role == "administrator" ) {
				wp_add_dashboard_widget( 'uci_pro_dashboard_linechart', 'Ultimate-CSV-Importer-Pro-Activity', array(
					'WpXMLSmackUCIAdmin',
					'LineChart'
				), $screen = get_current_screen(), 'advanced', 'high' );
				wp_add_dashboard_widget( 'uci_pro_dashboard_piechart', 'Ultimate-CSV-Importer-Pro-Statistics', array(
					'WpXMLSmackUCIAdmin',
					'PieChart'
				), $screen = get_current_screen(), 'advanced', 'high' );
			}
		}
		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get Ajax URL.
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Email Class.
		 * @return XML_SM_UCI_Emails
		 */
		public function mailer() {
			return XML_SM_UCI_Emails::instance();
		}
	}
endif;

add_action('plugins_loaded','WpXMLSmackCSVImporterLoadLanguages');
function WpXMLSmackCSVImporterLoadLanguages(){
	$wp_csv_importer_pro_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	load_plugin_textdomain( XML_SM_UCI_SLUG , false, $wp_csv_importer_pro_lang_dir );
}

/**
 * Main instance of WPUltimateCSVImporterPro.
 *
 * Returns the main instance of WC to prevent the need to use globals.
 *
 * @since  4.5
 * @return WPUltimateCSVImporterPro
 */
function WpXMLSmackUCI() {
	return WpXMLSM_WPUltimateCSVImporterPro::instance();
}
// Global for backwards compatibility.
$GLOBALS['wp_advanced_importer'] = WpXMLSmackUCI();