<?php defined('SYSPATH') OR die("No direct access allowed."); 
?>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle content_middle_alignment manage_cont_view">
			
					<?php
					if(count($ContactsList) > 0){
					foreach($ContactsList as $contacts_list) { ?>
					<p><?php echo __('title_label'); ?> : <?php echo $contacts_list['menu_name'];?></p>
					<p><?php echo __('content'); ?> : <?php echo $contacts_list['content'];?></p>
					<p><?php echo __('link'); ?> : <?php echo $contacts_list['menu_link'];?></p>
					
					<?php } 
					} 
					else 
					{ ?>
					<p><?php echo __('no_data'); ?></p>
					<?php 
					} ?>
				
		</div>
	</div>
</div>
