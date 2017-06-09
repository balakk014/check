<?php
defined('SYSPATH') OR die("No direct access allowed.");

$sms_account_id = '';
$sms_auth_token = '';
$sms_from_number = '';
?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>

<div style="display: none;">
<svg id="delete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" enable-background="new 0 0 12 12">
    <g>
    <path d="M12 2c0 .5-.4 1-.8 1H.8C.4 3 0 2.5 0 2s.4-1 .8-1H4c0-.5.7-1 1.2-1h1.6C7.3 0 8 .6 8 1h3.2c.4 0 .8.5.8 1zm-1 2.6v6.6c0 .4-.8.8-1.2.8H2.2c-.4 0-1.2-.3-1.2-.8V4.6c0-.1.5-.6.7-.6h8.7c.1 0 .6.5.6.6zM5 6.4c0-.2-.2-.4-.4-.4h-.2c-.2 0-.4.2-.4.4v3.2c0 .2.2.4.4.4h.2c.2 0 .4-.2.4-.4V6.4zm3 0c0-.2-.2-.4-.4-.4h-.2c-.2 0-.4.2-.4.4v3.2c0 .2.2.4.4.4h.2c.2 0 .4-.2.4-.4V6.4z"/>
    <g>
</svg>
 <svg version="1.1" id="question_mart" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 92 92" style="enable-background:new 0 0 92 92;" xml:space="preserve">
        <g>
            <path d="M45.386,0.004C19.983,0.344-0.333,21.215,0.005,46.619c0.34,25.393,21.209,45.715,46.611,45.377
                c25.398-0.342,45.718-21.213,45.38-46.615C91.656,19.986,70.786-0.335,45.386,0.004z M45.25,74l-0.254-0.004
                c-3.912-0.116-6.67-2.998-6.559-6.852c0.109-3.788,2.934-6.538,6.717-6.538l0.227,0.004c4.021,0.119,6.748,2.972,6.635,6.937
                C51.904,71.346,49.123,74,45.25,74z M61.705,41.341c-0.92,1.307-2.943,2.93-5.492,4.916l-2.807,1.938
                c-1.541,1.198-2.471,2.325-2.82,3.434c-0.275,0.873-0.41,1.104-0.434,2.88l-0.004,0.451H39.43l0.031-0.907
                c0.131-3.728,0.223-5.921,1.768-7.733c2.424-2.846,7.771-6.289,7.998-6.435c0.766-0.577,1.412-1.234,1.893-1.936
                c1.125-1.551,1.623-2.772,1.623-3.972c0-1.665-0.494-3.205-1.471-4.576c-0.939-1.323-2.723-1.993-5.303-1.993
                c-2.559,0-4.311,0.812-5.359,2.478c-1.078,1.713-1.623,3.512-1.623,5.35v0.457H27.936l0.02-0.477
                c0.285-6.769,2.701-11.643,7.178-14.487C37.947,18.918,41.447,18,45.531,18c5.346,0,9.859,1.299,13.412,3.861
                c3.6,2.596,5.426,6.484,5.426,11.556C64.369,36.254,63.473,38.919,61.705,41.341z"/>
        </g>
    </svg>   
    
   

    
    
</div>


