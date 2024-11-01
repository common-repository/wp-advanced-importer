<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class WpXMLWpXMLSmackUCIAdminAjax {

	public static function smuci_ajax_events() {
		$ajax_actions = array(
			'upload_actions' => false,
			'ftp_actions' => false,
			'uci_picklist_handler' => false,
			'external_file_actions' => false,
			'file_treeupload' => false,
			'parseDataToImport' => false,
			'parseDataToExport' => false,
			'parseDataToScheduleExport' => false,
			'static_formula_method_handler' => false,
			'check_templatename' => false,
			'delete_template' => false,
			'update_template' => false,
			'delete_schedule' => false,
			'edit_schedule' => false,
			'filter_template' => false,
			'search_template' => false,
			'get_mediaimg_size' => false,
			'get_headerData' => false,
			'update_event' => false,
			'selectrevision' => false,
			'downloadFile' => false,
			'download_AllFiles' => false,
			'deleteFileEvent' => false,
			'deleteRecordEvent' => false,
			'deleteAllEvents' => false,
			'deleteScheduledFile' => false,
			'deleteAllScheduledEvent' => false,
			'trashRecords' => false,
			'downloadLog' => false,
			'register_acfpro_fields' => false,
			'register_acf_free_fields' => false,
			'delete_acf_pro_fields' => false,
			'delete_acf_free_fields' => false,
			'register_pods_fields' => false,
			'delete_pods_fields' => false,
			'add_bidirectional_fields' => false,
			'register_types_fields' => false,
			'delete_types_fields' => false,
			'check_CFRequiredFields' => false,
			'schedule_your_current_event' => false,
			'inlineimage_upload' => false,
			'set_post_types' => false,
			'FetchPieChartData' => false,
			'FetchBarStackedChartData' => false,
			'FetchLineChartData' => false,
			'options_savein_ajax' => false,
			'database_optimization_settings' => false,
			'database_optimization_process' => false,
			'upload_zipfile_handler' => false,
			'get_schedule_event_info' => false,
			'dismiss_notices' => false,
			'sendmail' => false,
			'send_subscribe_email' =>false,
			'retrieve_record' => false,
			'preview_record' => false,
			'rollback_now' => false,
			'clear_rollback' => false,
			'parseXmlDataToShow' => false,
			'treeNode' => false,
			'tableNode' => false,
		);
		foreach($ajax_actions as $action => $value ){
			add_action('wp_ajax_'.$action, array(__CLASS__, $action));
		}
	}

	public static function parseXmlDataToShow()
	{

		global $xml_uci_admin;
		$namespace = explode(":", sanitize_text_field($_POST['id']));
		if(isset($namespace[1]))
		$n = $namespace[1];
		else
		$n = sanitize_text_field($_POST['id']);

		$file = sanitize_text_field($_POST['path']);
		$treetype = isset($_POST['treetype']) ? sanitize_text_field($_POST['treetype']) : 'tree';
		$doc = new DOMDocument();
		$doc->load($file);

		  $nodes=$doc->getElementsByTagName($n);
		 // print_r($nodes);
	
		if($nodes->length < $_POST['pag'])
		 die('<div style="color:red;padding:20px">Maximum Limit Exceed!<div>');

		if(isset($_POST['pag']))
		  $i = intval($_POST['pag']) - 1;
		else
		  $i = 0;
		if($i < 0)
		  die('<div style="color:red;padding:20px">Node not available!<div>');

		while (is_object($finance = $doc->getElementsByTagName($n)->item($i))) {
			if($treetype == 'table')
		    $xml_uci_admin->tableNode($finance);
			else
			$xml_uci_admin->treeNode($finance);
		    die();
		    $i++;
		}
		die();
	}

	public static function dismiss_notices() {
		$notice = sanitize_text_field($_POST['notice']);
		update_option('smack_uci_' . $notice, 'off');
	}

	public static function get_schedule_event_info() {
		
		die();
	}

	public static function check_requiredfields() {
		global $wpdb;
		$req_arr = array();
		$wobj = new WPClassifyFields();
		$i = 0;
		$import_type = isset($_REQUEST['import_type']) ? sanitize_text_field($_REQUEST['import_type']) : '' ;
		//TYPES Fields
		$types_fields = $wobj->TypesCustomFields();
		if($import_type == 'users')
			$getOptions = get_option('wpcf-usermeta');
		else
			$getOptions = get_option('wpcf-fields');
		if(!empty($types_fields) && is_array($types_fields) && array_key_exists('TYPES',$types_fields)){
			foreach($types_fields['TYPES'] as $key => $value){
				$pt_title = $value['label'];
				if(is_array($getOptions)) {
					if(array_key_exists($pt_title,$getOptions)){
						foreach($getOptions[$pt_title] as $okey => $ovalue){
							if(is_array($ovalue)){
								if(array_key_exists('validate',$ovalue)){
									foreach($ovalue['validate'] as $typeskey => $typesval){
										if($typeskey == 'required'){
											if(is_array($typesval)){
												if(array_key_exists('active',$typesval)){
													if($typesval['active'] == 1){
														$req_arr[$i] = $value['name'];
														$i++;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

			}
		}

		//ACF Fields
		$acf_fields = $wobj->ACFCustomFields();
		if(!empty($acf_fields) && is_array($acf_fields) && array_key_exists('ACF',$acf_fields)){
			foreach($acf_fields['ACF'] as $key => $value){
				$pt_title = $value['label'];
				$acf_postcont = $wpdb->get_col($wpdb->prepare("SELECT post_content FROM $wpdb->posts where post_title = %s",$pt_title));
				if(!empty($acf_postcont)){
					$acf_postcont = unserialize($acf_postcont[0]);
					if($acf_postcont['required'] == 1){
						$req_arr[$i] = $value['name'];
						$i++;
					}
				}
			}
		}

		//ACF Fields (Free)
		$getacf_fields = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta
                                                        GROUP BY meta_key
                                                        HAVING meta_key LIKE 'field_%'
                                                        ORDER BY meta_key");
		if(!empty($getacf_fields) && is_array($getacf_fields)){
			foreach($getacf_fields as $acfkey =>$acfval){
				$acf_arr = @unserialize($acfval);
				if($acf_arr !== false){
					if(array_key_exists('required',$acf_arr) && $acf_arr['required'] == 1){
						$req_arr[$i] = $acf_arr['label'];
						$i++;
					}
				}
			}
		}

		//PODS Fields
		$pods_fields = $wobj->PODSCustomFields();
		if(!empty($pods_fields) && is_array($pods_fields) && array_key_exists('PODS',$pods_fields)){
			foreach($pods_fields['PODS'] as $key => $value){
				$pt_title = $value['label'];
				$pods_postid = $wpdb->get_col($wpdb->prepare("SELECT id FROM $wpdb->posts where post_title = %s",$pt_title));
				if(!empty($pods_postid)){
					$pods_postid = $pods_postid[0];
					$pods_reqval = $wpdb->get_col($wpdb->prepare("SELECT meta_value from $wpdb->postmeta where meta_key = %s and post_id = %d",'required',$pods_postid));
					if(!empty($pods_reqval)){
						if($pods_reqval[0] == 1){
							$req_arr[$i] = $value['name'];
							$i++;
						}
					}
				}
			}
		}
		$req_arr = json_encode($req_arr);
		print_r($req_arr);
		die();
	}

	public static function upload_actions() {
		include_once('class-uci-upload-handler.php');
		die();
	}

	public static function ftp_actions() {
		//include_once('class-uci-ftp-handler.php');
	}

	public static function  external_file_actions() {
		//include_once('class-uci-external-file-handler.php');
	}

	public static function file_treeupload() {
		//include_once('class-uci-file-tree-upload.php');
	}

	public static function parseDataToImport() {
		global $xml_uci_admin;
		$event_information = $xml_uci_admin->getEventInformation();
		if(empty($event_information)) {
			$xml_uci_admin->setEventKey(sanitize_key($_POST['postData']['event_key']));
			$xml_uci_admin->setImportType(sanitize_text_field($_POST['postData']['import_type']));
			$xml_uci_admin->setImportMethod(sanitize_text_field($_POST['postData']['importMethod']));
			$xml_uci_admin->setInsertedRowCount(intval( $_POST['postData']['inserted'] ));
			$xml_uci_admin->setUpdatedRowCount(intval( $_POST['postData']['updated'] ));
			$xml_uci_admin->setSkippedRowCount(intval( $_POST['postData']['skipped'] ));
			$additional_event_info = $xml_uci_admin->GetPostValues($xml_uci_admin->getEventKey());
			$xml_uci_admin->setEventFileInformation($additional_event_info[$xml_uci_admin->getEventKey()]['import_file']);
			$xml_uci_admin->setMappingConfiguration($additional_event_info[$xml_uci_admin->getEventKey()]['mapping_config']);
			//$xml_uci_admin->setMediaConfiguration($additional_event_info[$xml_uci_admin->getEventKey()]['media_handling']);
			$xml_uci_admin->setImportConfiguration($additional_event_info[$xml_uci_admin->getEventKey()]['import_config']);
			$xml_uci_admin->setFileType(pathinfo($additional_event_info[$xml_uci_admin->getEventKey()]['import_file']['uploaded_name'], PATHINFO_EXTENSION));

			// Assign import as
			$xml_uci_admin->setImportAs($xml_uci_admin->import_post_types(sanitize_text_field($_POST['postData']['import_type'])));
			// Assign import type
			#$importType = $_POST['postData']['import_type'];
			$importType = $xml_uci_admin->getImportType();
			$customPosts = $xml_uci_admin->get_import_custom_post_types();
			if (in_array($importType, get_taxonomies())) {
				if($importType == 'category' || $importType == 'product_category' || $importType == 'product_cat' || $importType == 'wpsc_product_category' || $importType == 'event-categories'):
					$importType = 'Categories';
				elseif($importType == 'product_tag' || $importType == 'event-tags' || $importType == 'post_tag'):
					$importType = 'Tags';
				else:
					$importType = 'Taxonomies';
				endif;
			}
			if (in_array($importType, $customPosts)) {
				$importType = 'CustomPosts';
			}

			// Get mode of the current event
			$mode = $xml_uci_admin->getEventFileInformation('import_mode');

			if($mode == 'new_items') {
				$xml_uci_admin->setMode('Insert');
			} else {
				$xml_uci_admin->setMode('Update');
			}
			$xml_uci_admin->setEventInstance($importType);
		}
		$startLimit = intval( $_POST['postData']['startLimit'] );
		$endLimit = intval( $_POST['postData']['endLimit'] );
		$limit = intval( $_POST['postData']['Limit'] );
		$totalCount = intval( $_POST['postData']['totalcount'] );
		$affectedRecords = array(
			'inserted'  => intval( $_POST['postData']['inserted'] ),
			'updated'   => intval( $_POST['postData']['updated'] ),
			'skipped'   => intval( $_POST['postData']['skipped'] )
		);
		$totalCount = intval( $_POST['postData']['totalcount'] );
		$data = $dataToBeImport = array();

		$eventMapping = $xml_uci_admin->getMappingConfiguration();
		//$mediaConfig = $xml_uci_admin->getMediaConfiguration();
		$importConfig = $xml_uci_admin->getImportConfiguration();
		$original_file_name = $xml_uci_admin->getEventFileInformation('uploaded_name');
		$file_name = $xml_uci_admin->getEventFileInformation('file_name');
		$version = $xml_uci_admin->getEventFileInformation('file_version');
		$mode = $xml_uci_admin->getMode();
		$fileType = $xml_uci_admin->getFileType();
		$importMethod = $xml_uci_admin->getImportMethod();
		$eventKey = $xml_uci_admin->getEventKey();
		$eventDir = XML_SM_UCI_IMPORT_DIR . '/' . $eventKey;
		$eventLog = '';
		// Mapped array for the event by group
		// Read file based on the $fileType, $offset & $limit
		switch($fileType) {
			case 'xml':
				// $parserObj = new WpXMLSmackXMLParser();
				// $eventFile = $xml_uci_admin->getUploadDirectory($parserObj) . '/' . $eventKey . '/' . $eventKey;
				// $root_element = $parserObj->getNodeOccurrences($eventFile);
				// $xml_arr = $parserObj->readData($eventFile, $startLimit, $limit);
				// $data = $xml_uci_admin->xml_file_data($xml_arr, $data);
			// $parserObj = new WpXMLSmackNewXMLImporter();
			// $eventFile = $xml_uci_admin->getUploadDirectory($parserObj) . '/' . $eventKey . '/' . $eventKey;
				$data = array();
				break;
			case 'csv':
			default:
				$parserObj = new WpXMLSmackCSVParser();
				$eventFile = $eventDir . '/' . $eventKey;
				$data = $parserObj->parseCSV($eventFile, $startLimit , $limit);
				break;
		}
		for ($i = $startLimit; $i < $endLimit; $i++) {
			try {
				$xml_uci_admin->importData($eventKey, $importType, $importMethod, $mode, '', $i, $eventMapping, $affectedRecords, '', $importConfig);
				$manage_records[$mode][] = $xml_uci_admin->getLastImportId();
				$detailed_log = $xml_uci_admin->detailed_log;
				if (!empty($detailed_log)) {
					$uciEventLogger = new WpXMLSmackUCIEventLogging();
					$eventLogFile = $eventDir . '/'.$eventKey.'.log';
					$eventInfoFile = $eventDir . '/'.$eventKey.'.txt';
					$recordId = array($xml_uci_admin->getLastImportId());
					$contents = array();
					if(file_exists($eventInfoFile)) {
						$handle   = fopen( $eventInfoFile, 'r' );
						$contents = json_decode( fread( $handle, filesize( $eventInfoFile ) ) );
						fclose( $handle );
					}
					$fp = fopen($eventInfoFile, 'w+');
					if(!empty($contents) && $contents != null) {
						$contents = array_merge( $contents, $recordId );
						$contents = json_encode( $contents );
					} else {
						$contents = json_encode( $recordId );
					}
					fwrite($fp, $contents);
					fclose($fp);
					$uciEventLogger->lfile("$eventLogFile");
					if($startLimit == 1) {
						$uciEventLogger->lwrite(__("File has been used for this event: ") . $original_file_name, false);
						$uciEventLogger->lwrite(__("Type of the imported file: ") . $fileType, false);
						$uciEventLogger->lwrite(__("Revision of the which is used: ") . $version, false);
						$uciEventLogger->lwrite(__("Mode of event: ") . $mode, false);
						$uciEventLogger->lwrite(__("Total no of records: ") . $totalCount, false);
						$uciEventLogger->lwrite(__("Rows handled on each iterations (Based on your server configuration): ") . $limit, false);
						$uciEventLogger->lwrite(__("File used to import data into: ") . $xml_uci_admin->getImportAs() . ' (' . $importType . ')', false);
						#$fp = fopen($eventInfoFile, 'w+');
					}
					foreach ($xml_uci_admin->detailed_log as $lkey => $lvalue) {
						$eventLog = '<div style="margin-left:10px; margin-right:10px;"><table>';
						$verify_link = '';
						foreach ($lvalue as $lindex => $lresult) {
							if($lindex != 'VERIFY')
								$eventLog .= '<tr><td><p><b>' . $lindex . ': </b>' . $lresult . ' </td><p></tr>';
							else
								$verify_link = '<tr><td><p>' . $lresult . ' </td><p></tr>';
						}
						$eventLog .= $verify_link;
					}
					$eventLog .= '</table></div>';
					$uciEventLogger->lwrite($eventLog);
					$xml_uci_admin->setProcessedRowCount($i);
					echo json_encode(array(
							'total_no_of_rows' => $totalCount,
							'processed' => $xml_uci_admin->getProcessedRowCount(),
							'inserted' => $xml_uci_admin->getInsertedRowCount(),
							'updated'  => $xml_uci_admin->getUpdatedRowCount(),
							'skipped'  => $xml_uci_admin->getSkippedRowCount(),
							'eventLog' => $eventLog)
					);
				//skip empty row in csv file check starts
				}else{
					$eventLog = '<div style="margin-left:10px; margin-right:10px;"><table>';
                                        $eventLog .= '<tr><td><p><b>Message:</b> Skip empty row</p></td><p></tr>';
                                        $eventLog .= '</table></div>';
                                        echo json_encode(array(
                                                        'total_no_of_rows' => $totalCount,
                                                        'processed' => 0,
                                                        'inserted' => 0,
                                                        'updated'  => 0,
                                                        'skipped'  => 1,
                                                        'eventLog' => $eventLog)
                                        );
				}
				//skip empty row in csv file check ends 
			} catch (Exception $e) {
				$parserObj->logE('ERROR:', $e);
			}
		}

		$fileInfo = array(
			'file_name' => $file_name,
			'original_file_name' => $original_file_name,
			'file_type' => $fileType,
			'revision'  => $version,
		);
		$eventInfo = array(
			'count' => $totalCount,
			'processed' => $xml_uci_admin->getProcessedRowCount(),
			'inserted' => $xml_uci_admin->getInsertedRowCount(),
			'updated'  => $xml_uci_admin->getUpdatedRowCount(),
			'skipped'  => $xml_uci_admin->getSkippedRowCount(),
			'eventLog' => $eventLog
		);
		$xml_uci_admin->manage_records($manage_records, $fileInfo, $eventKey, $importType, $mode, $eventInfo);
		die();
	}

	public static function parseDataToExport() {
		// global $wpdb, $xml_uci_admin;
		// require_once ('class-uci-exporter.php');
		die();
	}

	public static function parseDataToScheduleExport() {
		
		die;
	}

	public static function static_formula_method_handler(){
		require_once(XML_SM_UCI_PRO_DIR . "admin/views/xml-form-static-formula-views.php");
		die();
	}

	public static function uci_picklist_handler() {
		require_once(XML_SM_UCI_PRO_DIR . "admin/views/form-add-custom-field.php");
		die();
	}

	public static function check_templatename() {
		global $wpdb;
		$where = '';
		$tempName = sanitize_text_field($_POST['templatename']);
		$templatename = addslashes($tempName);
		$templateid = isset($_REQUEST['templateid']) ? intval($_REQUEST['templateid']) : '';
		if ($templateid) {
			$where = " and id != $templateid";
		}
		$template_count = $wpdb->get_results("select count(*) as count from wp_ultimate_csv_importer_mappingtemplate_xml where templatename = '{$templatename}' $where");
		print_r($template_count[0]->count);
		die();
	}

	public static function delete_template() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-templatemanager.php");
		global $templateObj;
		$templateObj->deleteTemplate(intval($_POST['templateid']));
		die;
	}

	public static function update_template() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-templatemanager.php");
		global $templateObj;
		die;
	}

	public static function delete_schedule() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-schedulemanager.php");
		global $scheduleObj;
		$scheduleObj->deleteSchedule(sanitize_text_field($_POST['scheduleid']), sanitize_text_field($_POST['type']));
		die;
	}

	public static function edit_schedule() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-schedulemanager.php");
		global $scheduleObj;
		$scheduleObj->editSchedule(sanitize_text_field($_POST['schedule_data']));
		die;
	}

	public static function get_mediaimg_size() {
		global $_wp_additional_image_sizes;
		$sizes = array();
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'large','mediumlarge','custom') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			}
		}
		print_r(json_encode($sizes));
		die;
	}

	/**
	 * Filter the templates
	 */
	public static function filter_template() {
		global $wpdb;
		$filename = sanitize_text_field($_POST['filename']);
		$startDate = sanitize_text_field($_POST['startDate']);
		$endDate = sanitize_text_field($_POST['endDate']);
		$templateName = sanitize_text_field($_POST['search']);
		$offset = intval($_POST['offset']);
		$limit = intval($_POST['limit']);
		$filterclause = '';
		if (!empty($startDate) && !empty($endDate)) {
			$filterclause .= "createdtime between '$startDate%' and '$endDate%' and";
			$filterclause = substr($filterclause, 0, -3);
		} else {
			if (!empty($startDate)) {
				$filterclause .= "createdtime >= '%$startDate%' and";
				$filterclause = substr($filterclause, 0, -3);
			} else {
				if (!empty($endDate)) {
					$filterclause .= "createdtime <= '%$endDate%' and";
					$filterclause = substr($filterclause, 0, -3);
				}
			}
		}
		if (!empty($templateName)) {
			$filterclause .= " templatename like '%$templateName%'";
		}
		if (!empty($filterclause)) {
			$filterclause = "where $filterclause";
		}
		$templateCount = $wpdb->get_results("select count(*) from wp_ultimate_csv_importer_mappingtemplate_xml  " .$filterclause.  " and  csvname = '" . $filename ."'");
		foreach($templateCount[0] as $key => $value) {
			$count = $value;
		}
		if($count < $offset) {
			print_r('Count Exceeded.');
			die;
		}
		$templateList = $wpdb->get_results("select * from wp_ultimate_csv_importer_mappingtemplate_xml ".$filterclause." and csvname = '".$filename ."' limit ".$offset.",".$limit."");

		$template_detail = array();
		if(empty($templateList)) {
			print_r("Templates Not Found");
			die;
		} else {
			foreach($templateList as $templatedata) {
				$use_template = "<a href = ".esc_url(admin_url(). "admin.php?page=smack-uci-import&step=mapping_config&eventkey=". $templatedata->eventKey . "&templateid=" . $templatedata->id)." class='btn btn-success'>Use Template</a>";
				$template_detail[] = array('rowcount' => $templateCount[0],'id' => $templatedata->id,'name' => $templatedata->templatename,'file' => $templatedata->csvname,'module' => $templatedata->module,'createdat' => $templatedata->createdtime, 'use_template' => $use_template );
			}}
		print_r(json_encode($template_detail));
		die;
	}

	public static function search_template() {
		global $wpdb;
		$filename = sanitize_text_field($_POST['filename']);
		$templatename = sanitize_text_field($_POST['templatename']);
		$templateList = $wpdb->get_results("select * from wp_ultimate_csv_importer_mappingtemplate_xml where templatename like '".$templatename."%' and csvname= '".$filename."'");
		$template_detail = array();
		if(empty($templateList)) {
			print_r("Templates Not Found");
		}
		else {
			foreach($templateList as $templatedata) {
				$use_template = "<a href = ".esc_url(admin_url(). "admin.php?page=smack-uci-import&step=mapping_config&eventkey=". $templatedata->eventKey . "&templateid=" . $templatedata->id)." class='btn btn-success'>Use Template</a>";
				$template_detail[] = array('id' => $templatedata->id,'name' => $templatedata->templatename,'file' => $templatedata->csvname,'module' => $templatedata->module,'createdat' => $templatedata->createdtime, 'use_template' => $use_template );
			}}
		print_r(json_encode($template_detail));
		die;
	}

	public static function get_headerData() {
		global $xml_uci_admin;
		$eventkey = sanitize_text_field($_POST['eventkey']);
		$headers = $_POST['headers'];
		$post_values = $xml_uci_admin->GetPostValues($eventkey);
		$core_group = array('Core Fields' => 'CORE');
		$import_data = $xml_uci_admin->generateDataArrayBasedOnGroups($core_group,$post_values[$eventkey]['mapping_config']);
		$headers = array_intersect_key($import_data['CORE'],$headers);
		print_r(json_encode($headers));
		die;
	}

	public static function schedule_your_current_event() {
		global $scheduleObj;
		$schedule_msg = $scheduleObj->saveEventInformationToSchedule(); //generateSchedule();
		print_r(json_encode($schedule_msg));
		die;
	}

	public static function update_event() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		die;
	}

	public static function downloadFile() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->downloadFile(sanitize_text_field($_POST['event_id']), sanitize_text_field($_POST['revision']), sanitize_text_field($_POST['filename']));
		die;
	}

	public static function selectrevision() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->selectrevisiondetails(sanitize_text_field($_POST['event_id']), sanitize_text_field($_POST['revision']), sanitize_text_field($_POST['filename']));
		die;
	}


	public static function deleteFileEvent() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->deleteFiles(sanitize_text_field($_POST['path']), sanitize_text_field($_POST['id']), sanitize_text_field($_POST['filename']), sanitize_text_field($_POST['version']));
		die;
	}

	public static function deleteRecordEvent() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->deleteRecords(sanitize_text_field($_POST['filename']), sanitize_text_field($_POST['version']), sanitize_text_field($_POST['module']), sanitize_text_field($_POST['importas']));
		die;
	}

	public static function deleteAllEvents() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->deleteAll(sanitize_text_field($_POST['id']), sanitize_text_field($_POST['filename']), sanitize_text_field($_POST['module']));
		die;
	}

	public static function deleteAllScheduledEvent() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->deleteAll_scheduledEvent(sanitize_text_field($_POST['schedule_idList']), intval($_POST['file_id']));
		die;
	}

	public static function trashRecords() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->transactRecords(sanitize_text_field($_POST['id']), sanitize_text_field($_POST['module']), sanitize_text_field($_POST['filename']), sanitize_text_field($_POST['status']));
		die;
	}

	public static function deleteScheduledFile() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->deleteSchedule_Files();
		die;
	}

	public static function downloadLog() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-logmanager.php");
		global $log_managerObj;
		$log_managerObj->logDownload();
		die;
	}

	public static function download_AllFiles() {
		require_once(XML_SM_UCI_PRO_DIR . "managers/class-uci-filemanager.php");
		global $fileObj;
		$fileObj->downloadAllFiles(sanitize_text_field($_POST['id']));
		die;
	}

	/** Third party custom field Registration & Deletion **/
	public static function register_acfpro_fields() {
		global $acfHelper;
		$acfHelper->Register_ProFields();
	}

	public static function register_acf_free_fields() {
		global $acfHelper;
		$acfHelper->Register_FreeFields();
	}

	public static function register_pods_fields() {
		global $podsHelper;
		$podsHelper->Register_Fields();
	}

	public static function register_types_fields() {
		global $typesHelper;
		$typesHelper->Register_Fields();
	}

	public static function delete_acf_pro_fields() {
		global $acfHelper;
		$acfHelper->Delete_ProFields();
	}

	public static function delete_acf_free_fields() {
		global $acfHelper;
		$acfHelper->Delete_FreeFields();
	}

	public static function delete_pods_fields() {
		global $podsHelper;
		$podsHelper->Delete_Fields();
	}

	public static function delete_types_fields() {
		global $typesHelper;
		$typesHelper->Delete_Fields();
	}

	public  static function add_bidirectional_fields() {
		global $podsHelper;
		$podsHelper->Relational_Fields();
	}
	/** End Field Registration */

	public static function check_CFRequiredFields() {
		global $xml_uci_admin;
		$xml_uci_admin->Required_CF_Fields();
	}

	public static function inlineimage_upload(){
		require_once(XML_SM_UCI_PRO_DIR . "includes/class-uci-inlinezipupload.php");
	}

	public static function set_post_types() {
		global $xml_uci_admin;
		$parserObj = new WpXMLSmackCSVParser();
		$eventKey = isset($_POST['filekey']) ? sanitize_key($_POST['filekey']) : '';
		$uploadedname = isset($_POST['uploadedname']) ? sanitize_text_field($_POST['uploadedname']) : '';
		$file = XML_SM_UCI_IMPORT_DIR . '/' . $eventKey . '/' . $eventKey;
		$parserObj->parseCSV($file, 0, -1);
		$Headers = $parserObj->get_CSVheaders();
		$Headers = $Headers[0];
		$type = 'Posts';
		if(in_array('wp_page_template', $Headers) && in_array('menu_order', $Headers)){
			$type = 'Pages';
		} elseif(in_array('user_login', $Headers) || in_array('role', $Headers) || in_array('user_email', $Headers) ){
			$type = 'Users';
		} elseif(in_array('comment_author', $Headers) || in_array('comment_content', $Headers) ||  in_array('comment_approved', $Headers) ){
			$type = 'Comments';
		} elseif( in_array('reviewer_name', $Headers) || in_array('reviewer_email', $Headers)){
			$type = 'CustomerReviews';
		} elseif( in_array('event_start_date', $Headers) || in_array('event_end_date', $Headers)){
			$type = 'event';
		}
		elseif( in_array('ticket_start_date', $Headers) || in_array('ticket_end_date', $Headers) && !in_array('event_start_date' , $Headers)){
			$type = 'ticket';
		}
		elseif( in_array('location_name', $Headers) || in_array('location_address', $Headers)){
			$type = 'location';
		} elseif( in_array('hide_on_screen', $Headers) || in_array('position', $Headers) || in_array('layout', $Headers)){
			if(in_array('advanced-custom-fields/acf.php', $xml_uci_admin->get_active_plugins())) {
				$type = 'acf';
			} elseif( in_array('advanced-custom-fields-pro/acf.php', $xml_uci_admin->get_active_plugins())) {
				$type = 'acf-field-group';
			}
		} elseif( in_array('recurrence_freq', $Headers) || in_array('recurrence_interval', $Headers) || in_array('recuurence_days', $Headers)){
			$type = 'event-recurring';
		} elseif( in_array('name', $Headers) && in_array('slug', $Headers)){
			$type = 'category';
		} elseif(in_array('woocommerce/woocommerce.php', $xml_uci_admin->get_active_plugins())){
			if(in_array('PARENTSKU', $Headers) || in_array('VARIATIONSKU', $Headers) || in_array('PRODUCTID', $Headers) || in_array('VARIATIONID', $Headers)){
				$type = 'WooCommerceVariations';
			} elseif(in_array('coupon_code', $Headers) || in_array('COUPONID', $Headers) || in_array('coupon_amount', $Headers)){
				$type = 'WooCommerceCoupons';
			} elseif(in_array('ORDERID', $Headers) || in_array('payment_method', $Headers)){
				$type = 'WooCommerceOrders';
			} elseif(in_array('REFUNDID', $Headers)){
				$type = 'WooCommerceRefunds';
			} elseif(in_array('sku', $Headers)){
				$type = 'WooCommerce';
			}
		} elseif(in_array('wordpress-ecommerce/marketpress.php', $xml_uci_admin->get_active_plugins()) || in_array('marketpress/marketpress.php', $xml_uci_admin->get_active_plugins())){
			if(in_array('VARIATIONID', $Headers) || in_array('PRODUCTID', $Headers)){
				$type = 'MarketPressVariations';
			} elseif(in_array('sku', $Headers) || in_array('PRODUCTSKU', $Headers)){
				$type = 'MarketPress';
			}
		} elseif(in_array('wp-e-commerce/wp-shopping-cart.php', $xml_uci_admin->get_active_plugins())){
			if(in_array('coupon_code', $Headers) || in_array('COUPONID', $Headers)){
				$type = 'WPeCommerceCoupons';
			} elseif(in_array('sku', $Headers)){
				$type = 'WPeCommerce';
			}
		}
		$result = $template_order = array();
		$template_order = $xml_uci_admin->setPriority($uploadedname, $eventKey, null, $Headers);
		$result['is_template'] = 'no';
		if(!empty($template_order)){
			$result['is_template'] = 'yes';
		}
		$result['type'] = $type;
		print_r(json_encode($result));
		die();
	}

	public static function FetchBarStackedChartData() {
		global $wpdb, $xml_uci_admin;
		$available_types = array();
		foreach($xml_uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col("select distinct( import_type ) from smackuci_events_xml");
		$count = 1;
		foreach($get_list_of_imported_types as $import_type) {
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as created, sum(updated) as updated, sum(skipped) as skipped from smackuci_events_xml where import_type = %s", $import_type, $import_type ) );
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}

			if($get_chart_data[0]->created) {
				$returnArray[ $import_type_data ]['created'] = $get_chart_data[0]->created;
			} else {
				$returnArray[ $import_type_data ]['created'] = 0;
			}
			if($get_chart_data[0]->updated) {
				$returnArray[ $import_type_data ]['updated'] = $get_chart_data[0]->updated;
			} else {
				$returnArray[ $import_type_data ]['updated'] = 0;
			}
			if($get_chart_data[0]->skipped) {
				$returnArray[ $import_type_data ]['skipped'] = $get_chart_data[0]->skipped;
			} else {
				$returnArray[ $import_type_data ]['skipped'] = 0;
			}
			$count++;
		}
		echo json_encode($returnArray);
		die();
	}

	public static function FetchPieChartData() {
		global $wpdb, $xml_uci_admin;
		$available_types = array();
		foreach($xml_uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col("select distinct( import_type ) from smackuci_events_xml");
		$count = 1;
		foreach($get_list_of_imported_types as $import_type) {
			$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as %s from smackuci_events_xml where import_type = %s", $import_type, $import_type ) );
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}
			if($get_chart_data[0]->$import_type) {
				$data = $get_chart_data[0]->$import_type;
				$returnArray[ $count ][ $import_type_data ] = $data;
			} else {
				$returnArray[ $count ][ $import_type_data ] = 0;
			}
			$count++;
		}
		echo json_encode($returnArray);
		die();
	}

	public static function FetchLineChartData() {
		global $wpdb, $xml_uci_admin;
		$available_types = array();
		foreach($xml_uci_admin->get_import_post_types() as $name => $type) {
			$available_types[$name] = $type;
		}
		foreach (get_taxonomies() as $item => $taxonomy_name) {
			$available_types[$item] = $taxonomy_name;
		}
		$available_types = array_flip($available_types);
		$returnArray = array();
		$today = date("Y-m-d H:i:s");
		$j = 0;
		for($i = 11; $i >= 0; $i--) {
			$month[$j] = date("M", strtotime( $today." -$i months"));
			$year[$j]  = date("Y", strtotime( $today." -$i months"));
			$j++;
		}
		$get_list_of_imported_types = $wpdb->get_col("select distinct( import_type ) from smackuci_events_xml");
		foreach($get_list_of_imported_types as $import_type) {
			$data = '';
			for($i = 0; $i <= 11; $i++) {
				$count = 0;
				$get_chart_data = $wpdb->get_results( $wpdb->prepare( "select sum(created) as %s from smackuci_events_xml where import_type = %s and month = %s and year = %d", $import_type, $import_type, $month[$i], $year[$i] ) );
				if($get_chart_data[0]->$import_type) {
					$data .= $get_chart_data[0]->$import_type . ',';
				} else {
					$data .= $count . ',';
				}
			}
			if(array_key_exists($import_type,$available_types)){
				$import_type_data = $available_types[$import_type];
			} else {
				$import_type_data = $import_type;
			}

			$returnArray[ $import_type_data ] = substr($data, 0, -1);
		}
		echo json_encode($returnArray);
		die();
	}

	public static function options_savein_ajax(){
		$ucisettings = get_option('XML_SM_UCI_pro_settings');
		$option = sanitize_text_field($_REQUEST['option']);
		$value = sanitize_text_field($_REQUEST['value']);
		foreach ($ucisettings as $key => $val) {
			$settings[$key] = $val;
		}
		$settings[$option] = $value;
		update_option('XML_SM_UCI_pro_settings', $settings);
	}

	public static function database_optimization_settings(){
		$get_optimize = get_option('XML_SM_UCI_pro_optimization');
		if (is_array($get_optimize)) {
			foreach($get_optimize as $key => $value) {
				if(isset($key))
					$optimize_settings[$key] = $value;
			}
		}
		$optimize_settings[sanitize_text_field($_POST['option'])] = sanitize_text_field($_POST['value']);
		update_option('XML_SM_UCI_pro_optimization', $optimize_settings);
	}

	public static function database_optimization_process(){
		require_once(XML_SM_UCI_PRO_DIR . "includes/class-uci-dboptimizer.php");
	}

	public static function upload_zipfile_handler(){
		require_once(XML_SM_UCI_PRO_DIR . "includes/class-uci-zipfilehandler.php");
	}

	public static function sendmail(){
		if(isset($_POST) && !empty($_POST)){
			$email = sanitize_email($_POST['email']);
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers.= "Content-type: text/html; charset=iso-8859-1 \r\n";
			$to = 'support@smackcoders.com';
			$subject = sanitize_text_field($_POST['query']);
			$message = "Site URL: " . $url . "\r\n";
			$message .= "Plugin Name: " . XML_SM_UCI_SETTINGS . "\r\n";
			$message .= "Message: " . sanitize_text_field($_POST['message']) . "\r\n";
			//send email
			if(wp_mail($to, $subject, $message, $headers)) {
				$result_message = 'Mail Sent!';
				echo esc_html($result_message);
			} else {
				$result_message = "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
				echo esc_html($result_message);
			} //This method sends the mail.
			die;
		}
	}

	public static function send_subscribe_email(){
		if(isset($_POST) && !empty($_POST)){
			$email = sanitize_email($_POST['subscribe_email']);
			$url = get_option('siteurl');
			$site_name = get_option('blogname');
			$headers = "From: " . $site_name . "<$email>" . "\r\n";
			$headers.= 'MIME-Version: 1.0' . "\r\n";
			$headers.= "Content-type: text/html; charset=iso-8859-1 \r\n";
			$to = 'support@smackcoders.com';
			$subject = 'Newsletter Subscription';
			$message = "Site URL: " . $url . "\r\n";
			$message .= "Plugin Name: " . XML_SM_UCI_SETTINGS . "\r\n";
			$message .= "Message: Hi Team, I want to subscribe to your newsletter." . "\r\n";
			//send email
			if(wp_mail($to, $subject, $message, $headers)) {
				$result_message = 'Mail Sent!';
				echo esc_html($result_message);
			} else {
				$result_message = "Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!";
				echo esc_html($result_message);
			} //This method sends the mail.
			die;
		}
	}

	public function rollback_now(){
		global $xml_uci_admin;
		$eventKey = sanitize_text_field($_POST['eventkey']);
		$importtype = sanitize_text_field($_POST['importtype']);
		$tables = '';	
		$result = $xml_uci_admin->set_backup_restore($tables,$eventKey,'restore');	
		print_r(json_encode($result));
		die;
	}

	public function clear_rollback(){
		global $xml_uci_admin;
		$eventKey = sanitize_text_field($_POST['eventkey']);
		$importtype = sanitize_text_field($_POST['importtype']);
		$tables = '';
		$result = $xml_uci_admin->set_backup_restore($tables,$eventKey,'delete');
		print_r(json_encode($result));
		die;
	}

	public function retrieve_record() {
		$parserObj = new WpXMLSmackCSVParser();
		if(isset($_POST) && !empty($_POST)) {
			$file = XML_SM_UCI_IMPORT_DIR . '/' . sanitize_key($_POST['event_key']) . '/' . sanitize_key($_POST['event_key']);
			$csv_row = $parserObj->parseCSV($file, intval($_POST['row_no']));
			print_r(json_encode($csv_row[intval($_POST['row_no'])]));
		}
		die;
	}

	public static function preview_record() {
		$parserObj = new WpXMLSmackCSVParser();
		$modified_result = array();
		$result = '';
		if(isset($_POST) && !empty($_POST)) {
			$file = XML_SM_UCI_IMPORT_DIR . '/' . sanitize_key($_POST['event_key']) . '/' . sanitize_key($_POST['event_key']);
			if(intval($_POST['is_xml']) == 1){
				$mapping = array('title' => sanitize_text_field($_POST['title']), 'content' => sanitize_text_field($_POST['content']), 'excerpt' => sanitize_text_field($_POST['excerpt']), 'image' => sanitize_text_field($_POST['image']));
				$xmlparse = new WpXMLSmackNewXMLImporter();
				$doc = new DOMDocument();
				$doc->load($file);
				$tag = sanitize_text_field($_POST['xmltag']);
				foreach ($mapping as $key => $val) {
					if($val!=""){
						$val = str_replace('{', '', $val);
					$val = str_replace('}', '', $val);
					$val = str_replace('<p>', '', $val);
					$val = str_replace('</p>', '', $val);
					$val = preg_replace("(".$tag."[+[0-9]+])", $tag."[".intval($_POST['row_no'])."]", $val);
					$modified_result[$key] = $xmlparse->parse_element($doc,$val);
					}
				}	
			}
			else{
				$csv_row = $parserObj->parseCSV($file, intval($_POST['row_no']));
				$data = $csv_row[intval($_POST['row_no'])];
				$mapping = array('title' => sanitize_text_field($_POST['title']), 'content' => sanitize_text_field($_POST['content']), 'excerpt' => sanitize_text_field($_POST['excerpt']), 'image' => sanitize_text_field($_POST['image']));
				foreach($mapping as $key => $val) {
					$pattern = "/({([a-z A-Z 0-9 | , _ -]+)(.*?)(}))/";
					preg_match_all($pattern, $val, $results, PREG_PATTERN_ORDER);
					for($i=0; $i<count($results[2]); $i++) {
						$oldWord = $results[0][$i];
						$get_val = $results[2][$i];
						//TODO xml
						if(isset($data[$get_val])) {
							$newWord = $data[$get_val];
						} else {
							$newWord = $get_val;
						}
						$val = str_replace($oldWord, ' ' . $newWord, $val);
					}
					$modified_result[$key] = $val;
				}
			}
			if(!isset($modified_result['image']))
				$modified_result['image'] = '';
			if(!isset($modified_result['excerpt']))
				$modified_result['excerpt'] = '';
			if(!isset($modified_result['title']))
				$modified_result['title'] = '';
			if(!isset($modified_result['content']))
				$modified_result['content'] = '';
			
			$result .= '<table class="table table-striped">';
			$result .= '<tr>';
			#$result .= '<td><label>Post Title</label></td>';
			$result .= '<td><p><b>' . $modified_result['title'] . '</b></p></td>';
			$result .= '</tr>';
			$result .= '<tr>';
			#$result .= '<td><label>Post Content</label></td>';
			$result .= '<td><p>' . $modified_result['content'] . '</p></td>';
			$result .= '</tr>';
			$result .= '<tr>';
			$result .= '<tr>';
			#$result .= '<td><label>Featured Image</label></td>';
			$result .= '<td><p><img src="' . $modified_result['image'] . '" width="50" height="50" /></p></td>';
			$result .= '</tr>';
			#$result .= '<td><label>Short Description</label></td>';
			$result .= '<td><p>' . $modified_result['excerpt'] . '</p></td>';
			$result .= '</tr>';
			$result .= '</table>';
			print $result;
		}
		die;
	}
}
