<?php
defined('SYSPATH') OR die("No direct access allowed.");
$count_menu = count($site_menu_settings);
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
            <form method="POST" enctype="multipart/form-data" class="form" action="" >
		<?php if($count_menu > 0){ ?>
			<table class="0" cellpadding="5" cellspacing="0">
				<?php foreach($site_menu_settings as $menu_settings) { ?>
				<tr>
					<td>
						<label for="dummy3" style="float:left; width:100%; line-height:23px;"><?php echo __('menu_name_label'); ?>:</label><br/>
						<input type="text" class="required" title="<?php echo __('enter_menu_name_label'); ?>" name="menu_name1[]" id="menu_name" value="<?php echo $menu_settings['menu_name'];?>" ><br/>
						<label for="dummy3" style="float:left; clear:both; width:100%; line-height:23px;"><?php echo __('menulink_label'); ?> : </label><br/>
						<input type="text"  class="required" title="<?php echo __('enter_menulink_label'); ?>" name="menu_link1[]" id="menu_link" value="<?php echo $menu_settings['menu_link'];?>"></br>
						<label for="dummy3" style="float:left clear:both; width:100%; line-height:23px;"><?php echo __('publish_status_label'); ?></label><br/>
						<input type="radio" name="status_posts<?php echo $menu_settings['menu_id'];?>" <?php if($menu_settings['status_post']=='P'){ echo 'checked="checked"'; } else { echo ""; }?> id="status_post" value="Publish">Publish
						<input type="radio"  name="status_posts<?php echo $menu_settings['menu_id'];?>" <?php if($menu_settings['status_post']=='U'){ echo 'checked="checked"'; } else { echo ""; }?> id="status_post" value="Unpublish">Unpublish
					</td>
					<td valign="bottom" width="75">
						<a href="javascript:;" onClick="return delete_menus(<?php echo $menu_settings['menu_id'];?>)">Delete</a>
						<input type="hidden" name="cnt_contact1[]" id="cnt_contact"  value="<?php echo $menu_settings['order_status'];?>"/>
					</td>
				</tr>
		<?php } ?>
			</table>
		<?php }
		?>
		<table border="0" cellpadding="5" cellspacing="0" id="sub_add">
				<tr>
					<td><a  id="add_contact" --href="#" style="padding:7px 0; float:left; clear:both; width:100%;"><?php echo __('add_more'); ?></a></td>
				</tr>
				<tr class="trbg" style="display:none;">
				</tr>
		</table>  
		
		<?php //if($count_menu > 0){ ?>
		 <table class="0" cellpadding="5" cellspacing="0" id="mytable">                                               
                    <tr>
                        <td valign="top">&nbsp;</td>
                        <td style="padding-left:0px;">
                            <div class="button dredB"> <input type="reset" name="editsettings_reset" title="<?php echo __('button_reset'); ?>" value="<?php echo __('button_reset'); ?>"></div>
                            <div class="button greenB">  <input type="submit" name="editsettings_submit" title ="<?php echo __('button_update'); ?>" value="<?php echo __('button_update'); ?>"></div>

                        </td></tr>
                </table>
                <?php //} ?>
		
            </form>
            <br/><br/>

        </div>

        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt" ></div></div>
    </div>

</div>
<script type="text/javascript" language="javascript">
 $(document).ready(function() {
toggle(3);
});  
//For Dynamic Fields (menus)
//====================================
$(function(){ 

  $('#add_contact').click(function(){	
        var newRow = $("#sub_add tr").length-2; 
       
        /*if(newRow){
		$("#mytable").show();
	}*/
        $("#sub_add").append('<tr id="row_'+newRow+'" class="trbg"><td><label for="dummy3" style="float:left; width:100%; line-height:23px;"><?php echo __('menu_name_label'); ?>: </label></br><input type="text" class="required" title="<?php echo __('enter_menu_name_label'); ?>" name="menu_name[]" id="menu_name'+newRow+'" ><br/><label for="dummy3" style="float:left; clear:both; width:100%; line-height:23px;"><?php echo __('menulink_label'); ?> : </label><br/><input type="text"  class="required" title="<?php echo __('enter_menulink_label'); ?>" name="menu_link[]" id="menu_link'+newRow+'" ></br><label for="dummy3" style="float:left clear:both; width:100%; line-height:23px;"><?php echo __('publish_status_label'); ?></label><br><input type="radio" name="status_post'+newRow+'" checked="checked" id="status_post'+newRow+'" value="Publish"> Publish<input type="radio"  name="status_post'+newRow+'" id="status_post'+newRow+'" value="Unpublish"> Unpublish</td><td valign="bottom" width="75"><a href="javascript:;" onClick="return removetr_contact('+newRow+')">Delete</a><input type="hidden" name="cnt_contact[]" id="cnt_contact"  value="'+newRow+'" style="width:0px;"></td></tr>');
       
        return false;
        
    });
  });  
  
  function removetr_contact(rowid) {
   var r1 = "row_"+rowid;
   $("#sub_add tr").each(function () {    
    if(r1==$(this).attr('id')) {
     $(this).remove();
    }   
   });
	
   return false;
  }
 
var confirm_msg =  "<?php echo __('do_you_wanttodelete_this_menus');?>";
function delete_menus(id){
	var ans = confirm(confirm_msg);
	if(ans){
		window.location='<?php echo URL_BASE ;?>admin/delete_menus/'+id;
	}
}
</script>
