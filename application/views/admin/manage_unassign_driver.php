<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<style>
.alert-message {
    font-family: sans-serif;
    margin: 20px;
    font-weight: bold;
    
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    
    padding: 1px;
    position: relative;
    font-size: 12px;
    width:570px;
}

.alert-message .close {
    color: #745050;
    text-decoration: none;
    float: right;
    margin: 7px 7px 0 0;
    font-weight: bold;
    font-size: 16px;
}

.alert-message p {
    display: block;
    margin: 0;
    padding: 8px 20px 7px 10px;
    
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}

.error {
    background-color: #d29191;
    
    -webkit-box-shadow: 0px 0px 6px rgba(244,187,187,0.7);
    -moz-box-shadow: 0px 0px 6px rgba(244,187,187,0.7);
    box-shadow: 0px 0px 6px rgba(244,187,187,0.7);
}

.error p {
    background-color: #f4baba;
    
    background-image: -webkit-gradient(linear, left top, left bottom, from(#f4baba), to(#db7f7f));
    background-image: -webkit-linear-gradient(top, #f4baba, #db7f7f);
    background-image: -moz-linear-gradient(top, #f4baba, #db7f7f);
    background-image: -ms-linear-gradient(top, #f4baba, #db7f7f);
    background-image: -o-linear-gradient(top, #f4baba, #db7f7f);
    background-image: linear-gradient(top, #f4baba, #db7f7f);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorStr='#f4baba', EndColorStr='#db7f7f');
    
    -webkit-box-shadow: inset 0px 1px 0px #f7d0d0;
    -moz-box-shadow: inset 0px 1px 0px #f7d0d0;
    box-shadow: inset 0px 1px 0px #f7d0d0;
    
    color: #745050;
    text-shadow: 1px 1px 0px #eaadad;
    font-size:13px;
}
</style>
<?php

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

<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
		<?php
		if($availabilitycount < 0)
		{?>
		<div class="error alert-message">
		    <a  class="close">!!</a>
		    <?php if($_SESSION['user_type'] =='C')
		    { ?>
			<p><?php echo __('kindly_deactivate_any').substr($availabilitycount,1).__('driver_from_list'); ?> </p>
		    <?php }
		    else
		    {  
		    	if($total_company > 0) { ?>
		    	<p><?php echo __('kindly_deactivate_any').substr($availabilitycount,1).__('driver_from_list').__('or_contact_company_owner'); ?> </p>
		    	<?php }
		    	else { ?>
		    	<p><?php echo __('kindly_contact_company_owner'); ?> </p>
		    	<?php } ?>
		 <?php } ?>
		</div>
		<?php	
		}
		?>		 
        <form method="get" class="form" name="managedriver" id="managedriver" action="unassign_driversearch">
<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
 <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="new_input_field">
                                <input type="text" name="keyword"  maxlength="256" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('serch_by'); ?></span>
                        </td>
                        <td valign="top"><!--<label><?php //echo __('status_label'); ?></label>--></td>
                        <td valign="top">
			<!--<div class="selector" id="uniform-user_type">
                            <select class="select2" name="status" id="status" onchange="this.form.submit()">
                                <option value=""><?php echo __('select_label'); ?></option>    
                                <?php
                                //foreach ($status as $status_key => $allstatus) {

                                    //$selected_status = ($status_val == $status_key) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php //echo $status_key; ?>"  <?php //echo $selected_status; ?> ><?php //echo ucfirst($allstatus); ?></option>
                                <?php //}  ?>
                            </select>
                            </div>-->
                        </td>
                         <?php //if($_SESSION['user_type'] == 'A') { ?>
                        <td valign="top"><!--<label><?php echo __('company'); ?></label>--></td>
                        <td valign="top">
			<!--<div class="selector" id="uniform-user_type">
                            <select class="select2" name="filter_company" id="filter_company" onchange="this.form.submit()">
                                <option value=""><?php //echo __('select_label'); ?></option>    
                                <?php 
                                //foreach ($get_allcompany as $comapany_list) {

                                   // $selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php // echo $comapany_list['cid']; ?>"  <?php //echo $selected_status; ?> ><?php //echo ucfirst($comapany_list['company_name']); ?></option>
                                <?php //}  ?>
                            </select>
                            </div>-->
                        </td>     
                        <?php //} ?>  
                                                
                        </tr>
                        <tr>
                        <td valign="top"><label>&nbsp;</label></td>
                        <td>
                            <!--[if IE]>
                            <input type="text" style="display: none;" disabled="disabled" size="1" />
                            <![endif]-->
                            <div class="button brownB">
                                <input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
                            </div>
                            <div class="button blueB">
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/unassign_driver'" />
                            </div>
                        </td>
                    </tr>
                </table>

                		<div class="widget">
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin: 4px 3px;">
		<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       

		</div>
		</div>
<?php if($total_company > 0){ ?>
<div class= "overflow-block">
<?php } ?>		
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_company > 0){ ?>
<thead>
	<tr>
		<!--<td align="left" width="5%"><?php //echo __('Select'); ?></td>
		<td align="left" width="5%" style="min-width: 22px !important;" ><?php //echo __('availability_status_label'); ?></td> 
		<td align="left" width="5%" style="min-width: 22px !important;" ><?php //echo __('status_label'); ?></td>-->
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="15%"><?php echo ucfirst(__('name')); ?></td>
		<td align="left" width="10%"><?php echo __('companyname'); ?></td>
		<?php /*<td align="left" width="10%"><?php echo __('mark_unavailable'); ?></td> */ ?>
		<!--<td align="left" width="10%"><?php //echo __('email_label'); ?></td>
		<td align="left" width="15%"><?php //echo __('address'); ?></td>
		<td align="left" width="10%"><?php //echo __('phone_label'); ?></td>
		<td align="left" width="10%"><?php //echo __('driver_license_id'); ?></td>
		<td align="left" width="10%"><?php //echo __('companyname'); ?></td>
		<?php /* <td align="left" width="15%"><?php echo __('created_by'); ?></td> */ ?>
		<td align="left" width="10%"><?php //echo __('country_label'); ?></td>
		<td align="left" width="10%"><?php //echo __('state_label'); ?></td>
		<td align="left" width="10%"><?php //echo __('city_label'); ?></td>
		<td align="left" width="10%" ><?php //echo __('action_label'); ?></td>-->
	</tr>
</thead>
<tbody>	
		<?php
		/* For Serial No */
		$sno=$Offset; 
		
		 foreach($all_company_list as $listings) {

		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

        <tr class="<?php echo $trcolor; ?>">
                   <!-- <td><input type="checkbox" name="uniqueId[]" id="trxn_chk<?php //echo $listings['id'];?>" value="<?php //echo $listings['id'];?>" />
                    </td>
                    <td> 
                         <?php 
                            /* if($listings['availability_status']=='A')
                             {  $txt = "Active"; $class ="unsuspendicon";    }
                             else{  $txt = "Deactive"; $class ="blockicon";      }
                             echo '<a  title ='.$txt.' class='.$class.'></a>' ;  */
                         ?>  
                    </td>    
                                        
                    <td> 
                         <?php 
                            /* if($listings['status']=='A')
                             {  $txt = "Active"; $class ="unsuspendicon";    }
				elseif($listings['status']=='T')
				{$txt = "Trash"; $class ="trashicon";}
                             else{  $txt = "Deactive"; $class ="blockicon";      }


                             echo '<a  title ='.$txt.' class='.$class.'></a>' ;  */
                         ?>  
                    </td> -->
			<td><?php echo $sno; ?></td>
			<td><a href="<?php echo URL_BASE.'manage/driverinfo/'.$listings['id'];?>"><?php echo wordwrap($listings['name'],30,'<br/>',1); ?></a></td>
			<?php /*<td><a href="<?php echo URL_BASE.'manage/driver_unavailable/'.$listings['id'];?>"><?php echo __('mark_unavailable'); ?></a></td>*/ ?>
			<td><a href="<?php echo URL_BASE.'manage/companydetails/'.$listings['cid'];?>">
			<?php echo wordwrap($listings['company_name'],25,'<br />',1); ?></a></td>
			<!--<td><?php //echo wordwrap($listings['email'],25,'<br />',1); ?></td>
			<td><?php //echo $listings['address']; ?></td>
			<td><?php //echo $listings['phone']; ?></td>
			<td><?php //echo $listings['driver_license_id']; ?></td>
			
			<?php /* <td><?php echo $listings['created_by']; ?></td> */ ?>
			<td><?php //echo $listings['country_name']; ?></td>
			<td><?php //echo $listings['state_name']; ?></td>
			<td><?php //echo $listings['city_name']; ?></td>
			<td width="20" colspan='3' ><?php //echo '<a href='.URL_BASE.'edit/driver/'.$listings['id'].' " title ="Edit" class="editicon"></a>' ; ?></td>
-->

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
<?php } ?>
</form>
</div>
</div>
<div class="clr">&nbsp;</div>
<div class="pagination">
		<?php if($total_company > 0): ?>
		 <p><?php echo $pag_data->render(); ?></p>  
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

<!--** Multiple select starts Here ** -->
<?php //if(count($all_company_list) > 0)
      // { ?>
          <!--<div class="select_all">
                <b><a href="javascript:selectToggle(true, 'managedriver');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'managedriver');"><?php echo __('select_none');?></a></b>

                 <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('Change Status'); ?></option>
                        <option value="block_driver_request" ><?php echo __('Block'); ?></option>
                        <option value="active_driver_request" ><?php echo __('Active'); ?></option>
                        <option value="trash_driver_request" ><?php echo __('Trash'); ?></option>
                    </select>
                 </span>
	        </div>-->
        <?php
      //  } ?>
<!--** Multiple select ends Here ** -->


</div>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function(){
 $("#keyword").focus(); 
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

					case "block_driver_request":
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.managedriver.action="<?php echo URL_BASE;?>manage/block_driver_request/index";
							 document.managedriver.submit();
						 }else{
						 	$('#more_action').val('');
						 }
	
					}
					else{
					        //alert for no record select
					        //=============================
						    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
						    $('#more_action').val('');
					}					
					break;



					//	Current Action "approve"
					//=========================

					case "active_driver_request":
					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.managedriver.action="<?php echo URL_BASE;?>manage/active_driver_request/index";
								 document.managedriver.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
							    $('#more_action').val('');
						}						

					break;


                	//	Current Action "trash"
					//==========================

					case "trash_driver_request":
					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.managedriver.action="<?php echo URL_BASE;?>manage/trash_driver_request/index";
								 document.managedriver.submit();
							 }else{
							 	$('#more_action').val('');
							 }		
						}
						else{
						        //alert for no record select
						        //=============================
							    alert("<?php echo __('Please select atleast one or more Record(s) to do this action');?>")	
							    $('#more_action').val('');
						}						

					break;
				}		
			return false;  
	});
</script>
