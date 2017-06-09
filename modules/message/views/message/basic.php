<div class="<?php echo $message->type; ?>_float_tt">
	<button type="button" class="close close_message" data-dismiss="alert">×</button>
	<label style="float: left; margin: 4px 0 0 6px;">
		<ul id="message" class="<?php echo $message->type.'_flash'; ?> fl">
			<?php
				if( is_array( $message->message ) ):
					foreach( $message->message as $msg ): ?>
						<li><?php echo $msg; ?></li>
					<?php
					endforeach;
				else: ?>
					<li><?php echo $message->message; ?></li>
				<?php endif; ?>
		</ul>
	</label>
</div>
