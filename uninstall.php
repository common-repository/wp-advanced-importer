<?php

/**
 * Created by PhpStorm.
 * User: sujin
 * Date: 02/03/16
 * Time: 7:29 PM
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class WpXMLSmackUCIUnInstall {
	/**
	 * UnInstall UCI Pro.
	 */
	public static function uninstall() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$ucisettings = get_option('XML_SM_UCI_pro_settings');
		$droptable = isset($ucisettings['drop_table']) ? $ucisettings['drop_table'] : '';
		if(!empty($droptable) && $droptable == 'on'){
			WpXMLSmackUCIInstall::remove_options();
			$tables[] = 'drop table smack_field_types_xml';
			$tables[] = 'drop table smackuci_events_xml';
			$tables[] = 'drop table smackuci_history_xml';
			$tables[] = 'drop table wp_ultimate_csv_importer_log_values_xml';
			$tables[] = 'drop table wp_ultimate_csv_importer_manageshortcodes_xml';
			$tables[] = 'drop table wp_ultimate_csv_importer_mappingtemplate_xml';
			$tables[] = 'drop table wp_ultimate_csv_importer_shortcodes_statusrel_xml';
			foreach($tables as $table) {
				$wpdb->query($table, array());
			}
		}
	}
}
