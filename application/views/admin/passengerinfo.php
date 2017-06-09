<?php defined('SYSPATH') OR die("No direct access allowed.");?>
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
          <div class="driverinfo_common">
              <h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2>
           <ul>
           <li><label><?php echo ucfirst(__('name')); ?></label>
		   <?php if(isset($user_details[0]['name'])) { echo $user_details[0]['name']; } else { echo ''; } ?>
		   </li> 	

           <li><label><?php echo __('email'); ?></label>
		   <?php if(isset($user_details[0]['email'])) { echo $user_details[0]['email']; } ?>
		   </li> 
           
           <li><label><?php echo __('mobile'); ?></label>
           <?php $country_code = (isset($user_details[0]['country_code']) && $user_details[0]['country_code'] != '') ? $user_details[0]['country_code'].' - ' : ""; ?>
		   <?php if(isset($user_details[0]['phone'])) { echo $country_code.$user_details[0]['phone']; } ?>
		   </li>                       		   
		   
           <li><label><?php echo __('address'); ?></label>
		   <?php if(isset($user_details[0]['address'])) { echo $user_details[0]['address']; } ?>
		   </li>
           
            <li><label><?php echo __('referral_code'); ?></label>
		   <?php if(isset($user_details[0]['referral_code'])) { echo $user_details[0]['referral_code']; } else { echo '-';} ?>
		   </li>
           
            <li><label><?php echo __('referral_amount')." ( ". __('referral_info')." )"; ?></label>
		   <?php if(isset($user_details[0]['referral_code_amount']) && $user_details[0]['referral_code_amount'] != 0) { echo $user_details[0]['referral_code_amount']; } else { echo '-';} ?>
		   </li>
           
            <li><label><?php echo __('wallet_amount'); ?></label>
		   <?php if(isset($user_details[0]['wallet_amount']) && $user_details[0]['wallet_amount'] != 0) { echo $user_details[0]['wallet_amount']; } else { echo '-';} ?>
		   </li>
           <li><label><?php echo __('referred_person'); ?></label>
		   <?php if(isset($user_details[0]['referred_by'])) { echo $user_details[0]['referred_by']; } else { echo '-';} ?>
		   </li>
	<!--  <tr>
           <td valign="top" width="20%"><label><?php //echo __('discounts_passenger'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php //if(isset($user_details[0]['discount'])) { echo $user_details[0]['discount']; } ?>
		   </div>
	   </td>   	
           </tr> -->
         </ul>
          </div>
     <?php /*if($_SESSION['user_type'] == 'C') { ?>
         <!-- Groups --> 
			<div class="widget">
				<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('groups'); ?></h6>
				 
			</div>    
<div class= "overflow-block">			
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive" id="changetr">
<?php if(count($user_group_details) > 0){ ?>
<thead>	
<tr>
	<td align="center" ><?php echo __('sno_label'); ?></td>
	<td align="center" ><?php echo __('group_name'); ?></td>
	<td align="center" ><?php echo __('limit'); ?></td>
</tr>
</thead>
<tbody>
           <?php $i=1; 
		 foreach($user_group_details as $groupresult) { 
		$trcolor=($i%2==0) ? 'oddtr' : 'eventr';  			 
			 ?>	
				<tr class="<?php echo $trcolor; ?>" id="addtr_<?php echo $groupresult['aid']; ?>">
					<td align="center"><?php echo $i; ?></td>
					<td align="center"><?php echo $groupresult['department']; ?></td>
					<td align="center"><?php echo $groupresult['limit']; ?></td>
				</tr>
	<?php $i++; } ?>

	<?php }else { ?>
			<tr>
				<td colspan="3" align="center"><?php echo __('no_data');?></td>
			</tr>
	<?php } ?>
</tbody>	
</table>	
        </div>
        </div>
<!-- Groups -->
<!-- Account -->
			<div class="widget">
				<div class="title"><img src="<?php echo IMGPATH; ?>icons/dark/frames.png" alt="" class="titleIcon" /><h6><?php echo __('Acounts'); ?></h6>
				 
			</div>    
<div class= "overflow-block">			
<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive" id="changetr">
<?php if(count($user_account_details) > 0){ ?>
<thead>	
<tr>
	<td align="center" ><?php echo __('sno_label'); ?></td>
	<td align="center" ><?php echo __('account_name'); ?></td>
	<td align="center" ><?php echo __('limit'); ?></td>
</tr>
</thead>
<tbody>
           <?php $i=1; 
		 foreach($user_account_details as $groupresult) { 
		$trcolor=($i%2==0) ? 'oddtr' : 'eventr';  			 
			 ?>	
				<tr class="<?php echo $trcolor; ?>" id="addtr_<?php echo $groupresult['aid']; ?>">
					<td align="center"><?php echo $i; ?></td>
					<td align="center"><?php echo $groupresult['account_name']; ?></td>
					<td align="center"><?php echo $groupresult['limit']; ?></td>
				</tr>
	<?php $i++; } ?>

	<?php }else { ?>
			<tr>
				<td colspan="3" align="center"><?php echo __('no_data');?></td>
			</tr>
	<?php } ?>
</tbody>	
</table>	
        </div>
	</div>
<!-- Account -->
<?php } */ ?>
<!-- Completed  Journey -->
<div class="over_all">	
<div class="widget margin-bottom" >
			<div class="title passengerinfo_title">
			<h6><?php echo __('completed_journey'); ?></h6>
			 <form  action="<?php echo URL_BASE;?>manage/genpdf" method="post" name="drivermgmt" id="drivermgmt" >
			  <div class="driverinfo_chattop">
                     <ul>
                        <li>
				 <label>
                           <?php echo __('startdate');?></label>
                           <div class="date_txt">
				 <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_details[0]['id']; ?>">
				 <input type="hidden" name="user_name" id="user_name" value="<?php echo $user_details[0]['name']; ?>">
				   <input type="hidden" name="user_type" id="user_type" value="P">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="userstartdate" name="userstartdate" value=""  />
				 <span id="startdate_error" class="errors" style="display:none;"></span>
				</div>
                        </li>
                        <li>
                           <label>
                           <?php echo __('enddate'); ?></label>
                           <div class="date_txt">
				 <input type="text"  readonly title="<?php echo __('select_datetime'); ?>"  id="userenddate" name="userenddate" value=""  />
				 <span id="enddate_error" class="errors" style="display:none;"></span>
                                  </div>
                        </li>
                        <li>
				 <div class="button blackB"> 
				 <input type="hidden" name="type_export" id="type_export" value="">
				 <input type="button" name="change_usercompany" id="change_usercompany" value="GO" title="Go" >
				 
				 </div>				
			 </li>
                     </ul>
                  </div>
			</form>
        	<?php /*?><div style="width:auto; float:right; margin: 0px 0px;">		
				<?php if($count_driver_logs_completed_transaction > REC_PER_PAGE) {  ?>
					<?php if($_SESSION['user_type'] == 'A') { ?> 
					<div class="button greyish">
					<a href="<?php echo URL_BASE ?>transaction/admintransaction_list?filter_company=All&taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a>
					</div>
					<?php } 
					else if($_SESSION['user_type'] == 'C')
					{ ?>
					<div class="button greyish">
					<a href="<?php echo URL_BASE ?>transaction/companytransaction_list?taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a>
					</div>
					<?php }
					else if($_SESSION['user_type'] == 'M')
					{ 
					?>
					<div class="button greyish"><a href="<?php echo URL_BASE; ?>transaction/managertransaction_list?taxiid=All&startdate=&enddate=&passengerid=All&driver_id=<?php echo $user_details1[0]['id']; ?>"><?php echo __('view_all'); ?></a></div>
					<?php }
				}
				else
				{ ?>            
				<div class="button greyish"></div>
				<?php } ?>
				</div> <?php */ ?>
			</div>
			<div id="drivercompleted_logs">
				<table cellspacing="1" cellpadding="10" width="100%" align="center" class="sTable responsive">
					<?php if(count($passenger_logs_completed_transaction)>0) { ?>
					
						
						<thead>
						<tr>
							<td>#</td>
							<td><?php echo __('passenger_name'); ?></td>
							<td><b><?php echo __('pickuploc_droploc'); ?></b></td>
							<td><b><?php echo __('pictup_date');?></b></td>
							<td><b><?php echo __('distance');?></b></td>									
							<td><b><?php echo __('tax');?></b></td>	
							<td><b><?php echo __('trip_total_fare');?></b></td>
							<?php /*<td><b><?php echo __('equivalent_to_usd').CURRENCY_FORMAT; ?></b></td> */ ?>
							
							</tr>
						</tr>
						</thead>					
						
						<?php 
						$i=1;
						($i%2 == 1)?$class="eventr":$class="oddtr";
						
							foreach($passenger_logs_completed_transaction as $values)
							{
								$dis = isset($values->distance) ? round($values->distance,2) : "";
								$dis_unit = isset($values->distance_unit) ? $values->distance_unit : "";
								$distance = $dis.' '.$dis_unit;
								$current_fare = round($values->fare,2);
								$company_tax = $values->Taxamt;
								//$percentvalue = ($company_tax/100)*$current_fare;
								//$currtotal = $current_fare - $company_tax;
								$travel_status = $values->travel_status;
								
								/*if($_SESSION['company_id'] != 0)
								{
									$company_currency = findcompany_currency($_SESSION['company_id']);
								}
								else
								{
									$company_currency = findcompany_currency($values->company_id);
								} */
								$company_currency = CURRENCY;
								//$company_currency_format = findcompany_currencyformat($values->company_id);
								//$convet_amt = currency_conversion($company_currency_format,$current_fare);
								//$convet_amt = round($convet_amt,2);
								
							if($travel_status == 0) { $status = __('critical'); $row_solor = 'style="color:#00FF00;"';  } elseif($travel_status == 1) { $status = __('completed'); $row_solor = 'style="color:#00FF00;"'; }  elseif($travel_status == 2) { $status = __('inprogress'); $row_solor = 'style="color:#0000FF;"'; }  if($travel_status == 3) { $status = __('start_to_pickup'); $row_solor = 'style="color:#FFFF00;"'; } elseif($travel_status == 4) { $status = __('cancel_by_passenger'); $row_solor = 'style="color:#990066;"';} elseif($travel_status== 5) { $status = __('waiting_payment'); $row_solor = 'style="color:#00FFFF;"';} elseif($travel_status == 6) { $status = __('missed'); $row_solor = 'style="color:#FF6633;"';} elseif($travel_status == 7) { $status = __('dispatched'); $row_solor = 'style="color:#003333;"'; }  elseif($travel_status == 8) { $status = __('cancelled'); $row_solor = 'style="color:#990000;"';} 		//echo $row_solor;
								?>
								<tr class="<?php echo $class; ?>">	
								<td><?php echo $i;?></td>
								<td><?php echo ucfirst($values->name); ?></td>
								<td><p <?php echo $row_solor;?>><?php echo $values->current_location;?></p>
								<p><?php echo $values->drop_location;?></p></td>
								<td><?php echo Commonfunction::getDateTimeFormat($values->pickup_time,1); ?></td>
								<td><?php echo $distance;?></td>								
								<td><?php echo $company_currency.$company_tax;?></td>
								<td><?php echo $company_currency.$current_fare;?></td>
								<?php /*<td><?php echo $convet_amt;?></td> */ ?>
								</tr>								
								<?php $i++;
							}
						 ?>
					<?php }else { ?>
							<tr>
								<td colspan="3" align="center"><?php echo __('no_data');?></td>
							</tr> 							
						<?php }?>									
				</table>
				<?php if(count($passenger_logs_completed_transaction)>0) { ?>
			    <div align="left" class="new_button"> 
					<input type="button" name="gen_pdf" id="gen_pdf" value="<?php echo __('gen_pdf');?>" title="<?php echo __('gen_pdf');?>" onclick="gen_pdf(this.value)">
			    </div>
			    <?php } ?>
				</div>
 
		</div>
