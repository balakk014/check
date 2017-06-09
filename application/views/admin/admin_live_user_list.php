<?php defined('SYSPATH') OR die("No direct access allowed."); 


//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$status_val = isset($srch["status"]) ? $srch["status"] :''; 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 

//For CSS class deefine in the table if the data's available
//===========================================================
$total_users = count($all_user_list);

$table_css = $export_excel_button="";?>

<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
		<form method="get" class="form" name="frmusers" id="frmusers" action="live_users_search">
<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="new_input_field">
                                <input type="text" name="keyword"  maxlength="256" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('srch_info_usr_keyword'); ?></span>
                        </td>
                        <td valign="top">
                            <label><?php //echo __('usertype_label'); ?></label>
                        </td>
                        <td valign="top">
                            <!--<div class="formRight">
				<div class="selector" id="uniform-user_type">
                                <select class="select2" name="user_type" id="user_type" onchange="this.form.submit()">
                                <option value=""><?php //echo __('select_label'); ?></option>    
                                    <?php
                                   // $selected_user_type = "";
                                    //foreach ($filter as $user_type_key => $usertype_text) {
                                       // $selected_user_type = ($user_type_key == $user_type_val) ? " selected='selected' " : "";
                                        ?>
                                        <option value="<?php //echo $user_type_key; ?>"  <?php //echo $selected_user_type; ?>><?php //echo ucfirst($usertype_text); ?></option>
                                    <?php //}  ?>
                                </select>
                                </div>
                            </div> -->
                        </td>
                        <td valign="top"><!--<label><?php //echo __('status_label'); ?></label>--></td>
                        <td valign="top">
			<!--<div class="selector" id="uniform-user_type">
                            <select class="select2" name="status" id="status" onchange="this.form.submit()">
                            <option value=""><?php //echo __('select_label'); ?></option>    
                                <?php
                                //foreach ($status as $status_key => $allstatus) {

                                   // $selected_status = ($status_val == $status_key) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php //echo $status_key; ?>"  <?php //echo $selected_status; ?> ><?php //echo ucfirst($allstatus); ?></option>
                                <?php // }  ?>
                            </select>
                            </div>-->
                        </td>
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
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manageusers/live_users'" />
                            </div>
                        </td>
                    </tr>
                </table>
                  <div class="widget">
                        <div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
                            <?php /*<div style="width:auto; float:right; margin: 4px 3px;">
                                <div class="button greyishB"> <?php echo $export_excel_button; ?></div>                       
                                
                            </div> */ ?>
                        </div>
                        
<?php if($total_users > 0){ ?>


	<div class= "overflow-block">
<?php } ?>
	<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if ($total_users > 0) { ?>
		<thead>
		    <tr >
		<!--<td align="left" width="5%"></td>-->
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="15%"><?php echo ucfirst(__('name_label')); ?></td>
		<td align="left" width="15%"><?php echo ucfirst(__('last_login')); ?></td>
		<td align="left" width="10%"><?php echo __('phone_label'); ?></td>
		<td align="left" width="10%"><?php echo __('address_label'); ?></td>
		<!--<td align="left" width="20%"><?php //echo __('address'); ?></td>
		<td align="left" width="10%"><?php //echo __('created_date'); ?></td>
		<td align="left" width="10%"><?php //echo __('account_type'); ?></td>
		<td align="left" width="10%"><?php //echo __('status_label'); ?></td>    
		<td align="left" width="10%" ><?php //echo __('action_label'); ?></td>-->
		    </tr>
		</thead>
                  <tbody>               
                         
		<?php

         $sno=$Offset; /* For Serial No */

		 foreach($all_user_list as $listings) {
		 
		 //S.No Increment
		 //==============
		 $sno++;
        
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
		 
        ?>     

	<tr class="<?php echo $trcolor; ?>">

		<!--<td> 
		<input type="checkbox" name="uniqueId[]" id="trxn_chk<?php //echo $listings['id'];?>" value="<?php //echo $listings['id'];?>" />
		</td>-->


		<td><?php echo $sno; ?></td>
		<td><a href="<?php echo URL_BASE.'manage/userinfo/'.$listings['id'];?>"><?php echo wordwrap(ucfirst($listings['name']),30,'<br/>',1); ?></a></td>
		<td><?php echo $listings['last_login']; ?></td>
		<td><?php echo wordwrap($listings['phone'],25,'<br />',1); ?></td>
		<td><?php echo wordwrap($listings['address'],25,'<br />',1); ?></td>
		<!--<td><?php //echo wordwrap($listings['address'],25,'<br />',1); ?></td>
		<td><?php //echo wordwrap($listings['created_date'],25,'<br />',1); ?></td>
		<td><?php 
		//if($listings['user_type']=='D') { echo 'Taxi Driver'; }
		//elseif($listings['user_type']=='C') { echo 'Company Owner'; }
		//elseif($listings['user_type']=='M') { echo 'Company Manager'; }
		?></td>

		<td> 
		<?php 
		//if($listings['status']=='A')
		//{$txt = "Deactivate"; $class ="unsuspendicon";}
		//elseif($listings['status']=='T')
		//{$txt = "Trash"; $class ="trashicon";}
		//else{$txt = "Activate"; $class ="blockicon";}


		//echo '<a href="javascript:void(0);" title ='.$txt.' class='.$class.'></a>' ;  
		?>
		</td> 
		
		<td width="20" colspan='3' ><?php //echo '<a href='.URL_BASE.'admin/edituserprofile/'.$listings['id'].' " title ="Edit" class="editicon"></a>' ; ?></td>-->

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

                    <?php if ($total_users > 0) { ?>
                        </div>
                            <?php } ?>
</form>
</div>
</div>
<div class="clr">&nbsp;</div>
<div class="pagination">
		<?php if($total_users > 0): ?>
		 <p><?php echo $pag_data->render(); ?></p>  
		<?php endif; ?> 
  </div>
  <div class="clr">&nbsp;</div>

<!--** Multiple select starts Here ** -->
<?php //if(count($all_user_list) > 0)
       //{ ?>
          <!--<div class="select_all">
                <b><a href="javascript:selectToggle(true, 'frmusers');"><?php echo __('all_label');?></a></b><span class="pr2 pl2">|</span><b><a href="javascript:selectToggle(false, 'frmusers');"><?php echo __('select_none');?></a></b>

                <span style="padding-left:10px;">
                    <select name="more_action" id="more_action">
                        <option value=""><?php //echo __('Change Status'); ?></option>
                        <option value="block_users_request" ><?php //echo __('Block'); ?></option>
                        <option value="active_users_request" ><?php //echo __('Active'); ?></option>
                        <option value="trash_users_request" ><?php //echo __('Trash'); ?></option>
                    </select>
                 </span>
	        </div>-->
        <?php
        //} ?>
<!--** Multiple select ends Here ** -->



</div>
</div>
<script type="text/javascript" language="javascript">
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
$(document).ready(function(){
	toggle(1);$("input[type='text']:first", document.forms[0]).focus();
});
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

					case "block_users_request":
					var confirm_msg =  "<?php echo __('areyou_sure_wanttoblock_request');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.frmusers.action="<?php echo URL_BASE;?>manageusers/block_users_request/index";
							 document.frmusers.submit();
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

					case "active_users_request":
					var confirm_msg =  "<?php echo __('areyousure_wantto_activate_request');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/active_users_request/index";
								 document.frmusers.submit();
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

					case "trash_users_request":
					var confirm_msg =  "<?php echo __('are_yousure_wanttomove_request_to_trash');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/trash_users_request/index";
								 document.frmusers.submit();
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
