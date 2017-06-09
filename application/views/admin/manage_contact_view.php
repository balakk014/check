<?php defined('SYSPATH') OR die("No direct access allowed."); 
?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
                    <div class="contact_view">
					<?php
					if(count($ContactsList) > 0){
					foreach($ContactsList as $contacts_list) { ?>
					<?php if($usertype!='C'){ ?>
                                        <p><label><?php echo __('companyname'); ?> </label><?php echo !empty($contacts_list['company_name'])?$contacts_list['company_name']:'Admin';?></p>
					<?php } ?>
                                        <p><label><?php echo __('name_label'); ?> </label> <?php echo $contacts_list['first_name'];?></p>
                                        <p><label><?php echo __('email_label'); ?></label> <a href="mailto:<?php echo $contacts_list['email'];?>" title="mailto" target="_blank"><?php echo $contacts_list['email'];?></a></p>
                                        <p><label><?php echo __('subject'); ?> </label><?php echo $contacts_list['subject'];?></p>
                                        <p><label><?php echo __('message'); ?> </label><?php echo $contacts_list['message'];?></p>
					<?php if($contacts_list['phone'] != '') { ?>
                                        <p><label><?php echo __('phone_label'); ?> </label> <?php echo $contacts_list['phone'];?></p>
					<?php } ?>
                                        <p><label><?php echo __('sent_date'); ?></label> <?php echo Commonfunction::convertphpdate("Y-m-d H:i:s",$contacts_list['sent_date']); ?></p>
					
					<?php  } 
					} 
					else 
					{ ?>
					<p><?php echo __('no_data'); ?></p>
					<?php 
					} ?>
                    </div>
		</div>
	</div>
</div>
<script type="text/javascript">
 $(document).ready(function(){
	toggle(22);
});
</script>
