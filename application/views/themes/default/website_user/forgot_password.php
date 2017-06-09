<div class="forgot_form" style="display: none;">
	<h3><?php echo __('forgot_password'); ?></h3>
<!--	<a href="javascript:;" class="close_butt">&nbsp;</a>-->
	<form name="forgot_form" method="post" autocomplete="off">
		<ul>
			<li>
				<div class="full_name_mobile">
					<span>
						<input type="text" class="country_code_restrict" tabindex="1" onpaste="return false;" title="Country Code" name="country_code" value="" placeholder="<?php echo TELEPHONECODE ?>*" maxlength="5"/>
					</span>
					<div class="mob_no">
						<input type="text" name="mobile_number" tabindex="2" class="txtboxToFilter" maxlength="15" value="" onpaste="return false;" placeholder="<?php echo __('mobile_number'); ?>*" title="<?php echo __('mobile_number'); ?>"/>
					</div>
				</div>
				<em id="forgot_code_phone_error"></em>
			</li>
			<li>
				<div class="sub_butt">
					<input type="button" name="forgot_submit" tabindex="3" onclick="forgotSubmit();" value="<?php echo __('btn_submit'); ?>" title="<?php echo __('btn_submit'); ?>"/>
				</div>
			</li>
		</ul>
	</form>
</div>

<script type="text/javascript">
/** Signin form tab focus within a form **/
$(document).ready( function () {
	tab_index("forgot_form");

	$("form[name='forgot_form'] :input").keypress(function (e) {
		if (e.which == 13) {
			forgotSubmit();
			return false;
		}
	});
	
	$("form[name='forgot_form'] :input").keypress(function (e) {
		if (e.which == 13) {
			forgotSubmit();
			return false;
		}
	});
});
function forgotSubmit()
{
	$.ajax({
		url:'<?php echo URL_BASE; ?>users/passenger_forgot_password',
		type:'post',
		dataType:'json',
		data:$("form[name='forgot_form']").serialize(),
		beforeSend:function() {
			$("input[name='forgot_submit']").val("<?php echo __('please_wait'); ?>").attr("disabled","disabled");
			$(".response_forgot_error").remove();
		},
		success:function(json)
		{
			if(json.error)
			{
				$("form[name='forgot_form'] em#forgot_code_phone_error").html("");
				$("input[name='forgot_submit']").val("<?php echo __('btn_submit'); ?>").removeAttr("disabled");
				$.each(json.error,function(k,v) {
					$("form[name='forgot_form'] em#forgot_code_phone_error").html(ucFirst(v));
				}); 
			}
			if(json.redirect)
			{
				window.location = json.redirect;
			}
		}
	});
	tab_index("forgot_form");
}
</script>
