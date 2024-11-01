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
<div class="wp_ultimate_csv_importer_pro">

<div class="box-one">
    <div class="top-right-box">
        <h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(XML_SM_UCI_PRO_URL . '/assets/images/chart_bar.png');?>" /></span><?php echo __('Importers Activity','wp-advanced-importer'); ?></h3>
        <div class="top-right-content">
            <div id='dispLabel'></div>
            <canvas id="uci-line-chart"></canvas>
            <!-- <div class='lineStats' id='lineStats' style='height: 250px;width:100%;margin-top:15px; margin-bottom:15px;'></div> -->
        </div>
    </div>
    <div class="top-right-box">
        <h3><span style="margin: -5px 5px 5px 5px;"><img src="<?php echo esc_url(XML_SM_UCI_PRO_URL . '/assets/images/stat_icon.png');?>"></span><?php echo __('Import Statistics','wp-advanced-importer'); ?></h3>
        <div class="top-left-content">
            <div id='dispLabel'></div>
            <!-- <div id="canvas-holder" style="width:50%; float: left;">
                <canvas id="uci-pie-chart"></canvas>
            </div> -->
            <div id="canvas-holder" style="width:100%;">
                <canvas id="uci-bar-stacked-chart"></canvas>
            </div>
            <!-- <div class='pieStats' id='pieStats' style='float:left;height:250px;width:100%;margin-top:15px;margin-bottom:15px;'></div> -->
        </div>
    </div>

    <div style="width:75%;">

    </div>
    
</div>
</div>

<div style="font-size: 15px;text-align: center;padding-top: 20px">Powered by <a href="https://www.smackcoders.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=free_xml_importer" target="blank">Smackcoders</a>.</div>
