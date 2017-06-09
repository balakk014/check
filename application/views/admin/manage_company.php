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
		<form method="get" class="form" name="frmcompany" id="frmcompany" action="companysearch">
		<table class="list_table1" border="0" width="65%" cellpadding="5" cellspacing="0">
 <tr>
                        <td valign="top"><label><?php echo __('keyword_label'); ?></label></td>
                        <td >
                            <div class="ser_input_field">
                                <input type="text" name="keyword"  maxlength="75" id="keyword" value="<?php echo isset($srch['keyword']) ? trim($srch['keyword']) : ''; ?>" />
                            </div>
                            <span class="search_info_label"><?php echo __('search_by_taxicompanyname'); ?></span>
                        </td>
                        <td valign="top"><label><?php echo __('status_label'); ?></label></td>
                        <td valign="top">
			<div class="selector ser_input_field" id="uniform-user_type">
                            <select class="select2" name="status" id="status" onchange="this.form.submit()">
                                <option value=""><?php echo __('status_label'); ?></option>    
                                <?php
                                foreach ($status as $status_key => $allstatus) {

                                    $selected_status = ($status_val == $status_key) ? ' selected="selected" ' : " ";
                                    ?>  
                                    <option value="<?php echo $status_key; ?>"  <?php echo $selected_status; ?> ><?php echo ucfirst($allstatus); ?></option>
                                <?php }  ?>
                            </select>
                            </div>
                        </td>
                        <?php /*if($_SESSION['user_type'] == 'A') { ?>
                        <td valign="top"><label><?php echo __('company'); ?></label></td>
                        <td valign="top">
			<div class="selector" id="uniform-user_type">
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
                        <?php } */ ?>                   
                        <tr/>
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
                                <input type="button" value="<?php echo __('button_cancel'); ?>" title="<?php echo __('button_cancel'); ?>" onclick="location.href = '<?php echo URL_BASE; ?>manage/company'" />
                            </div>
                        </td>
                    </tr>
                </table>
                     <div class="over_all">
			<?php if($all_list > 0) { ?>
			<div class="widget" style="margin-bottom:0px !important;">
				<div class="title"><h6><?php echo $page_title; ?></h6>
					<div style="width:auto; float:right;">
					<?php $export_table_count= $all_list; include_once(APPPATH.'views/admin/export_menu.php');?></div>
				</div>
			</div>
			<?php } ?>
