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
global $scheduleObj;
if(isset($_POST) && !empty($_POST)) {

	$fields = $_POST;
	foreach($fields as $fieldKey => $fieldVal ){
		if( is_array($field)){
			$value[$fieldKey] = array_map( 'sanitize_text_field', $fieldVal );
		}
		else{
			$value[$fieldKey] = sanitize_text_field($fieldVal);
		}
	}

	$records['mapping_config'] = $value;
	$eventkey = sanitize_key($_REQUEST['eventkey']);
	$get_records = $xml_uci_admin->GetPostValues($eventkey);
	$result = array_merge($get_records[$eventkey], $records);
	$xml_uci_admin->SetPostValues($eventkey, $result);
}
$eventkey = sanitize_key($_REQUEST['eventkey']);
$post_values = $xml_uci_admin->GetPostValues($eventkey);

$import_mode = '';
if($post_values[$eventkey]['import_file']['import_mode'] == 'existing_items') {
	$import_mode = "checked = 'checked'";
}
$import_type = $post_values[$eventkey]['import_file']['posttype'];
$importAs = '';
$server_request = $xml_uci_admin->serverReq_data();
$file = XML_SM_UCI_IMPORT_DIR . '/' . $eventkey . '/' . $eventkey;
$parserObj->parseCSV($file, 0, -1);
$total_row_count = $parserObj->total_row_cont - 1;
$actionURL = esc_url(admin_url() . 'admin.php?page=smack-uci-import&step=confirm&eventkey='.$eventkey);
$backlink = esc_url(admin_url() . 'admin.php?page=smack-uci-import&step=mapping_config&eventkey='.$eventkey);
if($import_mode != '') {
	$duplicate_text = 'Do you update on existing records ?';
	$duplicate_subtext = 'Update records based on';
}else{

	$duplicate_text = 'Do you want to handle the duplicate on existing records ?';
	$duplicate_subtext = 'Mention the fields which you want to handle duplicates';
}
if(isset($_REQUEST['templateid'])) {
         $actionURL .= '&templateid=' . intval($_REQUEST['templateid']);
	 $backlink .= '&templateid=' . intval($_REQUEST['templateid']);
}
$backlink .= '&mapping_type=advanced';
$ucisettings = get_option('XML_SM_UCI_pro_settings');

if( isset($post_values[$eventkey]['import_file']['file_extension']) && $post_values[$eventkey]['import_file']['file_extension'] == 'xml'){
	$tag = $post_values[$eventkey]['mapping_config']['xml_tag_name'];
	$backlink .= '&tag_name=' .$tag;
}
if (isset($post_values[$eventkey]['mapping_config']['tree_type'])) {
	$tree_type = $post_values[$eventkey]['mapping_config']['tree_type'];
	$backlink .= '&tree_type=' .$tree_type;
}
?>
<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
            
         </div>
<div class="template_body whole_body wp_ultimate_csv_importer_pro" style="font-size: 14px; margin-top: 40px;">
	<h3 style="margin-left:2%;" class="csv-importer-heading"><?php echo esc_html__('Import configuration Section','wp-advanced-importer');?></h3>
	<form class="form-inline" method="post" action="<?php echo $actionURL;?>">
		<div id='wp_warning' style = 'display:none;' class = 'error'></div>
		<!-- table --><div class="config_table">
			<?php
			$duplicate_option = array('Users','CustomerReviews','Tags','Categories','Comments','WooCommerceVariations','WooCommerceOrders','MarketPressVariations','WPeCommerceCoupons','WooCommerceRefunds','WooCommerceCoupons');
			if(!in_array($import_type,$duplicate_option)) { ?>
			<div class="col-md-12 mt20">
				<div class="col-md-12 mb15">
					<label style="display:inline;">
						<input type = "checkbox" name="duplicate" id="duplicate" class="import_config_checkbox" onclick = "toggle_configdetails(this.id);" /><?php echo esc_html__($duplicate_text,'wp-advanced-importer');?></label></div>
			</div>
			<?php } ?>
			<div id="duplicate_headers" class="mb40" style="display:none;">
				<div class="col-md-12 mb15">
					<div class = "col-md-6 col-md-offset-1 col-sm-7 col-sm-offset-1">
					    <label>
						    <?php echo esc_html__($duplicate_subtext,'wp-advanced-importer');?>
					    </label></div>
					    <div class="col-xs-offset-3 col-xs-0">
					     <!-- <select class="dropdown-search-multiple selectpicker" name="duplicate_conditions[]" id="duplicate_conditions" disabled> -->
						 <select name="duplicate_conditions[]" id="duplicate_conditions" disabled>
					     <?php
					     //$fields = $xml_uci_admin->get_widget_fields('Core Fields', $post_values[$eventkey]['import_file']['posttype'],$importAs);
					     $fields = $xml_uci_admin->get_update_fields($post_values[$eventkey]['import_file']['posttype'],$post_values[$eventkey]['import_file']['import_mode']);
					     foreach( $fields as $wp_fieldLabel => $wp_fieldarray){ ?>
						     <option value="<?php echo esc_html($wp_fieldarray);?>">
							     <?php echo esc_html($wp_fieldarray);?>
						     </option>
					     <?php } ?>
					     </select></div>
				</div>
			</div>
			<!-- Schedule Configuration -->
			<div class="col-md-12 mt20">
				<div class="col-md-12 mb15">
					<label><input disabled type = "checkbox" class="import_config_checkbox" name = "schedule" id = "schedule" ><?php echo esc_html__('Do you want to Schedule this Import ?');?></label> <a href="https://www.smackcoders.com/wp-ultimate-csv-importer-pro.html?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank" style="color: red">Upgrade To Pro</a></div>
			</div>
			<div id="schedule_import"  style = "display:none;">
				<div class="col-md-12 ">
					
				</div>
			</div>
		</div>
		<input type="hidden" id="eventkey" value="<?php echo esc_attr($eventkey);?>">
		<input type="hidden" id="import_type" value="<?php echo esc_attr($import_type);?>">
	<div class="clearfix"></div>	<div class="col-md-12 mt40 mb20" style="display:flex;">	

		<div class="pull-left">
		<a class="smack-btn btn-default btn-radius" style="margin-left: 15%;" href="<?php echo esc_url($backlink);?>"><?php echo esc_html__('Back','wp-advanced-importer');?>
                        </a></div>
 	 	
 	 	<div class="pull-right" style="margin-top: -10px; margin-left: 80%;">
 	 	<input type="submit" class="smack-btn smack-btn-primary btn-radius" id="ignite_import" name="ignite_import" value="<?php echo esc_attr__('Import','wp-advanced-importer');?>" onsubmit="schedule_rightnow();">
		<input style="display:none" type="button" class="smack-btn smack-btn-primary btn-radius" id="schedule_import_btn" name="schedule_import" value="<?php echo esc_attr__('Schedule','wp-advanced-importer');?>" onclick="igniteSchedule();"></div>
</div><div class="clearfix"></div>


	</form>
</div>

<?php if($import_mode != '') { ?>
	<script type="application/javascript">
		jQuery('#duplicate').click();
	</script>
<?php } ?>
<script>
jQuery(function(){

	jQuery('#datetoschedule').datepicker({
		format: 'yyyy-mm-dd',
	});
	jQuery('#schedule')
	    .on('ifChecked', function(event) {
		jQuery('#main_ch').hide();   
		jQuery('#rollback_ch').hide();
	})
	.on('ifUnchecked', function() {
		jQuery('#main_ch').show();
		jQuery('#rollback_ch').show();
        });
});
</script>

<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank">Smackcoders</a>.</div>