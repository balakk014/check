<?php defined('SYSPATH') OR die("No direct access allowed.");

$status_val = isset($srch["status"]) ? $srch["status"] :''; 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 
//print_r($all_rating_drivers);
$total_drivers = count($all_rating_drivers);
$total_driverslist = count($RatingdriversList);

$table_css=$export_excel_button="";
if($total_drivers >0)
{ 
	$table_css='class="table_border"'; 

	$export_excel_button='<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'\'" />';
}?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
<form method="get" class="form" name="managerating_form" id="managerating_form" action="ratingdriver_search">
<table class="list_table1" border="0" width="80%" cellpadding="5" cellspacing="0">
                    
 <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="ser_input_field">
                                <input type="text" name="keyword"  maxlength="55" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('search_by_driver_name'); ?></span>
                        </td>
				<?php if($usertype == 'A') { ?>
						<td valign="top"><label><?php echo __('company_name'); ?></label></td>
						<td >
                           <div class=" ser_input_field sselector" id="uniform-user_type">
								<select class="select2" name="filter_company" id="filter_company" >
									
										<option value=""><?php echo __('all_label'); ?></option>
									<?php 
										foreach ($get_rate_company as $comapany_list) {
										$companyName = (isset($comapany_list['company_brand_type']) && $comapany_list['company_brand_type'] == 'S') ? ucfirst($comapany_list["company_name"]).' - Admin' : ucfirst($comapany_list["company_name"]);

									?>  
										<option value="<?php echo $comapany_list['cid']; ?>" <?php if(isset($srch['filter_company'])){if($srch['filter_company']==$comapany_list['cid']){echo "selected";} } ?>><?php echo $companyName; ?></option>
										<?php }  ?>
								</select>
							</div>
							<div id="filter_company_error" class="error"></div>
                            <span class="search_info_label"><?php echo __('search_by_company_name'); ?></span>
						</td>
                <?php } ?>
                        </tr>
                        <tr>
                        <td valign="top"><label>&nbsp;</label></td>
                        <td>
                            <!--[if IE]>
                            <input type="text" style="display: none;" disabled="disabled" size="1" />
                            <![endif]-->
                            <div class="new_button">
                                <input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
                            </div>
                            <div class="new_button">
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/ratingdrivers'" />
                            </div>
                        </td>
                    </tr>
                </table>
          <div class="over_all">
       		<div class="widget">
		<div class="title"><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right;">
		<?php /* <div class="button greyishB"> <?php echo $export_excel_button; ?></div> */  ?>

		</div>
		</div>
<?php if($total_drivers > 0){ ?>
<div class= "overflow-block">
<?php } ?>
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_drivers > 0){ ?>
<thead>
	<tr>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="20%"><?php echo __('driver_name'); ?></td>
		<td align="left" width="10%"><?php echo __('rating_points'); ?></td>
		<td align="center" width="10%"><?php echo __('action_label'); ?></td>
	</tr>
</thead>
<tbody>		
		<?php

         $sno=$Offset; /* For Serial No */
		 //print_r($all_rating_drivers);
		 foreach($all_rating_drivers as $listings) {
		 //S.No Increment
		 $sno++;
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 $rate = $listings['total_posts']/$listings['co_nt'];
		 //$rate = 1;
		 //print_r($listings);
        ?>     

        <tr class="<?php echo $trcolor; ?>">
            
			<td align="center"><?php echo $sno; ?></td>
			<td align="left"><?php echo wordwrap(ucfirst($listings['name'])); ?></td>
			<td align="left"><?php echo wordwrap(number_format($rate,1)." Out of 5"); ?></td>
			<td align="center"><a href="<?php echo URL_BASE.'manage/managerating_driversview/'.$listings['driver_id'];?>" class="viewicon" title="View details"></a></td>
		</tr>
		<?php } 
 		 } 
		 
		//For No Records
	     else{ ?>
       	<tr>
        	<td class="nodata"><?php echo __('no_data'); ?></td>
        </tr>
		<?php } ?>
	</tbody>
</table>
<?php if ($total_drivers > 0) { ?>
</div>
<?php } ?>
<input type="hidden" name="change_value" id="change_value" value="">
</form>
</div>
</div>
</div>
</div>
<div class="bottom_contenttot">
<div class="pagination">
		<?php if($total_drivers > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
 </div>

</div>

<script type="text/javascript">
 $(document).ready(function(){
  $("#keyword").focus(); 
});
</script>