<div class="account_outer">
	<form action="" name="frm_domains" id="frm_domains" method="post" onsubmit="return sms_settings()">
    
            <div class="domain_det_list">
                <div class="account_lft_det">
                    <div class="acc_tit"><h2>Set your primary domain</h2></div>
                    <div class="acc_det">
                        <p>Your primary domain is the one you want customers and search engines to see.</p>
                    </div>
                </div>
                <div class="account_rgt_det">
                    <div class="rgt_lay sms_lay">
                        <div class="primary_domain_det">
                            <label class="next_label">Primary domain</label>
                            <div class="small_sel">
                                <select class="form_control">
                                    <option value="<?php echo $live_domain_name;?>"><?php echo $live_domain_name;?></option>                                    
                                </select>
                            </div>
                            <div class="traffic_redirect">
                                <div class="checkbox_primary">
                                    <input type="checkbox" id="domain_traffic" name="domain_traffic" <?php if($redirect_enable_status==1){ echo 'checked=checked';} ?>/>
                                    <label for="domain_traffic">Redirect all traffic to this domain</label>
                                </div>
                                <p class="input_help_text">This will redirect traffic from all your domains to the single primary domain.</p>
                            </div>
                        </div>
                        <div class="bottom_butt_sec">
                            <div class="align_right">
                                <input class="common_butt" name="save_redirect" id="save_redirect" type="submit" value="Save" />
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        
            <div class="domain_det_list">
                <div class="account_lft_det">
                    <div class="acc_tit"><h2>Manage domains</h2></div>
                    <div class="acc_det">
                        <p>You can manage email forwarding, renew domains you've purchased and remove domains from your store.</p>
                    </div>
                </div>
                <div class="account_rgt_det">
                    <div class="rgt_lay">
                        <div class="table_wrapper domain_list">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th>Status</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span><?php echo $_SERVER['HTTP_HOST'];?></span></td>
                                        <td class="domain-status-first">
                                            
                                            <span class="status-success">
                                               OK
                                            </span>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td class="tr">&nbsp;</td>
                                    </tr>
                                    
                                    <tr>
                                        <td><span><a href="#"><?php echo $live_domain_name;?></a></span>
                                            <input type="hidden" name="hid_domain_name" id="hid_domain_name" value="<?php echo $live_domain_name;?>"</td>
                                            <td class="domain-status"><div class="domain_load_img"><img src="<?php echo URL_BASE;?>public/cloud_package/loading.gif"/></div></td>
                                        <td class="setup_status"></td>
                                        <td class="tr"><a class="del_ico" href="<?php echo URL_BASE;?>package/domain_delete" onclick="return confirm_delete();"><svg role="img" viewBox="0 0 408.483 408.483" class="icon_12"><g><use xlink:href="#delete" class="icon_12"></use></g></svg></a></td>
                                    </tr>
