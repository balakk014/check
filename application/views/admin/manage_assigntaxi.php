<?php defined('SYSPATH') OR die("No direct access allowed.");


//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :'';
$company_val = isset($srch["filter_company"]) ? $srch["filter_company"] :'';
$status_val = isset($srch["status"]) ? $srch["status"] :'';
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :'';

//For CSS class deefine in the table if the data's available
//===========================================================
$total_company=count($all_company_list);

$table_css=$export_excel_button="";
if($total_company>0)
{ 
	$table_css='class="table_border"'; 

	$export_excel_button='
        				<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" 
        				onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'&status='.$status_val.'&type='.$user_type_val.'\'" />
    				';
}?>

<script type="text/javascript">
	$(function(){
		$(".wmd-view-topscroll").scroll(function(){
			$(".wmd-view")
				.scrollLeft($(".wmd-view-topscroll").scrollLeft());
		});
		$(".wmd-view").scroll(function(){
			$(".wmd-view-topscroll")
				.scrollLeft($(".wmd-view").scrollLeft());
		});
	});
</script>




<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 
        <form method="get" class="form" name="manageassigntaxi" id="manageassigntaxi" action="assigntaxisearch">
<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
 <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="ser_input_field">
                                <input type="text" name="keyword"  maxlength="55" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('search_by_name'); ?></span>
                        </td>
                        <td valign="top"><label><?php echo __('status_label'); ?></label></td>
                        <td valign="top">
			<div class="selector ser_input_field" id="uniform-user_type">
                            <select class="select2" name="status" id="status" onchange="this.form.submit()">
                                <option value=""><?php echo __('select_label'); ?></option>    
                                <?php
                                foreach ($status as $status_key => $allstatus) {

                                    $selected_status = ($status_val == $status_key) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php echo $status_key; ?>"  <?php echo $selected_status; ?> ><?php echo ucfirst($allstatus); ?></option>
                                <?php }  ?>
                                <option value="U" <?php if($status_val == "U") { ?>selected<?php } ?>><?php echo __('unassigned'); ?></option>
                            </select>
                            </div>
                        </td>
                         <?php if($_SESSION['user_type'] == 'A') { ?>
                        <td valign="top"><label><?php echo __('company'); ?></label></td>
                        <td valign="top">
			<div class="selector ser_input_field" id="uniform-user_type">
                            <select class="select2" name="filter_company" id="filter_company" onchange="this.form.submit()">
                                <option value=""><?php echo __('select_label'); ?></option>    
                                <?php 
                                foreach ($get_allcompany as $company_list) {
									$companyName = (isset($company_list['company_brand_type']) && $company_list['company_brand_type'] == 'S') ? ucfirst($company_list["company_name"]).' - Admin' : ucfirst($company_list["company_name"]);
									
                                    $selected_status = ($company_val == $company_list['cid']) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php echo $company_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo $companyName; ?></option>
                                <?php }  ?>
                            </select>
                            </div>
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
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/assigntaxi'" />
                            </div>
                        </td>
                    </tr>
                </table>
            <div class="over_all">
				<?php if(count($all_list) > 0) { ?>
                		<div class="widget" style="margin-bottom:0px !important;">
		<div class="title"><h6><?php echo $page_title; ?></h6>
		
		<div style="width:auto; float:right; margin:0;">
		        <?php  $export_table_count=count($all_list); include_once(APPPATH.'views/admin/export_menu.php');?>
			<?php //echo $export_excel_button; ?>
		</div>               

		</div>
		</div>
		<?php } ?>
<?php if($total_company > 0){ ?>
		<div class="wmd-view-topscroll">
    <div class="scroll-div1">
    </div>
</div>
<div class="wmd-view">
	<div class="scroll-div2">
<div class= "overflow-block">
	
<?php } ?>		
<table cellspacing="1" style="border:1px solid #cdcdcd;" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_company > 0){ ?>
<thead>
	<tr>
		<td align="center" width="5%"><?php echo __('select'); ?></td>
		<td align="center" width="5%" style="min-width: 22px !important;" ><?php echo __('status_label'); ?></td>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" style="text-align:left;" width="8%"><?php echo __('driver_name'); ?></td>
		<td align="left" style="text-align:left;" width="8%"><?php echo __('taxi_no'); ?></td>
		<?php if($usertype != 'C' && $usertype != 'M') { ?>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('companyname'); ?></td>
		<?php } ?>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('assigned_by'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('country_label'); ?></td>
		<td align="left" style="text-align:left;" width="7%"><?php echo __('state_label'); ?></td>
		<td align="left" style="text-align:left;" width="8%"><?php echo __('city_label'); ?></td>
		<td align="left" style="text-align:left;" width="15%"><?php echo __('from_date'); ?></td>
		<td align="left" style="text-align:left;" width="15%"><?php echo __('end_date'); ?></td>
		<td align="left" width="10%"><?php echo __('action_label'); ?></td>
	</tr>
</thead>
<tbody>	
		<?php

         $sno=$Offset; /* For Serial No */

		 foreach($all_company_list as $listings) { ?>
		 
		 <input type="hidden" name="mapping_companyid" value="<?php echo $listings["mapping_companyid"]; ?>"/>
		 <?php 
		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

        <tr class="<?php echo $trcolor; ?>">
                    <td align="center">
						<?php if($listings['mapping_status'] == 'U') { ?>
							-
						<?php } else { ?>
							<input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['mapping_id'];?>" value="<?php echo $listings['mapping_id'];?>" />
						<?php } ?>
                    </td>
                    <td align="center"> 
						<?php 
							if($listings['mapping_status']=='A') {
								$txt = __('active'); $class ="unsuspendicon";
							} elseif($listings['mapping_status']=='T') {
								$txt = __('trash'); $class ="trashicon";
							} elseif($listings['mapping_status']=='U') {
								$txt = __('unassign'); $class ="unassignicon";
							} else {
								$txt = __('deactive'); $class ="blockicon";
							}
							echo '<a  title ='.$txt.' class='.$class.'></a>' ;  
						?>  
                    </td> 
                    <td align="center"><?php echo $sno; ?></td>
		    <td><a title="<?php echo ucfirst($listings['name']); ?>" href="<?php echo URL_BASE;?>manage/driverinfo/<?php echo $listings['id'];?>"><?php echo wordwrap(ucfirst($listings['name']),30,'<br/>',1); ?></a></td>
		    <td><a title="<?php echo $listings['taxi_no']; ?>" href="<?php echo URL_BASE;?>manage/taxiinfo/<?php echo $listings['taxi_id'];?>"><?php echo wordwrap($listings['taxi_no'],30,'<br/>',1); ?></a></td>
		    <?php if($usertype != 'C' && $usertype != 'M') { ?>
		    <td align="left"><a title="<?php echo ucfirst($listings['company_name']); ?>" title="<?php echo $listings['company_name']; ?>" href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $listings['cid'];?>">
		    <?php echo wordwrap(ucfirst($listings['company_name']),30,'<br/>',1); ?></a></td>
		    <?php } ?>
		    <td align="left"><?php echo wordwrap($listings['created_by'],30,'<br/>',1); ?></td>
		    <td><?php echo wordwrap($listings['country_name'],30,'<br/>',1); ?></td>
		    <td align="left"><?php echo wordwrap($listings['state_name'],30,'<br/>',1); ?></td>
		    <td align="left"><?php echo wordwrap($listings['city_name'],30,'<br/>',1); ?></td>
		    <td align="left"><?php echo Commonfunction::getDateTimeFormat($listings['mapping_startdate'],1); ?></td>
		    <td align="left"><?php echo Commonfunction::getDateTimeFormat($listings['mapping_enddate'],1); ?></td>
		    <td width="20" align="center" colspan='3'><?php echo '<a href='.URL_BASE.'edit/assigntaxi/'.$listings['mapping_id'].' " title ="Edit" class="editicon"></a>' ; ?></td>
		   
		    <input type="hidden" name="driverId[<?php echo $listings['mapping_id']; ?>]" value="<?php echo $listings['id']; ?>">
		</tr>
		<?php } 
 		 } 
		 
		//For No Records
		//==============
	     else{ ?>
       	<tr>
        	<td class="nodata"><?php echo __('no_data'); ?></td>
        </tr>
		<?php } ?>
		</tbody>
</table>
<?php if ($total_company > 0) { ?>
</div>
</div>
</div>
<?php } ?>
            </div>
</form>
</div>
</div>

<div class="bottom_contenttot">



<!--** Multiple select starts Here ** -->
<?php if(count($all_company_list) > 0)
       { ?>
          <div class="select_all manage_fag">
			<ul><li>
                <b><a href="javascript:selectToggle(true, 'manageassigntaxi');"><?php echo __('all_label');?></a></b></li>
				
				<li><span class="pr5 pl5">|</span></li><li><b><a href="javascript:selectToggle(false, 'manageassigntaxi');"><?php echo __('select_none');?></a></b></li>
				</ul>

                <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('change_status'); ?></option>
                         <option value="active_assigntaxi_request" ><?php echo __('active'); ?></option>
                        <?php if(COMPANY_CID!=1 || SUBDOMAIN!='demo') { ?> 
						<option value="block_assigntaxi_request" ><?php echo __('block'); ?></option>
						<option value="unassign_taxi" ><?php echo __('unassigned'); ?></option>
                        <option value="trash_assigntaxi_request" ><?php echo __('trash'); ?></option>
                        <?php } ?>
                    </select>
                 </span>
	        </div>
        <?php
        } ?>
<!--** Multiple select ends Here ** -->
<div class="pagination">
		<?php if($total_company > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
</div>
</div>
<div id="fade"></div>
<script type="text/javascript" language="javascript">
$(document).ready(function(){
 $("#keyword").focus(); 
	<?php if(isset($_SESSION['driverNames']) && $_SESSION['driverNames'] != "") { ?>
		alert('<?php echo __("these_driversarein_trip"); ?> : <?php echo $_SESSION["driverNames"]; ?>');
	<?php unset($_SESSION['driverNames']); } ?>
});
//For Delete the users
//=====================
function frmdel_user(userid)
{
	var answer = confirm("<?php echo __('delete_alert2');?>");
	if (answer){
        window.location="<?php echo URL_BASE;?>admin/delete/"+userid;
    }
    return false;
}  
function frmblk_user(userid,status)
{   
    window.location="<?php echo URL_BASE;?>admin/blkunblk/"+userid+"/"+status;
    return false;
}  

</script>



<script type="text/javascript">

	function selectToggle(toggle, form) {
		var myForm = document.forms[form];
		for( var i=0; i < myForm.length; i++ ) { 
		    if(toggle) {
		        myForm.elements[i].checked = "checked";
		    } 
		    else
		    { myForm.elements[i].checked = ""; }
		}
	}

	
	//for More action Drop Down
	//=========================
	$('#more_action').change(function() {

		//select drop down option value
		//======================================
		var selected_val= $('#more_action').val();
		
			//perform more action reject withdraw
			//===================================		
			switch (selected_val){


					//	Current Action "reject"//block 
					//===================================

					case "block_assigntaxi_request":
					var confirm_msg =  "<?php echo __('areyou_sure_wanttoblock_request');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.manageassigntaxi.action="<?php echo URL_BASE;?>manage/block_assigntaxi_request";
							 document.manageassigntaxi.submit();
						 }else{
						 	$('#more_action').val('');
						 }
	
					}
					else{
					        //alert for no record select
					        //=============================
						    alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
						    $('#more_action').val('');
					}					
					break;



					//	Current Action "approve"
					//=========================

					case "active_assigntaxi_request":
					var confirm_msg =  "<?php echo __('areyousure_wantto_activate_request');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.manageassigntaxi.action="<?php echo URL_BASE;?>manage/active_assigntaxi_request";
								 document.manageassigntaxi.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
							    $('#more_action').val('');
						}						

					break;
					
					case "unassign_taxi":
					var confirm_msg =  "<?php echo __('areyousure_wantto_taxirequesttounassign');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.manageassigntaxi.action="<?php echo URL_BASE;?>manage/unassign_taxi";
								 document.manageassigntaxi.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
							    $('#more_action').val('');
						}						

					break;


                	//	Current Action "trash"
					//==========================

					case "trash_assigntaxi_request":
					var confirm_msg =  "<?php echo __('are_yousure_wanttomove_request_to_trash');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.manageassigntaxi.action="<?php echo URL_BASE;?>manage/trash_assigntaxi_request";
								 document.manageassigntaxi.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('please_select_atleast_oneormore_record_todo_thisaction');?>")	
							    $('#more_action').val('');
						}						

					break;
				}		
			return false;  
	});
</script>
