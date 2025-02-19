<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
global $xml_uci_admin;
if(isset($_POST) && !empty($_POST)) {  	
	$records['import_config'] = sanitize_text_field($_POST['ignite_import']);
	$eventkey = sanitize_key($_REQUEST['eventkey']);
	$post_values = $xml_uci_admin->GetPostValues($eventkey);
	$result = array_merge($post_values[$eventkey], $records);
	$xml_uci_admin->SetPostValues($eventkey, $result);
}
$eventkey = sanitize_key($_REQUEST['eventkey']);
$get_screen_info =  $xml_uci_admin->GetPostValues($eventkey);
if(isset($get_screen_info[$eventkey]['import_config']['handle_duplicate']) && $get_screen_info[$eventkey]['import_config']['handle_duplicate'] == 'Update') {
	$process_of_event = 'Update';
} else {
	$process_of_event = 'Import';
}

$import_type = $get_screen_info[$eventkey]['import_file']['posttype'];
$file = XML_SM_UCI_IMPORT_DIR . '/' . $eventkey . '/' . $eventkey;
$parserObj->parseCSV($file, 0, -1);
//print_r($xml_uci_admin);die;
if(isset($get_screen_info[$eventkey]['import_file']['file_extension']) && $get_screen_info[$eventkey]['import_file']['file_extension'] == 'xml'){
	$total_row_count = $get_screen_info[$eventkey]['mapping_config']['total_no_of_records'];
}
else{
	$total_row_count = $parserObj->total_row_cont - 1;
}
$get_upload_url = wp_upload_dir();
$uploadLogURL = $get_upload_url['baseurl'] . '/smack_uci_uploads/imports/'. $eventkey . '/' . $eventkey;
$logfilename = $uploadLogURL.".log";

$gif = WP_PLUGIN_URL . '/wp-advanced-importer/assets/images/ajax-loader.gif';
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            
         </div>
<div class="template_body whole_body wp_ultimate_csv_importer_pro" style="font-size: 15px; margin-top: 40px;">
<form class="form-inline" method="post">
<div class="col-md-12">
<div class="col-md-12 mt40" style="text-align: center;">
	<input type="button" class="smack-btn smack-btn-primary btn-radius" value="<?php echo esc_attr('Resume','wp-advanced-importer');?>" style="display:none;" id="continue_import" onclick="continueImport();" >
	<input type="button" class="smack-btn smack-btn-danger btn-radius" value="<?php echo esc_attr('Pause','wp-advanced-importer');?>" id="terminate_now" onclick="terminateImport()">
	<input type="button" class="smack-btn smack-btn-danger btn-radius" value="<?php echo esc_attr('Verify import and Close','wp-ultimate-csv-importer');?>" id="new_import" onclick="reload_to_new_import()" style="display: none;">
	<input type="button" class="smack-btn smack-btn-danger btn-radius" value="<?php echo esc_attr('Rollback Now','wp-ultimate-csv-importer');?>" id="rollback_now" onclick="rollbacknow()" style="display: none;">
	<img id="img_ajax" src="<?php echo esc_url($gif); ?>" style="display: none;" >
</div>
</div>
	<div class="clearfix"></div>
	<div class="event-summary">
		<span class="es-left"> <?php echo esc_html__('File Name:','wp-advanced-importer');?> <?php echo esc_html($get_screen_info[$eventkey]['import_file']['uploaded_name']); ?> </span>
		<span class="es-right"> <?php echo esc_html__('File Size:','wp-advanced-importer');?> <?php echo esc_html($xml_uci_admin->getFileSize($file)); ?> </span>
	</div>
	<div class="event-summary">
		<span class="es-left"> <?php echo esc_html__('Process:','wp-advanced-importer');?> <?php echo esc_html($process_of_event); ?> </span>
		<span class="es-right"> <?php echo esc_html__('Total no of records:','wp-advanced-importer');?> <?php echo esc_html($total_row_count); ?> </span>
	</div>
	<div class="event-summary timer">
		<span class="es-left"> <?php echo esc_html__('Time Elapsed:','wp-advanced-importer');?> </span>
		<span class="es-left" style="padding-left: 10px;">
			<span class="hour">00</span>:<span class="minute">00</span>:<span class="second">00</span>
		</span>
		<span class="es-right" id="remaining" style="padding-right:2px;text-color:red;"> <?php echo esc_html__('Remaining Record:','wp-advanced-importer');?> </span>

		<span class="es-right" id="current" style = "padding-right:7px;text-color:green;"> <?php echo esc_html__('Current Processing Record:','wp-advanced-importer');?> </span>

	</div>
	<div class="control" style="display: none;">
		<input type="button" id="smack_uci_timer_start" onClick="timer.start(1000)" value="Start" />
		<input type="button" id="smack_uci_timer_stop" onClick="timer.stop()" value="Stop" />
		<input type="button" id="smack_uci_timer_reset" onClick="timer.reset(60)" value="Reset" />
		<input type="button" id="smack_uci_timer_count_up" onClick="timer.mode(1)" value="Count up"/>
		<input type="button" id="smack_uci_timer_count_down" onClick="timer.mode(0)" value="Count down" />
	</div>
	<div id="logsection" class="seoadv_options">
		<div class="seoadv_options_head"><?php echo esc_html__('Log section','wp-advanced-importer');?></div>
		<div id="innerlog" class="logcontainer">

		</div>
	</div>
	
	<input type="hidden" id="eventkey" value="<?php echo esc_attr($eventkey);?>">
	<input type="hidden" id="import_type" value="<?php echo esc_attr($import_type);?>">
	<input type="hidden" id="importlimit" name="importlimit" value = "1" >
	<input type="hidden" id="currentlimit" name="currentlimit" value = "1" >
	<input type="hidden" id="limit" name="limit" value = "1" >
	<input type="hidden" id="inserted" value="0" >
	<input type="hidden" id="updated" value="0" >
	<input type="hidden" id="skipped" value="0" >
	<input type="hidden" id="totalcount" name="totalcount" value = "<?php echo  esc_attr($total_row_count);?>">
	<input type="hidden" id="terminate_action" name="terminate_action" value="<?php echo esc_html__('continue','wp-advanced-importer');?>" />
	<input type="hidden" name="rollback_mode" id="rollback_mode" value="<?php echo esc_attr($rollback_mode); ?>">
	<input type="hidden" name="main_mode" id="main_mode" value="<?php echo esc_attr($main_mode); ?>">

</form>
<div class="col-md-12 mb30" id="dwnld_log_link" style="padding: 0px 40px 0px 40px;display: none;">
	<div class="pull-right"  >
                   <?php if(isset($logfilename))  { ?>
                <a href="<?php echo esc_url($logfilename); ?>" download id="dwnldlog" style="font-size:15px;"> <?php echo esc_html_e("CLICK HERE TO DOWNLOAD LOG","wp-advanced-importer"); ?></a>
                   <?php } ?>
        </div>
        <div class="pull-left" id="div_features" style="display: none;">
        	<label><button class="smack-btn smack-btn-danger" onclick="clear_rollback()"><?php echo esc_html__('Clear roll back','wp-advanced-importer');?></button></label>
        	<label><button class="smack-btn smack-btn-danger" onclick="rollback_now()"><?php echo esc_html__('Roll Back now','wp-advanced-importer');?></button></label>
        </div>

        </div>
<div class="clearfix"></div>
</div>
<script>
	jQuery(document).ready(function(e)
	{
		jQuery( "#smack_uci_timer_count_up" ).click();
		jQuery( "#smack_uci_timer_start").click();
	});
	igniteImport();
</script>

<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank">Smackcoders</a>.</div>