<!--                                    <tr class="no-border">
                                        <td><span><a href="#">jallicart.com</a></span></td>
                                        <td class="domain-status"><span class="domain_sta">Setup required</span></td>
                                        <td><a class="complete_setup" href="#">Complete setup</a></td>
                                        <td class="tr"><a class="del_ico" href="#"><svg role="img" viewBox="0 0 408.483 408.483" class="icon_12"><g><use xlink:href="#delete" class="icon_12"></use></g></svg></a></td>
                                    </tr>-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(PACKAGE_TYPE==3){ ?>
            <div class="domain_det_list">
                <div class="account_lft_det">
                    <div class="acc_tit"><h2>SSL certificates</h2></div>
                    <div class="acc_det">
                        <p>Add another layer of security to your online store with free SSL certificates for each of your custom domains.</p>
                    </div>
                </div>
                <div class="account_rgt_det">
                    <div class="rgt_lay ssl_sec">
                        <div class="billing_lft ssl_details">
                            <h2 class="comm_tit">SSL certificate encryption</h2>
                            <p>An SSL certificate is requested for each domain added to your store. When your SSL certificate is ready, your online store traffic is redirected from HTTP to encrypted HTTPS.</p>
                            <ul>
                                <li>If you use Google Webmaster Tools, make sure to <a href="#">update your sitemap.</a></li>
                            </ul>
                        </div>
                        <div class="billing_rgt">
                            <svg id="big_lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 96">
                                <g>
                                <path fill="#E4EAD4" d="M47.497 45.632H21.898c-.827 0-1.497.677-1.497 1.511v22.74c0 .835.67 1.511 1.497 1.511h.683c.827 0 1.497.677 1.497 1.511v2.156c0 .835-.67 1.511-1.497 1.511-.827 0-1.497.677-1.497 1.511v2.156c0 .835.67 1.511 1.497 1.511.827 0 1.497.677 1.497 1.511v2.156c0 .835-.67 1.511-1.497 1.511-.827 0-1.497.677-1.497 1.511v4.681c0 .835.67 1.511 1.497 1.511h24.275c.818 0 1.485-.664 1.496-1.49l.641-45.977c.012-.842-.661-1.532-1.496-1.532z"></path>
                                <path fill="#CCDBAF" d="M77.248 45.632h-5.952V34.218c0-9.975-8.038-18.091-17.918-18.091-9.88 0-17.918 8.116-17.918 18.091v11.415h-5.952V34.218c0-13.289 10.708-24.101 23.869-24.101s23.87 10.811 23.87 24.101v11.414z"></path>
                                <path fill="#5F5370" d="M77.248 46.062h-5.951c-.828 0-1.5.407-1.5-.43V34.218c0-9.14-7.365-16.577-16.418-16.577-9.053 0-16.418 7.437-16.418 16.577v11.415c0 .837-.671.43-1.5.43h-5.952c-.829 0-1.5.407-1.5-.43V34.218c0-14.124 11.38-25.615 25.369-25.615 13.989 0 25.37 11.491 25.37 25.615v11.415c0 .836-.672.429-1.5.429zm-4.451-1.944h2.951v-9.9c0-12.454-10.035-22.586-22.37-22.586-12.334 0-22.369 10.132-22.369 22.586v9.9h2.952v-9.9c0-10.811 8.711-19.606 19.418-19.606 10.707 0 19.418 8.795 19.418 19.606v9.9z"></path>
                                <path fill="#A0BF82" d="M34.039 45.632h52.317v25.762H34.039zm0 41.297h52.317v7.621H34.039zm0-10.356h52.317v5.178H34.039zm-3.534-5.179h52.317v5.178H30.505zm0 10.357h52.317v5.178H30.505z"></path>
                                <path fill="#5F5370" d="M86.356 72.909H20.402c-.829 0-1.5-.678-1.5-1.515V47.408c0-1.814 1.461-3.29 3.258-3.29h62.438c1.796 0 3.258 1.476 3.258 3.29v23.987c0 .836-.672 1.514-1.5 1.514zM21.902 69.88h62.954V47.408c0-.144-.115-.261-.258-.261H22.16c-.143 0-.258.117-.258.261V69.88zm61.149 18.564H23.707c-.829 0-1.5-.678-1.5-1.515V81.75c0-.836.671-1.515 1.5-1.515h59.344c.828 0 1.5.678 1.5 1.515v5.179c0 .837-.672 1.515-1.5 1.515zm-57.844-3.029h56.344v-2.15H25.207v2.15z"></path>
                                <path fill="#5F5370" d="M83.051 78.087H23.707c-.829 0-1.5-.678-1.5-1.515v-5.178c0-.836.671-1.515 1.5-1.515h59.344c.828 0 1.5.678 1.5 1.515v5.178c0 .837-.672 1.515-1.5 1.515zm-57.844-3.029h56.344v-2.149H25.207v2.149z"></path>
                                <path fill="#5F5370" d="M86.356 83.266H20.402c-.829 0-1.5-.678-1.5-1.515v-5.179c0-.836.671-1.515 1.5-1.515h65.954c.828 0 1.5.678 1.5 1.515v5.179c0 .836-.672 1.515-1.5 1.515zm-64.454-3.03h62.954v-2.15H21.902v2.15zM84.598 96H22.16c-1.797 0-3.258-1.476-3.258-3.29v-5.78c0-.836.671-1.515 1.5-1.515h65.954c.828 0 1.5.678 1.5 1.515v5.78c0 1.814-1.462 3.29-3.258 3.29zm-62.696-7.556v4.266c0 .144.116.261.258.261h62.438c.143 0 .258-.117.258-.261v-4.266H21.902z"></path>
                                <path fill="#FFB762" d="M7.168 91.721c.338.091.686-.112.776-.454 0 0 .229-.866.423-2.165.001-.01.003-.021.004-.031.273-1.909 1.757-3.404 3.648-3.68.009-.001.019-.003.028-.004 1.286-.197 2.144-.428 2.144-.428.211-.058.389-.225.449-.453.09-.342-.111-.693-.449-.784 0 0-.858-.231-2.144-.428-.024-.004-.048-.007-.072-.011-1.868-.268-3.331-1.749-3.597-3.634-.003-.024-.007-.047-.01-.071-.195-1.299-.424-2.165-.424-2.165-.057-.213-.222-.392-.449-.454-.339-.091-.687.112-.777.454 0 0-.229.866-.424 2.165-.022.15-.043.306-.062.468-.206 1.669-1.505 2.978-3.158 3.186-.158.02-.312.041-.46.063-1.286.197-2.144.427-2.144.427-.211.058-.389.225-.449.454-.09.342.111.693.449.784 0 0 .858.231 2.144.428.043.006.087.013.131.019 1.832.258 3.275 1.712 3.53 3.561.006.046.013.091.019.136.195 1.299.423 2.165.423 2.165.059.212.224.391.451.452zm74.697-74.528c.393.106.798-.13.903-.528 0 0 .266-1.007.492-2.517.002-.012.003-.024.005-.036.318-2.22 2.043-3.959 4.242-4.28.011-.002.022-.003.033-.005 1.496-.229 2.494-.498 2.494-.498.246-.067.452-.261.522-.527.105-.398-.129-.806-.522-.912 0 0-.998-.269-2.494-.498l-.084-.012c-2.172-.312-3.874-2.033-4.183-4.226-.004-.028-.008-.055-.012-.082-.227-1.511-.493-2.517-.493-2.517-.067-.248-.258-.456-.522-.528-.394-.106-.798.13-.903.528 0 0-.266 1.007-.493 2.517-.023.159-.045.325-.067.495-.245 1.962-1.776 3.512-3.719 3.759-.17.022-.334.044-.493.068-1.496.229-2.494.497-2.494.497-.246.067-.452.261-.522.528-.105.398.129.806.522.912 0 0 .998.269 2.494.497.05.007.101.015.152.022 2.13.3 3.809 1.991 4.106 4.142.007.053.015.106.022.158.227 1.511.492 2.517.492 2.517.066.246.258.454.522.526zm12.009 12.548c.276.075.561-.092.635-.371 0 0 .187-.708.346-1.769.003-.019.005-.038.008-.057.217-1.54 1.416-2.752 2.941-2.971.02-.003.039-.006.059-.009 1.051-.161 1.753-.35 1.753-.35.173-.047.318-.184.367-.371.074-.279-.091-.567-.367-.641 0 0-.701-.189-1.753-.35-.031-.005-.061-.009-.093-.013-1.508-.214-2.69-1.409-2.902-2.932-.004-.031-.009-.061-.013-.092-.159-1.062-.346-1.769-.346-1.769-.047-.174-.182-.321-.367-.371-.277-.075-.561.091-.635.371 0 0-.187.708-.346 1.769-.018.123-.035.251-.051.382-.168 1.364-1.23 2.434-2.581 2.604-.129.016-.255.033-.376.051-1.051.161-1.753.349-1.753.349-.173.047-.318.184-.367.371-.074.279.091.566.367.641 0 0 .701.189 1.753.35.052.008.104.015.157.022 1.47.202 2.628 1.369 2.828 2.853.007.055.015.109.023.162.159 1.062.346 1.769.346 1.769.046.175.182.321.367.372z"></path>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            
            
 </form>
</div>

<div class="ui_footer_help">
    <div class="ui_footer_help_content">
        <div class="help_icon">
            <svg role="img" viewBox="0 0 53 53" class="icon_24"><g><use xlink:href="#question_mart" class="icon_24"></use></g></svg>
        </div>
        <div><p>Need help? Go to the Taximobility  <a href="#" target="_blank">Domains Manual</a></p></div>
    </div>
</div>


<script>
 function confirm_delete() {   
        
	var strconfirm = confirm("Are you sure want to delete this domain?");
	if (strconfirm == true) {
		return true;
	} 
        return false;
}
function ajax_call(){
    var domain_verify_conn='test';
    var domain_name=$("#hid_domain_name").val();
$.ajax({
			url:"<?php echo URL_BASE;?>package/domain_verify",
			type:"POST",
			data:"domain_verify_conn="+domain_verify_conn+"&domain_name="+domain_name,
			success:function(data){                            
                           
                            var obj = $.parseJSON(data);           
                           if(obj.dns_status==1){
                               $('.domain-status').html('');
                               $('.domain-status').html('<span class="status-success">OK</span>');
                               $('.setup_status').html('');
                           }else{
                               $('.domain-status').html('');
                               $('.domain-status').html('<span class="domain_sta">Setup required</span>');
                               $('.setup_status').html('');
                               $('.setup_status').html('<a class="complete_setup" href="<?php echo URL_BASE;?>package/domain_connect">Complete setup</a>');
                           }
                           
                            
			},
			error:function(data)
			{
				//alert(cid);
			}
		});

}
$(document).ready(function () {	
	ajax_call();
			
});
</script>


