<?php defined('SYSPATH') OR die("No direct access allowed.");

$status_val = isset($srch["status"]) ? $srch["status"] :''; 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 

$total_content=count($all_content_list);
$total_contentlist=count($ContentList);

if(isset($all_content_list)){
$table_css=$export_excel_button="";
if($total_content >0)
{
	$table_css='class="table_border"'; 

	$export_excel_button='<input type="button"  title="'.__('button_export').'" class="button" value="'.__('button_export').'" onclick="location.href=\''.URL_BASE.'manage/export?keyword='.$keyword.'\'" />';
}?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle"> 

       		<div class="widget">
		<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin: 4px 3px;">
		<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       

		</div>
		</div>
<?php if($total_content > 0){ ?>
<div class= "overflow-block">
<?php } ?>
<form method="get" class="form" name="frmcompany" id="frmcompany" action="companysearch">
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_content > 0){ ?>
<thead>
	<tr>
		<td align="left" width="8%"><?php echo __('Select'); ?></td>
		<td align="left" width="8%" style="min-width: 22px !important;" ><?php echo 'Status'; ?></td>		
		<td align="left" width="8%"><?php echo __('sno_label'); ?></td>
		 <td align="left" style="text-align:left;" width="15%"><?php echo __('menu'); ?></td>
		<td align="left" style="text-align:left;" width="15%"><?php echo __('page_title'); ?></td>
		 <td align="left" style="text-align:left;" width="15%"><?php echo __('page_url'); ?></td>
		<td align="left" width="10%"><?php echo __('action_label'); ?></td>
	</tr>
</thead>
<tbody>		
		<?php

         $sno=$Offset; /* For Serial No */

		 foreach($all_content_list as $listings) {
		 //S.No Increment
		 $sno++;
         //For Odd / Even Rows
         //===================
         $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';  
 
        ?>     

        <tr class="<?php echo $trcolor; ?>">

                   <td align="center"> 
                        <input type="checkbox" name="uniqueId[]" id="trxn_chk<?php echo $listings['id'];?>" value="<?php echo $listings['id'];?>" />
                    </td>

                    <td align="center"> 
                         <?php 
                             if($listings['status']=='1')
                             {  $txt = "Active"; $class ="unsuspendicon";    }
                             else{  $txt = "Deactive"; $class ="blockicon";      }


                             echo '<a  title ='.$txt.' class='.$class.'></a>' ;  
                         ?>  

                    </td> 			
			
			<td align="center"><?php echo $sno; ?></td>
			 <td align="left"><?php echo wordwrap(ucfirst($listings['menu_name'])); ?></td>
			<td align="left"><?php echo wordwrap(ucfirst($listings['title'])); ?></td>
			 <td align="left"><?php echo wordwrap(ucfirst($listings['page_url'])); ?></td>
			<td style="clear:both;text-align:center;"><a href="<?php echo URL_BASE.'manage/company_content_edit/'.$listings['id'];?>" class="editicon" title="Edit"></a></td>
			

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
<?php if ($total_content > 0) { ?>
</div>
<?php } ?>
</form>
</div>
</div>
</div>
 <div class="bottom_contenttot">
<?php if(count($all_content_list) > 0)
       { ?>
          <div class="select_all">
              <ul><li>
                <b><a href="javascript:selectToggle(true, 'frmcompany');"><?php echo __('all_label');?></a></b><li><li><span class="pr2 pl2">|</span></li><li><b><a href="javascript:selectToggle(false, 'frmcompany');"><?php echo __('select_none');?></a></b></li>
              </ul>

              <span class="more_selection">
                    <select name="more_action" id="more_action">
                        <option value=""><?php echo __('Change Status'); ?></option>
                        <option value="block_company_request" ><?php echo __('Block'); ?></option>
                        <option value="active_company_request" ><?php echo __('Active'); ?></option>
                    </select>
                 </span>
	        </div>
        <?php
        } ?>
<div class="pagination">
		<?php if(($action != 'content_search') && $total_content > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
 </div>
</div>
<?php } ?>
<script type="text/javascript">
 $(document).ready(function(){
	toggle(24);
});

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
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.frmcompany.action="<?php echo URL_BASE;?>manage/status_company_content/0";
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
								 document.frmcompany.action="<?php echo URL_BASE;?>manage/status_company_content/1";
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
