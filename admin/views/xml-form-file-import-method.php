<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

$ucisettings = get_option('XML_SM_UCI_pro_settings');
$main_mode = isset($ucisettings['enable_main_mode']) ? $ucisettings['enable_main_mode'] : '';
   ?>
<div class="whole_body wp_ultimate_csv_importer_pro">
   <form class="form-horizontal" id="form_import_file" method="post" action= "<?php echo esc_url(admin_url() . 'admin.php?page=smack-uci-import');?>" enctype="multipart/form-data">
  

      <div id='wp_warning' style = 'display:none;' class = 'error'></div>
      <div id='wp_notice' style = 'display:none;' class = 'notice notice-warning'><p></p></div>
      <input type='hidden' id="siteurl" value="<?php echo esc_url(site_url()); ?>" />
      <!-- Code Added For POP UP  Starts here -->
      <div class='modal fade' id = 'modal_zip' tabindex='-1' role='dialog' aria-labelledby='mymodallabel' aria-hidden='true'>
         <div class='modal-dialog'>
            <div class='modal-content'>
               <div class='modal-header'>
                  <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                  <h4 class='modal-title' id='mymodallabel'> <?php echo esc_html_e("Choose CSV/XML to import","wp-advanced-importer");?> </h4>
               </div>
               <div class='modal-body' id = 'choose_file'>
                  ...
               </div>
               <div class='modal-footer'>
                  <!--<button type='button' class='btn btn-default' data-dismiss='modal'>close</button>  -->
                  <button type='button' class='smack-btn smack-btn-primary btn-radius' data-dismiss='modal'><?php echo esc_html_e("Close","wp-advanced-importer");?></button>
               </div>
            </div>
         </div>
      </div>
      <!-- Code Added For POP UP Ends here -->
        <div class="">
         <div class="list-inline pull-right mb10">
            
         </div>
        </div>
	<div class="clearfix"></div>
      <div class="panel upload-view" style="width: 97%;margin-left:1%">
         <!-- <div class="panel-heading">
            <h1 class="text-center"><?php //echo esc_html__('Hello, Choose CSV/XML to import','wp-advanced-importer');?></h1>
            </div> -->
	      <div id="warningsec" style="color:red;width:100%; min-height: 110px;border: 1px solid #d1d1d1;background-color:#fff;display:none;">
		      <div id ="warning" class="display-warning" style="color:red;align:center;display:inline;font-weight:bold;font-size:15px; border: 1px solid red;margin:2% 2%;padding: 20px 0 20px;position: absolute;text-align: center;width:93%;display:none;"> </div>
	      </div>
         <div class="panel-body">
                         <div class="row">
               <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab-container" style="display:flex;">
                  <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 no-padding bhoechie-tab-menu">

                     <div class="list-group">
                        <a id="1" href="#" class="list-group-item active text-left" onclick="show_upload(this.id);">
                           <h4 class="glyphicon glyphicon-upload icon-cloud-upload"></h4><?php echo esc_html__('Upload from Desktop','wp-advanced-importer');?>
                           
                        </a>
                        <span title="upgrade to pro" disabled>
                        <a id="2" href="#" class="list-group-item text-left" >
                           <h4 class="glyphicon glyphicon-upload icon-upload"></h4>
                           <?php echo esc_html__('Upload from FTP/SFTP','wp-advanced-importer');?>
                           <span style="background-color: #ec3939 !important" class="new badge">Pro</span>
                        </a></span>
                        <span title="upgrade to pro" disabled>
                        <a id="3" href="#" class="list-group-item text-left" >
                           <h4 class="glyphicon glyphicon-upload icon-link2"></h4>
                           <?php echo esc_html__('Upload from URL','wp-advanced-importer');?>
                           <span style="background-color: #ec3939 !important" class="new badge">Pro</span>
                        </a></span>
                        <span title="upgrade to pro" disabled>
                        <a id="4" href="#" class="list-group-item text-left">
                           <h4 class="glyphicon glyphicon-upload icon-tree"></h4>
                           <?php echo esc_html__('Choose File in the Server','wp-advanced-importer');?>
                           <span style="background-color: #ec3939 !important" class="new badge">Pro</span>
                        </a></span>
                     </div>
                  </div>
                  <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 no-padding bhoechie-tab">
                  <!-- <div class="no-padding bhoechie-tab"> -->


                     <div id ='displaysection' class="col-md-12" style='display: none;'>
                     <!-- <div id ='displaysection' style='display: none;width: 775px; margin-left:4%;'> -->
                        <div id="displayname">
                           <div id="filenamedisplay"></div>
                        </div>
                        <div class="">
                           <!-- <progress id ='progressdiv' value="100" max="100"> </progress> -->
                           <div id="progress-div">
                              <div id="progress-bar">
                                 <span class="progresslabel">
                                 <?php #echo esc_html__('Upload Completed','wp-advanced-importer');?>
                                 </span>
                              </div>
                           </div>
                           <div id="targetLayer"></div>
                        </div>
                        <div class="clearfix"></div>
                        <!-- <div class="form-group mt10"> -->
                        <div class="form-group">
                           <label>
                           <input type="radio" name="import_mode" id="mode_insert" value="new_items" checked="checked"> <?php echo esc_html__('New Items','wp-advanced-importer');?>
                           </label>
                           <label class="pl20" title="Please upgrade to PRO for updating records">
                           <!-- <div class="col-xs-6 col-xs-offset-0 col-sm-3 col-sm-offset-0 col-md-2 col-md-offset-0"><label class="wp_img_size"><input style="display:none"id="mode_update" ></div> -->
                            <input type="radio" name="import_mode" id="mode_update" value="existing_items" disabled="disabled"><?php echo esc_html__(' Existing Items','wp-ultimate-csv-importer');?>
                           </label>
                        </div>
                        <div id="select_module" class="select_module col-md-8 col-md-offset-3" style="height: 80px;margin-top: 5%;">
                           <span>
                           <label class="import-textnew" style="width: 160px; height: 40px;"><?php echo esc_html__('Import each record as','wp-advanced-importer');?></label>
                           </span>
                           <span class="select_box" style="width:200px;height:40px;">
                              <!-- <select class="search_dropdown selectpicker" id="search_dropdowns" data-size="5" name ='posttype' style="width:200px;height:37px;"> -->
                              <select id="search_dropdowns" data-size="5" name ='posttype' style="width:200px;height:37px;">
                                 <?php
                                    global $xml_uci_admin;
                                    $all_post_types = $xml_uci_admin->get_import_post_types(); ?>
                                 <optgroup label="PostType">
                                    <?php foreach ($all_post_types as $key => $type) { ?>
                                    <option value="<?php print($type);?>"><?php print($key); ?></option>
                                    <?php }?>
                                 </optgroup>
                              </select>
                           </span>
                        </div>
                        <div class="col-md-1 col-md-offset-10 col-sm-1 col-sm-offset-8 mt20" style="float:right;margin-right: 8%;">
                           <input type ="submit" class="smack-btn smack-btn-primary btn-radius ripple-effect continue-btn" disabled value="<?php echo esc_attr__('Continue','wp-advanced-importer');?>">
                        </div>
                     </div>
                     <div class="bhoechie-tab-content active" id="division1">
                        <div class="file_upload" style="margin-top: 20%">
                           <input id="upload_file" type="file" name = "files[]" onchange ="upload_method()"/>
                           <div class="file-upload-icon">
                           <span id="fileupload" style="cursor: pointer;" class="import-icon"> <img src="<?php echo esc_url(WP_PLUGIN_URL.'/'.XML_SM_UCI_SLUG) ;?>/assets/images/upload-128.png" width="60" height="60" /> </span>
                           <span class="file-upload-text"><?php echo esc_html__('Click here to upload from desktop','wp-advanced-importer');?><p style='color:#fff;'>(Max filesize is: <?php echo ini_get('upload_max_filesize').'B'; ?>)</p></span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Row -->
            </div>
            <!-- Panel Body -->
         </div>
      </div>
      <script type="text/javascript">
         jQuery(document).ready(function() {
         jQuery("div.bhoechie-tab-menu>div.list-group>a").click(function(e) {
         	e.preventDefault();
         	jQuery(this).siblings('a.active').removeClass("active");
         	jQuery(this).addClass("active");
         	var index = jQuery(this).index();
         	jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
         	jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
         });
         });
      </script>
       <script type="text/javascript">
         jQuery(document).ready(function() {
              jQuery('#mode_update').click(function(e) {
                swal('Warning!', 'Please upgrade to PRO', 'warning')
              });
            jQuery("div.bhoechie-tab-menu>div.list-group>a").click(function(e) {
               e.preventDefault();
               jQuery(this).siblings('a.active').removeClass("active");
               jQuery(this).addClass("active");
               var index = jQuery(this).index();
               if(index == 0) {
                  jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
                  jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
               } else {
                  jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
                  //jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(5).addClass("active");
                  jQuery("div#division5").addClass("active");
               }
            });
         });
      </script>
      <input type='hidden' id='uploaded_name' name='uploaded_name' value =''>
      <input type='hidden' id='file_name' name='file_name' value =''>
      <input type="hidden" id="file_extension" name="file_extension" value="">
      <input type="hidden" id="import_method" name = "import_method" value="desktop">
      <input type='hidden' id='file_version' name='file_version' value=''>
      <input type='hidden' id='upload_max' name='upload_max' value='<?php echo ini_get('upload_max_filesize');?>'>
   </form>
</div>
<div style="font-size: 15px;text-align: center;margin-top: 2%;">Powered by <a href="https://www.smackcoders.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank">Smackcoders</a>.</div>
