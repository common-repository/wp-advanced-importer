<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>

<div class="list-inline pull-right mb10 wp_ultimate_csv_importer_pro">
    <div class="col-md-6"></div>
</div>
<div class="wp_ultimate_csv_importer_pro panel col-md-12" style="font-size:14px; height: 650px;width:99%; background-color:white;">
    <div class="col-md-10" style="text-align:center;width:99%;font-size:15px;margin-top:20px;line-height:5;"><?php echo esc_html__('Love WP Ultimate CSV Importer, Give a 5 star review on','wp-advanced-importer');?> <a style="font-size:15px;" target="_blank"  href ="https://wordpress.org/support/plugin/wp-ultimate-csv-importer/reviews/?rate=5#new-post"><?php echo esc_html__('wordpress.org!','wp-advanced-importer');?></a></div>
    <div class="col-md-12">
        <!-- <div class="col-md-6 col-sm-6 mt40 mb40" style=""> -->
        <div class="col-md-6 col-sm-6 mt20 mb40" style="margin-left:4%; width:45%;">
            <fieldset class="scheduler-border"> <legend class="scheduler-border" style="margin-top:-15px;"><?php echo esc_html__('Contact Support','wp-advanced-importer');?></legend>
                <form class="support-form" type="post" action="" style="margin-top:45px;line-height:3;">
                    <div class="form-group">
                        <label><?php echo esc_html__('Email','wp-advanced-importer');?></label>
                        <input name="email" id="email" class="form-control" value="" type="text">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Support type','wp-advanced-importer');?></label>
                        <!-- <select name="" id="query" class="selectpicker form-control" data-live-search="false"> -->
                        <select name="" id="query" class="form-control" data-live-search="false">
                            <option value="Bug Reporting"><?php echo esc_html__('Bug Reporting','wp-advanced-importer');?></option>
                            <option value="Feature Enhancement"><?php echo esc_html__('Feature Enhancement','wp-advanced-importer');?></option>
                        </select></div>
                    <div class="form-group">
                        <label for="comment"><?php echo esc_html__('Message','wp-advanced-importer');?></label>
                        <textarea class="form-control" style="height:200px;" rows="5" name="message" id="message"></textarea>
                    </div>
                    <div id="loading" style="opacity:0.7;background-color: #fff;z-index: 99;text-align: center;">
                        <img class="col-md-offset-10 col-sm-offset-9 col-xs-offset-4 mb10" id="loading-image" src="<?php echo esc_url(WP_PLUGIN_URL.'/'.XML_SM_UCI_SLUG) ;?>/assets/images/loading.gif" width="24" height="24" alt="Loading" style="display: none;margin-left: 110px;position: absolute;margin-top: 14px;" />
                    </div>

                    <div class="col-md-offset-10 col-sm-offset-9 col-xs-offset-4 mb10">
                        <input name="" id="" class="smack-btn smack-btn-primary btn-radius" value=<?php echo esc_attr__("Send","wp-advanced-importer");?> onclick="send_support_email();" type="button" style="margin-top:15px;float:right;">
                    </div>
                </form>
            </fieldset>
        </div>
        <!-- <div class="col-md-6 col-sm-6 mt40 mb40" style="margin-left:52%; width:47%;"> -->
        <div class="col-md-6 col-sm-6" style="margin-left:51%; width:45%; margin-top: -50%">
            <fieldset class="scheduler-border"> <legend class="scheduler-border" style="margin-top:-15px;"><?php echo esc_html__('News Letter Subscription','wp-advanced-importer');?></legend>
                <div class="form-group" style="line-height:3;">
                    <label><?php echo esc_html__('Email','wp-advanced-importer');?></label>
                    <input name="subscribe_email" id="subscribe_email" class="form-control" value="" type="text">
                </div>
                <div id="loading" style="opacity:0.7;background-color: #fff;z-index: 99;text-align: center;">
                    <img class="col-md-offset-10 col-sm-offset-9 col-xs-offset-4 mb10" id="loading-img-subs" src="<?php echo esc_url(WP_PLUGIN_URL.'/'.XML_SM_UCI_SLUG) ;?>/assets/images/loading.gif" width="24" height="24" alt="Loading" style="display: none;margin-left: 65px;position: absolute;margin-top: 9px;" />
                </div>

                <div class="col-md-12">
                    <div class="col-md-offset-9 col-sm-offset-7 col-xs-offset-2 mb10">
                        <input name="" id="" class="smack-btn smack-btn-primary btn-radius" value=<?php echo esc_attr__("Subscribe","wp-advanced-importer");?>  onclick="send_subscribe_email();" type="button" style="margin-top:2%; float:right;">
                    </div>
                </div>
            </fieldset>
        </div>
        <div style="font-size:15px; margin-left: 53%; width: 45%; margin-top: 130px;" class="col-md-6 col-sm-6 mb40">
            <div><b><?php echo esc_html__("Note","wp-advanced-importer");?></b></div>
            <div class="mt20"><i class="icon-news-paper" style="color:#178D7C;"></i>&nbsp; <?php echo esc_html__("Subscribe to Smackcoders Mailing list (a few messages a year)","wp-advanced-importer");?></div>
            <div class="mt20"><i class="icon-mail" style="color:#178D7C;"></i>&nbsp;<?php echo esc_html__("Please draft a mail to support@smackcoders.com. If you doesn't get any acknowledgement within an hour!","wp-advanced-importer");?></div>
        </div>
    </div>
    <div style="font-size: 15px;text-align: center;padding-top: 10%">Powered by <a href="https://www.smackcoders.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank">Smackcoders</a>.</div>
</div>