<?php if($total_company > 0){ ?>
		

<div class= "overflow-block">
<?php } ?>
<table cellspacing="1" cellpadding="10" width="100%" align="center" style="border-top:1px solid #cdcdcd;" class="sTable responsive">
<?php if($total_company > 0){ ?>
<thead>
	<tr>
		<td align="left" width="5%"><?php echo __('Select'); ?></td>
		<td align="left" width="5%" style="min-width: 22px !important;" ><?php echo 'Status'; ?></td>
		<td align="left" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo ucfirst(__('name')); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('companyname'); ?></td>
		<?php /*<td align="left" style="text-align:left;" width="15%"><?php echo __('companyaddress'); ?></td> */ ?>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('email_label'); ?></td>
		<td align="left" style="text-align:left;" width="13%"><?php echo __('no_of_taxi'); ?></td>
		<td align="left" style="text-align:left;" width="10%"><?php echo __('no_of_driver'); ?></td>
		<td align="left" style="text-align:left;" width="13%"><?php echo __('no_of_manager'); ?></td>
		<?php /* <td align="left" width="10%"><?php echo __('package'); ?></td> */ ?>
		<td align="center" width="15%" ><?php echo __('action_label'); ?></td>
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
         $trcolor=($sno%2==0) ? 'eventr' : 'oddtr';  
		 
        ?>     

        <tr class="<?php echo $trcolor; ?>">

                   <td align="center">  
                        <input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['id'];?>" value="<?php echo $listings['id'];?>" />
                    </td>

                    <td align="center"> 
                         <?php 
                             if($listings['company_status']=='A')
                             {  $txt = "Active"; $class ="unsuspendicon";    }
				elseif($listings['company_status']=='T')
				{$txt = "Trash"; $class ="trashicon";}
                             else{  $txt = "Deactive"; $class ="blockicon";      }


                             echo '<a  title ='.$txt.' class='.$class.'></a>' ;  
                         ?>  

                    </td> 
		<?php	if($listings['no_of_package'] >0) { $change_title = __('upgrade_package'); } else { $change_title = __('add_package'); } ?>

		<td align="center"><?php echo $sno; ?></td>
                <td align="left"><?php /*<a href="<?php echo URL_BASE;?>manage/companyinfo/<?php echo $listings['id']; ?>" title="" > */ ?><?php echo wordwrap(ucfirst($listings['name']),30,'<br/>',1); ?><?php /*</a> */ ?></td>
 <td align="left"><a title="<?php echo $listings['company_name']; ?>" href="<?php echo URL_BASE;?>manage/companydetails/<?php echo $listings['id']; ?>" title="" >
 <?php echo wordwrap(ucfirst($listings['company_name']),30,'<br/>',1); ?></a></td>		                
		<?php /*<td><?php echo $listings['company_address']; ?></td> */ ?>
		<td><?php echo wordwrap($listings['email'],25,'<br />',1); ?></td>
		<td><?php echo '<a href='.URL_BASE.'add/taxi/'.$listings['cid'].' " title ="'.__('add_taxi').'" >'.__('add_taxi').'</a>' ; ?><?php echo '('.$listings['no_of_taxi'].')'; ?></td>
		<td><?php echo '<a href='.URL_BASE.'add/driver/'.$listings['cid'].' " title ="'.__('add_driver').'" >'.__('add_driver').'</a>' ; ?><?php echo '('.$listings['no_of_driver'].')'; ?></td>
		<td><?php echo '<a href='.URL_BASE.'add/manager/'.$listings['cid'].' " title ="'.__('add_manager').'" >'.__('add_manager').'</a>' ; ?><?php echo '('.$listings['no_of_manager'].')'; ?></td>
		<?php /*<td><?php echo '<a href='.URL_BASE.'add/packageupgrade/'.$listings['cid'].' "  title ="'.$change_title.'" >'.$change_title.'</a>' ; ?></td> */ ?>
		<td align="center" width="20" colspan='3' ><?php echo '<a href='.URL_BASE.'edit/company/'.$listings['id'].' " title ="'.__("edit").'" class="editicon"></a>' ; ?><?php echo '<br/><a href='.URL_BASE.'transaction/companytransaction/'.$listings['cid'].'/'.' " title ="'.__("view_transaction").'" class="transactionicon"></a>' ; ?></td>


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
		  
                <b><a href="javascript:selectToggle(true, 'frmcompany');"><?php echo __('all_label');?></a></b></li><li><span class="pr5 pl5">|</span></li>
				<li><b><a href="javascript:selectToggle(false, 'frmcompany');"><?php echo __('select_none');?></a></b></li>
				</ul>
                <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('Change Status'); ?></option>
                        <option value="block_company_request" ><?php echo __('Block'); ?></option>
                        <option value="active_company_request" ><?php echo __('Active'); ?></option>
                        <option value="trash_company_request" ><?php echo __('Trash'); ?></option>
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
 $("#keyword").focus(); 
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

					case "block_company_request":
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)? Blocked companies drivers will not be shown in dispatch & mobile apps');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.frmcompany.action="<?php echo URL_BASE;?>manage/block_company_request";
							 document.frmcompany.submit();
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

					case "active_company_request":
					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmcompany.action="<?php echo URL_BASE;?>manage/active_company_request";
								 document.frmcompany.submit();
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

					case "trash_company_request":
					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmcompany.action="<?php echo URL_BASE;?>manage/trash_company_request";
								 document.frmcompany.submit();
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
