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
<form method="post" class="form" name="managerating_form" id="managerating_form" action="contacts_search">
       		<div class="widget">
		<?php /* <div class="title"><h6><?php echo $page_title; ?></h6>
		<div style="width:auto; float:right; margin: 0;">
		<div class="button greyishB"> <?php //echo $export_excel_button; ?></div>                       

		</div>
		</div> */?>
<?php if($total_content > 0){ ?>
<div class= "overflow-block">
<?php } ?>
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
<?php if($total_content > 0){ ?>
<thead>
	<tr>
		<td align="center" width="5%"><?php echo __('sno_label'); ?></td>
		<td align="left" width="20%"><?php echo __('name_label'); ?></td>
		<!-- <td align="left" width="10%"><?php echo __('menu_status'); ?></td> -->
		<td align="center" width="10%"><?php echo __('action_label'); ?></td>
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
		if($listings['status_post'] == 'P')
		{
			$status_post = __('status_published');;
		} else {
			$status_post = __('status_unpublished');;
		}	 
        ?>     

        <tr class="<?php echo $trcolor; ?>">

			<td align="center"><?php echo $sno; ?></td>
			<td align="left"><a title="<?php echo ucfirst($listings['menu_name']); ?>" href="<?php echo URL_BASE.'manage/content_view/'.$listings['id'];?>"><?php echo wordwrap(ucfirst($listings['menu_name'])); ?></a></td>
			<!-- <td align="center"><?php echo $status_post; ?></td> -->
			<td align="center"><a href="<?php echo URL_BASE.'manage/content_edit_view/'.$listings['id'];?>" class="editicon" title="View details"></a><?php if($listings['id'] !=3 && $listings['id'] !=9){  echo '<a onclick="delete_contacts('.$listings["id"].');" title ="Delete" class="deleteicon"></a>' ;}else{ echo '<p style="background:none" class="deleteicon">--</p>';} ?></td>
			

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
<div class="pagination">
		<?php if(($action != 'content_search') && $total_content > 0): ?>
		 <?php echo $pag_data->render(); ?>
		<?php endif; ?> 
  </div>
 </div>

</div>
<?php } ?>
<script type="text/javascript">
var confirm_msg =  "<?php echo __('Do you want to delete this content');?>";
function delete_contacts(id){
	var ans = confirm(confirm_msg);
	if(ans){
		window.location='<?php echo URL_BASE ;?>manage/delete_content/'+id;
	}
}
</script>
