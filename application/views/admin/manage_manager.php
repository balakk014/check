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
        <form method="get" class="form" name="managemanager" id="managemanager" action="managersearch">
<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
 <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="ser_input_field">
                                <input type="text" name="keyword"  maxlength="55" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('srch_info_passenger_keyword'); ?></span>
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
                                foreach ($get_allcompany as $comapany_list) {

                                    $selected_status = ($company_val == $comapany_list['cid']) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php echo $comapany_list['cid']; ?>"  <?php echo $selected_status; ?> ><?php echo ucfirst($comapany_list['company_name']); ?></option>
                                <?php }  ?>
                            </select>
                            </div>
                        </td>     
                        <?php } ?>   
                        </tr>
                        <tr>
                        <td valign="top"><label>&nbsp;</label></td>
                        <td>
                            <div class="new_button">
                                <input type="submit" value="<?php echo __('button_search'); ?>" name="search_user" title="<?php echo __('button_search'); ?>" />
                            </div>
                            <div class="new_button">
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/manager'" />
                            </div>
                        </td>
                    </tr>
                </table>
            <div class="over_all">
				<?php if(count($all_company_list) > 0) { ?>
                		<div class="widget" style="margin-bottom:0px !important;">
		<div class="title"><h6><?php echo $page_title; ?></h6>
			<div style="width:auto; float:right; margin: 0 ;"><?php  $export_table_count=count($all_list); include_once(APPPATH.'views/admin/export_menu.php');?> <?php //echo $export_excel_button; ?>   </div>                 
		</div>
		</div>
		<?php } ?>
<?php if($total_company > 0){ ?>
		
<div class= "overflow-block">
<?php } ?>		
<table cellspacing="1" cellpadding="10" width="100%" align="center" style="border:1px solid #cdcdcd;" class="sTable responsive">
<?php if($total_company > 0){ ?>
<thead>
	<tr>
		<td align="center" width="5%"><?php echo __('Select'); ?></td>
		<td align="center" width="5%" style="min-width: 22px !important;" ><?php echo 'Status'; ?></td>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo ucfirst(__('name')); ?></td>
		<?php if($usertype != 'C') { ?>
		<td align="left" style="text-align:left;" width="12%"><?php echo __('companyname'); ?></td>
		<?php } ?>
		<td align="left" style="text-align:left;" width="13%"><?php echo __('email_label'); ?></td>
		<td align="left" style="text-align:left;" width="14%"><?php echo __('address'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('country_label'); ?></td>
		<td align="left" style="text-align:left;" width="8%"><?php echo __('state_label'); ?></td>
		<td align="left" style="text-align:left;" width="8%"><?php echo __('city_label'); ?></td>
		<td align="center" width="7%"><?php echo __('action_label'); ?></td>
	</tr>
</thead>
<tbody>	
		<?php

         $sno=$Offset; /* For Serial No */

		 foreach($all_company_list as $listings) {
		 
		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

        <tr class="<?php echo $trcolor; ?>">
                    <td align="center"><input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['id'];?>" value="<?php echo $listings['id'];?>" />
                    </td>
                    <td align="center"> 
                         <?php 
                             if($listings['status']=='A')
                             {  $txt = "Active"; $class ="unsuspendicon";    }
				elseif($listings['status']=='T')
				{$txt = "Trash"; $class ="trashicon";}
                             else{  $txt = "Deactive"; $class ="blockicon";      }


                             echo '<a  title ='.$txt.' class='.$class.'></a>' ;  
                         ?>  
                    </td> 
			<td align="center"><?php echo $sno; ?></td>
			<td><a title="<?php echo ucfirst($listings['name']); ?>" href="<?php echo URL_BASE.'manage/managerdetails/'.$listings['id'];?>"><?php echo wordwrap(ucfirst($listings['name']),30,'<br/>',1); ?></a></td>
			<?php if($usertype != 'C') { ?>
			<td><a title="<?php echo ucfirst($listings['company_name']); ?>" href="<?php echo URL_BASE.'manage/companydetails/'.$listings['userid'];?>">
				<?php /* if(file_exists($_SERVER["DOCUMENT_ROOT"].'/public/uploads/company/'.$listings['userid'].'.png')){  ?> 
					<img width="32" height="32" src="<?php echo URL_BASE.COMPANY_IMG_IMGPATH.$listings['userid'].'.png';?>"/>
				<?php }else{ ?>
					<img width="32" height="32"  src="<?php echo URL_BASE;?>public/common/images/company_noimage.png"/>
				<?php } */ ?>			
			<?php echo wordwrap(ucfirst($listings['company_name']),25,'<br />',1); ?></a></td>
			<?php } ?>
			<td><?php echo wordwrap($listings['email'],25,'<br />',1); ?></td>
			<td><?php echo $listings['address']; ?></td>
			<td><?php echo $listings['country_name']; ?></td>
			<td><?php echo $listings['state_name']; ?></td>
			<td><?php echo $listings['city_name']; ?></td>

			<td align="center" width="20" colspan='3'><?php echo '<a href='.URL_BASE.'edit/manager/'.$listings['id'].' " title ="Edit" class="editicon"></a>' ; ?></td>


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
                 </div>
</form>
</div>
</div>
<div class="bottom_contenttot">


<!--** Multiple select starts Here ** -->
<?php if(count($all_company_list) > 0)
       { ?>
          <div class="select_all manage_fag">
			<ul>
			<li>
                <b><a href="javascript:selectToggle(true, 'managemanager');"><?php echo __('all_label');?></a></b></li>
				
				<li><span class="pr5 pl5">|</span></li><li><b><a href="javascript:selectToggle(false, 'managemanager');"><?php echo __('select_none');?></a></b></li>
				</ul>

                 <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('Change Status'); ?></option>
                        <option value="block_manager_request" ><?php echo __('Block'); ?></option>
                        <option value="active_manager_request" ><?php echo __('Active'); ?></option>
                        <option value="trash_manager_request" ><?php echo __('Trash'); ?></option>
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

					case "block_manager_request":
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.managemanager.action="<?php echo URL_BASE;?>manage/block_manager_request";
							 document.managemanager.submit();
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

					case "active_manager_request":
					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.managemanager.action="<?php echo URL_BASE;?>manage/active_manager_request";
								 document.managemanager.submit();
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

					case "trash_manager_request":
					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.managemanager.action="<?php echo URL_BASE;?>manage/trash_manager_request";
								 document.managemanager.submit();
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
