<?php defined('SYSPATH') OR die("No direct access allowed."); 
echo html::script('public/common/ckeditor/ckeditor.js'); 

//For search values
//=================
$user_type_val = isset($srch["user_type"]) ? $srch["user_type"] :''; 
$company_val = isset($srch["filter_company"]) ? $srch["filter_company"] :''; 
$status_val = isset($srch["status"]) ? $srch["status"] :''; 
$keyword = isset($srch["keyword"]) ? $srch["keyword"] :''; 

//For CSS class deefine in the table if the data's available
//===========================================================
$total_users = count($all_user_list);

$table_css = $export_excel_button="";
?>
<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<div class="container_content fl clr">
	<div class="cont_container mt15 mt10">
		<div class="content_middle">
		<form method="POST" class="form" name="frmusers" id="frmusers" action="sendpromocode">
 
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<?php if($_SESSION['user_type'] =='A') { ?>
			<input type="hidden" name="company" value=""/>
			<?php /* <tr>
				<td valign="top" width="20%"><label><?php echo __('company'); ?></label></td>        
				<td>
					<div class="new_input_field">
						<select name="company" id="company" onchange="checkpromocode('');get_users('');" style="width:200px;" >
							<option value=""><?php echo __('All'); ?></option>
							<?php if(count($taxicompany_details) > 0) { ?>
							<?php foreach($taxicompany_details as $company_list) { ?>
								<option value="<?php echo $company_list['cid']; ?>"><?php echo ucfirst($company_list["company_name"]); ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
				</td>
			</tr> */ ?>
		<?php } ?>
		<tr>
			<td valign="top" width="20%"><label><?php echo __('Bulk Mail Option'); ?></label><span class="star">*</span></td>        
				<td>
				   <?php //print_r($errors);?>
				   <div class="new_input_field">
					 <select name="mail_type" id="mail_type" onchange="get_users(this.value);" style="width:100%;" class="required">
									<option value=""><?php echo __('select'); ?></option>				            			
                                    		<option value="1" selected><?php echo __('all_passenger');?></option>  
                                    		<option value="2"><?php echo __('select_passenger');?></option>  
									</select>
					  <?php //if(isset($errors) && array_key_exists('country_name',$errors)){ echo "<span class='error'>".ucfirst($errors['country_name'])."</span>";}?>
				   </div>
				</td>
		  </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('Passengers'); ?></label><span class="star">*</span></td>        
			   <td id="user_dd">
				   <?php echo __('all_passenger'); ?>
				   <input type="hidden" name="to_user[]" value="">
				   <div class="new_input_field">					   
		   
		      <?php //if(isset($errors) && array_key_exists('iso_country_code',$errors)){ echo "<span class='error'>".ucfirst($errors['iso_country_code'])."</span>";}?>
		   </div>
				</td>
				<input type="hidden" name="avail_passengers" id="avail_passengers" value="" >
		  </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('promocode'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required" title="<?php echo __('enterthepromocode'); ?>" minlength="3" maxlength="6" name="promo_code" id="promo_code" onblur="checkpromocode(this.value)" value="<?php echo $promocode;?>"   />
		      <?php //if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";} maxlength="10" minlength="4" ?>
		      <br /><span id="unameavilable" class="validerror"> </span>
		   </div>
           </td>   	
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('start_date'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required end_exp_valid" title="" name="start_date" id="start_date" readonly="readonly" value=""  />
		      <?php //if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>     
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('expire_date'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required start_exp_valid" title="" name="expire_date" readonly="readonly" id="expire_date" value="" />
		      <?php //if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>             
             

           <tr>
           <td valign="top" width="20%"><label><?php echo __('limit'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required onlynumbers" title="" name="limit" id="limit" min="1" value="" maxlength="5"  />
		      <?php //if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('Discount in %'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required numbersdots" title="<?php echo __('enterpromo_discount'); ?>" name="promo_discount" id="promo_discount" value="" maxlength="5"  />
		      <?php //if(isset($errors) && array_key_exists('telephone_code',$errors)){ echo "<span class='error'>".ucfirst($errors['telephone_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>           
               
           <tr>
           <td valign="top" width="20%"><label><?php echo __('subject'); ?></label><span class="star">*</span></td>        
			   <td>
			   <div class="new_input_field">
		   	  <input type="text" class="required" title="<?php echo __('subject'); ?>" name="subject" id="subject" value=""  maxlength="75" />
		      <?php //if(isset($errors) && array_key_exists('currency_code',$errors)){ echo "<span class='error'>".ucfirst($errors['currency_code'])."</span>";}?>
		   </div>
           </td>   	
           </tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('content'); ?></label></td>        
	   <td>
		<div class="new_input_field1">
			<textarea name="content" id="content" class="ckeditor" class="required" title="<?php echo __('entercontent'); ?>" rows="7" cols="35"><?php //if(isset($postvalue) && array_key_exists('content',$postvalue)){ echo $postvalue['content']; }?></textarea>
			<?php //if(isset($errors) && array_key_exists('content',$errors)){ echo "<span class='error'>".ucfirst($errors['content'])."</span>";}?>
		   </div>
           </td>
           </tr> 
          
	<tr>
            <td class="empt_cel">&nbsp;</td>
	
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>                         
                    <tr>
			<td>&nbsp;</td>
                        <td colspan="">
                            <div class="new_button">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
							<div class="new_button">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_addcountry" id="send_promocode" title="<?php echo __('submit' );?>" /></div>
                            <div class="new_button">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>                        

 

                 
</form>
</div>






</div>
</div>
<script type="text/javascript" language="javascript">
	
function get_users(mailtype)
{
	//alert(mailtype);
		if(mailtype == 2)
		{
			var company_id;
			// alert();
			<?php if($company_id == 0) { ?>
				company_id = $("#company").val();
			<?php } else { ?>
				company_id = '<?php echo $company_id; ?>';
			<?php } ?>
			  $.ajax({
				url:"<?php echo URL_BASE;?>manageusers/getuserslist?company_id="+company_id,
				type:"get",
				success:function(data){
					//alert(data);return false;
						$("#user_dd").html(data);	
				},
				error:function(data)
				{
					alert('error');
				}
			});	
		}
		else
		{
			$("#user_dd").html('<?php echo __('all_passenger'); ?>');
			$("#mail_type").val(mailtype);
		}
    
}

/** get promocode with company prefix **/
function get_company_promo(company_id)
{
	 $.ajax({
			url:"<?php echo URL_BASE;?>manageusers/getcompanypromo?company_id="+company_id,
			type:"get",
			success:function(data){
				//alert(data);return false;
					$("#promo_code").val(data.trim());	
			},
			error:function(data)
			{
				alert('error');
			}
		});	
}

 $.validator.addMethod("end_exp_valid", function(value, element) {
	 
	 $( "label" ).remove( ".errorvalid" );
	 
	 
	 //alert( value); 
	var expire_date=$('#expire_date').val();	
	var startdatevalue = $('#start_date').val();	
	
	if(start_date!=''&&value!='')
	{
		 //Check the expire date from the start date
		   if(Date.parse(expire_date) <= Date.parse(startdatevalue))
		   {			   
			   return false;
		   }
		   else
		   {
			   return true;			   
		   }
	}
	else
	{		
		return false;		
	} 
	 

 },"Expire date should be greater than start date"); 
 
 
 
 $.validator.addMethod("start_exp_valid", function(value, element) {
	 
	$( "label" ).remove( ".errorvalid" );	 
	
	var expire_date=$('#expire_date').val();	
	var startdatevalue = $('#start_date').val();	
	
	if(start_date!=''&&value!='')
	{
		 //Check the expire date from the start date
		   if(Date.parse(expire_date) <= Date.parse(startdatevalue))
		   {			   
			   return false;
		   }
		   else
		   {
			   return true;			   
		   }
	}
	else
	{		
		return false;		
	} 
		
	
	
	/* if(start_date!=''&&value!='')
	{
		var start_data=start_date.split(' ');
		var st_date=start_data[0].split('-');
		var st_time=start_data[1].split(':');
		
		var expire_data=value.split(' ');
		var ex_date=expire_data[0].split('-');
		var ex_time=expire_data[1].split(':');		
		
		var d1 = new Date(st_date.pop(), st_date.pop() - 1, st_date.pop(),st_time.pop(),st_time.pop(),st_time.pop());
		
		var d2 = new Date(ex_date.pop(), ex_date.pop() - 1, ex_date.pop(),ex_time.pop(),ex_time.pop(),ex_time.pop());
		
	
	//alert(d2);
		
		if(d1.getTime() > d2.getTime())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return true;
	} */
		 
 },"Expire date should be greater than start date");
	
//For Delete the users
//=====================
function frmdel_user(userid)
{
   var answer = confirm("<?php echo __('delete_alert2');?>");
    
	if (answer){
        window.location="<?php echo URL_BASE;?>admin/delete_passenger/"+userid;
    }
    
    return false;  
}  
function frmblk_user(userid,status)
{   
    window.location="<?php echo URL_BASE;?>admin/blkunblk_passenger/"+userid+"/"+status;    
    return false;  
}  

</script>
<script type="text/javascript">
 $(document).ready(function(){
	  jQuery("#frmusers").validate();
	//$("input[type='text']:first", document.forms[0]).focus();
	
			$("#start_date").datetimepicker( {
			showTimepicker:true,
			showSecond: true,
			timeFormat: 'hh:mm:ss',
			dateFormat: 'yy-mm-dd',
			stepHour: 1,
			stepMinute: 1,
			minDateTime : new Date(),
			stepSecond: 1,
			onClose: function( selectedDate ) {
				//alert(selectedDate);
				//$( "#expire_date" ).datepicker( "option", "minDateTime", selectedDate );
			}
			} );

			$("#expire_date").datetimepicker( {
			showTimepicker:true,
			showSecond: true,
			timeFormat: 'hh:mm:ss',
			dateFormat: 'yy-mm-dd',
			stepHour: 1,
			stepMinute: 1,
			minDateTime : new Date(), 
			stepSecond: 1,
			onClose: function( selectedDate ) {
				//$( "#start_date" ).datepicker( "option", "minDateTime", selectedDate );
			}			
			} );	
	//to show all type character in uppercase only 	
	$("#promo_code").bind('keyup', function (e) {
		if (e.which >= 97 && e.which <= 122) {
			var newKey = e.which - 32;
			// I have tried setting those
			e.keyCode = newKey;
			e.charCode = newKey;
		}
		this.value = this.value.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
		$("#promo_code").val(($("#promo_code").val()).toUpperCase());
	});
	
});
$( function() {
 $('#promo_discount').keyup(function(){
   if($(this).val().indexOf('.')!=-1){         
       if($(this).val().split(".")[1].length > 3){                
           if( isNaN( parseFloat( this.value ) ) ) return;
           this.value = parseFloat(this.value).toFixed(2);
       }  
    }            
    return this; //for chaining
 });
 
 
 //validation when there is no passengers
 $.validator.addMethod("promo_send_user", function(value, element) {
	 var toUser = $('#to_user').val();
	 if(toUser == '') {
		 return false;
	 } else {
		 return true;
	 }
},"There is no user to send promocode");
 
 /*$("#send_promocode").click(function(){
	 var flag;
	 var company_id;
	 
	<?php if($company_id == 0) { ?>
		company_id = $("#company").val();
	<?php } else { ?>
		company_id = '<?php echo $company_id; ?>';
	<?php } ?>
	  $.ajax({
			url:"<?php echo URL_BASE;?>manageusers/getpassengerscount?company_id="+company_id,
			type:"get",
			success:function(data){
				if(data == 0) {
					flag = false;
				} else {
					flag = true;
				}
			},
			error:function(data)
			{
				alert('error');
			}
		});	
		return flag;
 }); */
 
});

function checkpromocode(promocode)
{
	if(promocode == '') 
		promocode = $("#promo_code").val();
	var company_id;
	<?php if($company_id == 0) { ?>
		company_id = $("#company").val();
	<?php } else { ?>
		company_id = '<?php echo $company_id; ?>';
	<?php } ?>
	if(trim(promocode).length!=0 && trim(promocode).length >=3 && trim(promocode).length <=6){
	  loadurl("<?php echo URL_BASE;?>manageusers/checkpromocode?promo="+promocode+"&company_id="+company_id,"unameavilable");
	}else{
		$("#unameavilable").html('');
	}
}
$("#frmusers").validate({
        messages: {
			promo_code: { 
				minlength: "Please enter at least 3 characters."
			}
		}
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


					//	Current Action "reject"//block 
					//===================================

					case "block_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to block Request(s)?');?>";
	
					//Find checkbox whether selected or not and do more action
					//============================================================
					if($('input[type="checkbox"]').is(':checked'))
					{
				   		 var ans = confirm(confirm_msg)
				   		 if(ans){
							 document.frmusers.action="<?php echo URL_BASE;?>manageusers/block_passenger_request/index";
							 document.frmusers.submit();
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

					case "active_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to Activate Request(s)?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/active_passenger_request/index";
								 document.frmusers.submit();
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


                	//	Current Action "trash"
					//==========================

					case "trash_passenger_request":
					var confirm_msg =  "<?php echo __('Are you sure want to move Request(s) to Trash ?');?>";


						//Find checkbox whether selected or not and do more action
						//============================================================
						if($('input[type="checkbox"]').is(':checked'))
						{
					   		 var ans = confirm(confirm_msg)
					   		 if(ans){
								 document.frmusers.action="<?php echo URL_BASE;?>manageusers/trash_passenger_request/index";
								 document.frmusers.submit();
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
