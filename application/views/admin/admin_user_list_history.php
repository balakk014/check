<?php defined('SYSPATH') OR die("No direct access allowed."); 

//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$status_val = isset($srch["status"]) ? $srch["status"] :''; 							
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 

//For CSS class deefine in the table if the data's available
//===========================================================
$total_users=count($all_user_list);

$table_css=$export_excel_button="";
?>

<div class="bread_crumb" >
	<!-- common config  home link -->
	<?php 
	$atag_start='<a href="'.URL_BASE.'admin/login" title="Home">'; 
	$atag_end='</a>';?>	
	<?php echo $atag_start. "Home".$atag_end;?>
	<span class="fwn"><img src="<?php echo URL_BASE;?>public/admin/images/list_arrow_medium.png" width="14px" height="14px" _class="mt5"/></span>
	
	<div style="float: left;"><?php echo __('menu_manage_user'); ?></div>
	</div>

<div class="container_content fl clr">
<div class="cont_container mt15 mt10">

    <div class="content_top"><div class="top_left"></div><div class="top_center"></div><div class="top_rgt"></div></div>
    <div class="content_middle">
    
 
        <form method="post" class="admin_form" name="frmusers" id="frmusers" action="search">



<div class="clr">&nbsp;</div>
<div style="float: right;">

</div> 
<div class="clr">&nbsp;</div>


<table cellspacing="0" cellpadding="10" width="100%" align="center" <?php echo $table_css; ?>>
<?php if($total_users > 0){ ?>

	<tr class="rowhead">


<!--** Multiple select Starts Here ** -->
 <th align="center"><?php echo __('Select'); ?></th>
<th align="center" style="min-width: 22px !important;" ><?php echo 'Status'; ?></th>
<!--** Multiple select ends Here ** -->


		<th align="center" width="5%"><?php echo __('sno_label'); ?></th>
		<th align="center" width="15%"><?php echo __('username_list_label'); ?></th>
		<th align="center" width="10%"><?php echo __('email_label'); ?></th>
		<th align="center" width="10%"><?php echo __('location_label'); ?></th> 
		<th align="center" width="10%"><?php echo __('logintype_label'); ?></th>
		<th align="center" width="10%"><?php echo __('account_type'); ?></th>
		<th align="center" width="10%"><?php echo __('description');?></th>
		<th align="center" width="10%"><?php echo __('action_label'); ?></th>

	</tr>
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



<!--** Multiple select Starts Here ** -->

                    <td align="center"> <?php //echo $job_category['serviceid'];?>
                        <input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['id'];?>" value="<?php echo $listings['id'];?>" />
                    </td>

                    <td align="center"> 
                         <?php 
                             if($listings['status']=='A')
                             {  $txt = __('deactivate'); $class ="unsuspendicon";    }

                             else{  $txt = __('activate'); $class ="blockicon";      }


                             echo '<a href="javascript:void(0);" title ='.$txt.' class='.$class.'></a>' ;  
                         ?>
                    </td> 

<!--** Multiple select ends Here ** -->

			<td align="center"><?php echo $sno; ?></td>
			<td align="center">

<?php /*
				<a href="<?php echo URL_BASE;?>users/userprofile/<?php echo $listings['id'];?>" title ="<?php echo isset($listings['name'])?ucfirst($listings['name']):ucfirst($listings['username']); ?>" target="_blank">
                </a>
    */ ?>

                    <?php echo wordwrap(ucfirst($listings['name']),30,'<br/>',1); ?>

			</td>

			<td align="center"><?php echo wordwrap($listings['email'],25,'<br />',1); ?></td>
            <td align="center"><?php echo ($listings['location']!="")?$listings['location']:"_";?></td> 

<?php /*	
<td><?php echo isset($listings['industry'])?wordwrap($listings['industry'],10,'<br/>',1):"_"; ?></td>
*/?>
			<td align="center">
				 <?php
				 if($listings['login_type'] == ""){?>
				 <p class="normaluser" title="<?php echo __('taxi_user'); ?>" ></p>
				 <?php }
				 ?>
				 <?php
				 if($listings['login_type'] == FACEBOOK){?>
				 <p class="fbuser" title="<?php echo __('facebook_user'); ?>"></p>
				 <?php }
				 ?>				
				 <?php
				 if($listings['login_type'] == TWITTER){?>
				 <p class="twitteruser" title="<?php echo __('twitter_user'); ?>"></p>
				 <?php }
				 ?>
				 <?php
				 if($listings['login_type'] == GOOGLEPLUS){?>
				  <p class="googleplususer" title="<?php echo __('googleplus_user'); ?>"></p>
				 <?php }
				 ?>			
			
			</td>
		    <td align="center"><?php echo ($listings['account_type']==1)?__('caregiver'):__('careseeker'); ?></td>


			<td align="center" width="20"><?php echo ($listings['description']!="")?wordwrap(ucfirst($listings['description']),20,'<br/>',1):"_"; ?></td>


			<td align="center" width="20" colspan='3' ><?php echo '<a href='.URL_BASE.'admin/editprofile/'.$listings['id'].' " title ="Edit" class="editicon"></a>' ; ?></td>

 <?php /*
            <td align="center" width="20">

                         <?php
                                 if($listings['status']=='A')
                                 {  $txt = "Deactivate"; $class ="unsuspendicon";    }

                                 else{  $txt = "Activate"; $class ="blockicon";      }


                                if($listings['user_type'] != 'A')
                                {
                                    echo '<a onclick=frmblk_user("'.$listings['id'].'","'.$listings['status'].'");  title ='.$txt.' class='.$class.'></a>' ;  
                                }
                                else
                                {     echo "_";    } 
                         ?>

            </td> 
			<td align="center" width="20"><?php if($listings['user_type'] != 'A'){echo '<a onclick="frmdel_user('.$listings['id'].');" title="Delete" class="deleteicon"> </a>';}else{echo "_";} ?></td> 

             */ ?>
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
</table>

</form>
</div>
<div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
  <div class="bottom_contenttot">
 <!--** Multiple select starts Here ** -->
<?php if(count($all_user_list) > 0)
       { ?>
          <div class="select_all">
              <ul><li>
                <b><a href="javascript:selectToggle(true, 'frmusers');"><?php echo __('all_label');?></a></b></li>
                  <li><span class="pr2 pl2">|</span></li>
                  <li><b><a href="javascript:selectToggle(false, 'frmusers');"><?php echo __('select_none');?></a></b></li>
              </ul>

               <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('change_status'); ?></option>
                        <option value="active_users_request" ><?php echo __('active'); ?></option>
                        <option value="delete_users_request" ><?php echo __('delete'); ?></option>
                    </select>
                 </span>
	        </div>
        <?php
        } ?>
<!--** Multiple select ends Here ** -->
 <div class="pagination">
		<?php if(($action != 'search') && $total_users > 0): ?>
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
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/active_users_request/history";
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

					case "delete_users_request":
					var confirm_msg =  "<?php echo __('areyousure_wanttodelete_request');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/delete_users_request/history";
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