</div>
    </div>
</div>  

<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_details[0]['id']; ?>">
<!-- Company Manager -->
<div id="user_ratings"></div>

<script type="text/javascript" language="javascript">
/* $(document).ready(function() {

change_user_rating();
});*/
function change_user_rating()
{
      		var user_id = $("#user_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getuserratinglist",
			type:"get",
			data:"user_id="+user_id+"&page="+page_no,
			success:function(data){
			$('#user_ratings').html();
			$('#user_ratings').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_user_rating(page_no)
{
		var user_id = $("#user_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getuserratinglist",
			type:"get",
			data:"user_id="+user_id+"&page="+page_no,
			success:function(data){
			$('#user_ratings').html();
			$('#user_ratings').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

$(document).ready(function () {
	$("#userstartdate").datetimepicker( {
		showTimepicker:DEFAULT_TIME_SHOW,
		showSecond: true,
		timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
		dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
		stepHour: 1,
		stepMinute: 1,
		maxDateTime : new Date(),
		stepSecond: 1
	});

	$("#userenddate").datetimepicker( {
		showTimepicker:DEFAULT_TIME_SHOW,
		showSecond: true,
		timeFormat: DEFAULT_TIME_FORMAT_SCRIPT,
		dateFormat: DEFAULT_DATE_FORMAT_SCRIPT,
		stepHour: 1,
		stepMinute: 1,
		maxDateTime : new Date(), 
		stepSecond: 1
	});
	
});


 $("#change_usercompany").click(function(){
 	var startdate = $("#userstartdate").val();
	var enddate = $("#userenddate").val();
	var passenger_id = $("#user_id").val();
	if(startdate =='')
	{
		$("#startdate_error").html("<?php echo __('select_startdate'); ?>");
		$("#startdate_error").show();
	}
	else
	{
		$("#startdate_error").html("");
		$("#startdate_error").hide();
	}
	if(enddate =='')
	{
		$("#enddate_error").html("<?php echo __('select_enddate'); ?>");
		$("#enddate_error").show();
	}
	else
	{
		$("#enddate_error").hide("");
		$("#enddate_error").hide();
	}
	if(startdate !='' && enddate!='')
	{
		$('#drivercompleted_logs').html('<img alt="ajax-loading" src="'+SrcPath+'/public/common/css/img/ajax-loaders/ajax-loader-1.gif" />');
		if(to_timestamp(startdate) > to_timestamp(enddate)) 
		{
			$("#startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#startdate_error").show();
		}
		else
		{
			$("#startdate_error").html("");
			$("#startdate_error").hide();
		var dataS = "startdate="+startdate+"&enddate="+enddate+"&passenger_id="+passenger_id;
		$.ajax
		({ 			
			type: "POST",
			url: SrcPath+"manage/passenger_completed_logs", 
			data: dataS, 
			cache: false, 
			dataType: 'html',
			success: function(response) 
			{ 	
				$('#drivercompleted_logs').html(response);			
			} 
			 
		});	
			
		}
	}
	 
 });
 

function gen_pdf(type)
{
	//alert(type);
 	var startdate = $("#userstartdate").val();
	var enddate = $("#userenddate").val();
	var user_id = $("#user_id").val();
	var driver_name = $('#driver_name').val();	
	$('#type_export').val(type);
	if(startdate =='')
	{
		$("#startdate_error").html("<?php echo __('select_startdate'); ?>");
		$("#startdate_error").show();
	}
	else
	{
		$("#startdate_error").html("");
		$("#startdate_error").hide();
	}
	if(enddate =='')
	{
		$("#enddate_error").html("<?php echo __('select_enddate'); ?>");
		$("#enddate_error").show();
	}
	else
	{
		$("#enddate_error").hide("");
		$("#enddate_error").hide();
	}
	if(startdate !='' && enddate!='')
	{
		if(to_timestamp(startdate) > to_timestamp(enddate)) 
		{
			$("#startdate_error").html("<?php echo __('startdate_greater'); ?>");
			$("#startdate_error").show();
		}
		else
		{
			//alert();
			$("#startdate_error").html("");
			$("#startdate_error").hide();
			document.forms['drivermgmt'].submit();
		}
	}
	 
 }
</script>
