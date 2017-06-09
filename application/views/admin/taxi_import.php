<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">   
       <form name="registration_form" id="registration_form" class="form" action="" method="post" enctype="multipart/form-data" data-form="server-form">
       <table border="0" cellpadding="5" cellspacing="0" width="100%">            

	<tr>
            <td><h2 class="tab_sub_tit"><?php echo ucfirst(__('driver_import')); ?></h2></td>
	<td style="float:right"><div class="new_button"><input type="button" value="<?php echo __('download_sample'); ?>" onclick="window.location='<?php echo URL_BASE."public/uploads/sample/taxi_sample.xlsx"; ?>'" /></div>
</td>	          
	</tr>	
	
	<td valign="top" width="25%"><label><?php echo __('excel_file'); ?></label><span class="star">*</span></td>        
	<td>
	<div class="new_input_field">
              <input type="file" title="<?php echo __('excel_file'); ?>" id="excel_file" name="excel_file" value="<?php if(isset($postvalue) && array_key_exists('excel_file',$postvalue)){ echo $postvalue['excel_file']; }?>"  />

         <span class="error_msg"><?php 
                     echo '<br/>';
                    if(count($errors) > 0)
                    {
                        foreach($errors as $ekey => $eval)
                        {
                            if(is_array($eval) > 0)
                            {
                                echo "<b>Record - ".$ekey."</b><br/>";
                                foreach($eval as $key => $val )
                                {

                                        echo ucfirst($key)."  - ".ucfirst($val)."<br/>";
                                }
                                echo isset($errors['excel_file']) ? ucfirst($errors['excel_file']) : "";
                            }
                            else 
                            {
                               echo  $eval; 
                            }


                        }
                    }
                    ?></span>
        </div>
	</td>   	
	</tr>

							
<tr>
	<td  class="empt_cel">&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
			<input type="text" name="submit_driver" value="form" style="display:none;"/>
                           <div class="new_button"><input type="button" value="<?php echo __('button_back'); ?>" onclick="window.location='<?php echo URL_BASE."manage/driver"; ?>'" /></div>
                            <div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_driver" title="<?php echo __('submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" onclick="change_state('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>');change_city('<?php echo DEFAULT_COUNTRY; ?>','<?php echo DEFAULT_STATE; ?>','<?php echo DEFAULT_CITY; ?>')" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                        </td>
                    </tr> 

                </table>
        </form>
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  

<script type="text/javascript">
$(document).ready(function(){
});
    
    
</script>
