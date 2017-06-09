<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<?php
if($_SESSION['user_type'] =='C') {
	$company_currency = findcompany_currency($_SESSION['company_id']);
	$company_currency = $company_currency[0]['currency_symbol'];
} else{
	$company_currency = CURRENCY;
}
$split = explode('/',$_SERVER['REQUEST_URI']);
$list = $split[3];
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">
           <div class="driverinfo_common">
		   <?php if($transaction_details[0]['payment_type'] !='3') { ?>
               <h2 class="tab_sub_tit"><?php echo ucfirst(__('transaction_details')); ?></h2>
                        <ul>
			   <li><label><?php echo __('transactionid_label'); ?></label>
                               <p><?php if(isset($transaction_details[0]['transaction_id']) && $transaction_details[0]['transaction_id'] != '') { echo $transaction_details[0]['transaction_id']; } else { echo '-'; } ?></p>
				   </li> 	

			<?php /*   <tr>
			   <td valign="top" width="20%"><label><?php echo __('correlation_id'); ?></label>:</td>        
			   <td>
				   <div class="new_input_field">
				   <?php if(isset($transaction_details[0]['correlation_id'])) { echo $transaction_details[0]['correlation_id']; } else { echo ''; } ?>
				   </div>
			   </td>   	
			   </tr> 	*/ ?>
						   
			   <li><label><?php echo __('amount'); ?></label>
                               <p><?php if(isset($transaction_details[0]['amt']) && $transaction_details[0]['amt'] > 0) { echo $company_currency.$transaction_details[0]['amt']; } else { echo '-'; } ?></p>
				   </li>

			<li><label><?php echo __('paymentstatus_label'); ?></label>
				<p><?php if(isset($transaction_details[0]['payment_status']) && $transaction_details[0]['payment_status'] == '1') { echo 'Paid'; } else { echo 'Not Paid'; } ?></p>
			</li>
		</ul>
		
			<?php } ?>

               <h2 class="tab_sub_tit"><?php echo ucfirst(__('trip_details')); ?></h2>
			   <ul>
			   <li><label><?php echo __('passenger_name'); ?></label>
                               <p><?php echo (isset($transaction_details[0]['passenger_name']))?$transaction_details[0]['passenger_name'] : '--'; ?></p>
				   </li> 	

			   <li><label><?php echo __('driver_name'); ?></label>
                               <p><?php echo isset($transaction_details[0]['driver_name'])?$transaction_details[0]['driver_name']:"--"; ?></p>
				   </li> 	
						   
			   <li><label><?php echo __('companyname'); ?></label>
                               <p><?php echo isset($transaction_details[0]['company_name']) ? $transaction_details[0]['company_name'] : '--'; ?></p>
				   </li> 

			  <?php /* <tr>
			   <td valign="top" width="20%"><label><?php echo __('journey_date'); ?></label>:</td>        
			   <td>
				   <div class="new_input_field">
				   <?php echo $transaction_details[0]['createdate']; ?>
				   </div>
			   </td>   	
			   </tr> 	*/ ?>
			   
			   
			    <li><label><?php echo __('pickup_time'); ?></label>
					<p><?php
                                        
                                            
						$pickup_time_defaultformat=(isset($transaction_details[0]['actual_pickup_time']) && $transaction_details[0]['actual_pickup_time'] != '0000-00-00 00:00:00' &&  $transaction_details[0]['actual_pickup_time']!='')?$transaction_details[0]['actual_pickup_time']:"0000-00-00 00:00:00";
                                      
						if($pickup_time_defaultformat !="0000-00-00 00:00:00"){
							$pickup_time=Commonfunction::getDateTimeFormat($pickup_time_defaultformat,1);
						}else{
							$pickup_time="---";
						}
					?>
				   <?php echo $pickup_time; ?></p>
				   </li>
			    	
			   <li><label><?php echo __('drop_time'); ?></label>
				   <p><?php
						$drop_time_defaultformat=(isset($transaction_details[0]['drop_time']) && $transaction_details[0]['drop_time']!='') ?$transaction_details[0]['drop_time']:"0000-00-00 00:00:00";
						
						if($drop_time_defaultformat !="0000-00-00 00:00:00"){
							$drop_time=Commonfunction::getDateTimeFormat($drop_time_defaultformat,1);
						}else{
							$drop_time="---";
						}
					?>
				   <?php echo $drop_time; ?></p>
				   </li> 
				
				<li><label><?php echo __('total_time'); ?></label>
					   <p><?php
							if($pickup_time != "---" && $drop_time != "---") {
								echo $manage_transaction->dateDiff($drop_time_defaultformat,$pickup_time_defaultformat);
							} else {
								echo "---";
							}
                                                        ?></p>
					   </li> 

			   <li><label><?php echo __('Current_Location'); ?></label>
                               <p><?php echo $transaction_details[0]['current_location']; ?></p>
				   </li> 

			   <li><label><?php echo __('Drop_Location'); ?></label>
				   <p><?php $drop_location="";
					($transaction_details[0]['drop_location']!="")?
					$drop_location = $transaction_details[0]['drop_location']: 
					$drop_location = $manage_transaction->getaddress($transaction_details[0]['drop_latitude'],$transaction_details[0]['drop_longitude']); ?>
				   <?php echo $drop_location; ?></p>
				   </li> 

			  <?php /* <tr>
			   <td valign="top" width="20%"><label><?php echo __('No_Passengers'); ?></label>:</td>        
			   <td>
				   <div class="new_input_field">
				   <?php echo $transaction_details[0]['no_passengers']; ?>
				   </div>
			   </td>   	
			   </tr> */ ?>

			   <li><label><?php echo __('distance'); ?></label>
                               <p><?php if($transaction_details[0]['distance'] < 0) { echo '-'; } else { echo $transaction_details[0]['distance'].' '.$transaction_details[0]['distance_unit']; } ?></p>
					</li>
			   
			   <li><label><?php echo __('distance_fare'); ?></label>
                               <p><?php if($transaction_details[0]['tripfare'] <= 0) { echo '-'; } else { echo $company_currency.round(($transaction_details[0]['tripfare']-$transaction_details[0]['minutes_fare']),2); } ?></p>
				   </li>
			   
			   <li><label><?php echo __('trip_minutes').'('.$transaction_details[0]['trip_minutes'].__('minutes').')'; ?></label>
                               <p><?php if($transaction_details[0]['minutes_fare'] <= 0) { echo '-'; } else { echo $company_currency.round($transaction_details[0]['minutes_fare'],2); } ?></p>
				   </li>
			   
			 <?php /*   <tr>
			   <td valign="top" width="20%"><label><?php echo __('trip_time'); ?></label>:</td>        
			   <td>
				   <div class="new_input_field">
				   <?php 
					 //$passenger_discount = $transaction_details[0]['passenger_discount'];
					 //$account_discount = $transaction_details[0]['account_discount'];
					 //echo $transaction_details[0]['fare'];
				   if($transaction_details[0]['tripfare'] == 0) { echo '-'; } else {
					    
				   		
						/*if($passenger_discount > 0)
						{
							$fare = round($transaction_details[0]['fare']+$passenger_discount,3);
							//echo $fare;
						}
						elseif($account_discount > 0)
						{
							$fare = round($transaction_details[0]['fare']+$account_discount,3);
							////echo $fare;
						} 

					//$fare = $transaction_details[0]['tripfare']-$transaction_details[0]['taxi_waiting_cost'];//$fare-$transaction_details[0]['company_tax'];
					$fare = $transaction_details[0]['tripfare'];
					echo $company_currency.round($fare,2);
					
					}
					?>
				   </div>
			   </td>   	
			   </tr>  */ ?>
			   
			   <li><label><?php echo __('waiting_time_hours'); ?></label>
                               <p><?php if(isset($transaction_details[0]['taxi_waiting_time']) && $transaction_details[0]['taxi_waiting_time'] > 0 ) { echo $transaction_details[0]['taxi_waiting_time']; }else{ echo "-"; } ?></p>
				   </li>
			   <li><label><?php echo __('waiting_time_cost'); ?></label>
                               <p><?php if($transaction_details[0]['taxi_waiting_cost'] > 0) { echo $company_currency.round($transaction_details[0]['taxi_waiting_cost'],2); } else { echo '-'; } ?></p>
				   </li>
			   
			   <?php if($transaction_details[0]['nightfare_applicable'] == 1) { ?>
			   <li><label><?php echo __('nightfare'); ?></label>
                               <p><?php if(isset($transaction_details[0]['nightfare']) && $transaction_details[0]['nightfare'] > 0) { echo $company_currency.round($transaction_details[0]['nightfare'],2);  } else { echo '-'; } ?></p>
				   </li>
			   <?php } ?>
			   
			   <?php if($transaction_details[0]['eveningfare_applicable'] == 1) {  ?>
			   <li><label><?php echo __('eveningfare'); ?></label>
                               <p><?php if($transaction_details[0]['eveningfare'] == 0) { echo '-'; } else { echo $company_currency.round($transaction_details[0]['eveningfare'],2); } ?></p>
				   </li>
			   <?php } ?>
			  
			   
			     <li><label><?php echo __('tax').'('.$transaction_details[0]['org_tax'].'%)'; ?></label>
                                 <p><?php if($transaction_details[0]['company_tax'] == 0) { echo '-'; } else { echo $company_currency.round($transaction_details[0]['company_tax'],2); } ?></p>
				   </li>
			   
			   <li><label><?php echo __('Sub Total'); ?></label>
				   <p><?php 
						/*if($passenger_discount > 0)
						{
							$subtotal = round($transaction_details[0]['tripfare']+$transaction_details[0]['company_tax']+$transaction_details[0]['minutes_fare'],3);
							echo $company_currency.round($subtotal,2);
						}
						elseif($account_discount > 0)
						{
							$subtotal = round($transaction_details[0]['tripfare']+$transaction_details[0]['company_tax']+$transaction_details[0]['minutes_fare'],3);
							echo $company_currency.round($subtotal,2);
						} */
							//$subtotal = round($transaction_details[0]['tripfare']+$transaction_details[0]['company_tax'],3);
							$subtotal = round($transaction_details[0]['fare']+$transaction_details[0]['used_wallet_amount'],3);
							//$subtotal = round($transaction_details[0]['fare'],3);
							echo $company_currency.round($subtotal,2);

						
                                                        ?></p>
				   </li>
			   
			   <li><label><?php echo __('wallet_amount_paid'); ?></label>
                               <p><?php echo $company_currency.round($transaction_details[0]['used_wallet_amount'],2); ?></p>
					</li>
			   
			   <?php /* <tr>
			   <td valign="top" width="20%"><label><?php echo __('Discount'); ?></label>:</td>        
			   <td>
				   <div class="new_input_field">
				   <?php 
						$passenger_discount = $transaction_details[0]['passenger_discount'];
						$account_discount = $transaction_details[0]['account_discount'];
						if($passenger_discount > 0)
						{
							echo $company_currency.round($passenger_discount,2);
						}
						elseif($account_discount > 0)
						{
							echo $company_currency.round($account_discount,2);
						}
				    ?>
				   </div>
			   </td>   	
			   </tr>   */ ?>
			   <li><label><?php echo __('trip_time'); ?></label>
				   <p><?php 
						/*if($passenger_discount > 0)
						{
							$fare = $subtotal - $passenger_discount;
							echo $company_currency.round($fare);
						}
						elseif($account_discount > 0)
						{
							$fare = $subtotal - $account_discount;
							echo $company_currency.round($fare);
						}*/
						//$fare = round($transaction_details[0]['tripfare']+$transaction_details[0]['company_tax'],3);
						$fare = round($transaction_details[0]['fare'],3);
							echo $company_currency.round($fare,2);
                                                        ?></p>
				   </li>

			   <li><label><?php echo __('travel_status'); ?></label>
                               <p><?php if($transaction_details[0]['travel_status'] == 0) { echo __('not_completed'); } else if($transaction_details[0]['travel_status'] == 1) { echo __('completed'); } else if($transaction_details[0]['travel_status'] == 2) { echo __('inprogress'); } else if($transaction_details[0]['travel_status'] == 3) { echo __('start_to_pickup'); } else if($transaction_details[0]['travel_status'] == 4) { echo __('cancel_by_passenger'); } else if($transaction_details[0]['travel_status'] == 8) { echo __('cancelled_by_dispatcher'); } else if($transaction_details[0]['travel_status'] == 9 || (isset($transaction_details[0]['driver_reply']) && $transaction_details[0]['driver_reply'] == 'C')) { echo __('cancelled_by_driver'); } else { echo __('not_completed'); } ?></p>

				   </li> 
			<?php if($transaction_details[0]['driver_comments'] !='') { ?>
			   <li><label><?php echo __('reason'); ?></label>
                               <p><?php echo $transaction_details[0]['driver_comments'];  ?></p>
				   </li>
			   <?php } ?> 
			<?php if(isset($transaction_details[0]['comments']) && $transaction_details[0]['comments'] !='') { ?>
			   <li><label><?php echo __('reason'); ?></label>
                               <p><?php echo $transaction_details[0]['comments'];  ?></p>
				   </li>
			   <?php } ?> 
			<?php if($transaction_details[0]['passenger_app_version'] !='') { ?>
			   <li><label><?php echo __('passenger_app_version'); ?></label>
                               <p><?php echo $transaction_details[0]['passenger_app_version'];  ?></p>
					   </li>
			<?php } ?> 
			
			<?php if($transaction_details[0]['driver_app_version'] !='') { ?>
			   <li><label><?php echo __('driver_app_version'); ?></label>
                               <p><?php echo $transaction_details[0]['driver_app_version'];  ?></p>
					   </li>
			<?php } ?>
			
			<?php if($transaction_details[0]['help_comment'] !='') { ?>
			   <li><label><?php echo __('help_content'); ?></label>
                               <p><?php echo $transaction_details[0]['help_content'];  ?></p>
					   </li>
			   <li><label><?php echo __('help_comment'); ?></label>
                               <p><?php echo $transaction_details[0]['help_comment'];  ?></p>
					   </li>
			   <li><label><?php echo __('help_comment_date'); ?></label>
                               <p><?php echo $transaction_details[0]['help_comment_date'];  ?></p>
					   </li>
			<?php } ?>

		</ul>
           </div>
        

<?php 

//$driver_tracking  = $driver_trackings;
if(SHOW_MAP !=1) {
 if(isset($transaction_details[0]['active_record'])) { 

$driver_latlo = "[".$transaction_details[0]['active_record'].']';
$driver_latlog=str_replace(',,',',',$driver_latlo);
$driver_default = explode(',',$transaction_details[0]['active_record']);

$count =  count($driver_default);
$driver_centerlat = str_replace('[','',$driver_default[0]);
$driver_centerlng = str_replace(']','',$driver_default[1]);

$last_value=$driver_default[$count-1];
if($last_value==""){
$driver_endlat = str_replace('[','',$driver_default[$count-3]);
$driver_endlng = str_replace(']','',$driver_default[$count-2]);
}else{
$driver_endlat = str_replace('[','',$driver_default[$count-2]);
$driver_endlng = str_replace(']','',$driver_default[$count-1]);
}

?>		
           <div class="over_all">
               <div class="widget" style="margin-bottom: 5px;">
			<div class="title"><h6><?php echo __('trip_route_map'); ?></h6>
				<div style="width:auto; float:right; margin: 4px 3px;">		
				<div class="button greyish"></div>
				</div>
			</div>
			<div id="on_going_trip_map" >
				
					   <div id="map"></div>
			</div>
		</div>
           </div>

</div>
    </div>
</div>  


  <script type="text/javascript">
    var map;
    $(document).ready(function(){

      map = new GMaps({
        el: '#map',
        lat: <?php echo $driver_centerlat; ?>,
        lng: <?php echo $driver_centerlng; ?>,
        click: function(e){
          console.log(e);
        }
      });

      map.addMarker({
        lat: <?php echo $driver_centerlat; ?>,
        lng: <?php echo $driver_centerlng; ?>,
        title: 'Start Point',
        details: {
          database_id: 42,
          author: 'HPNeo'
        },
       /* click: function(e){
          if(console.log)
            console.log(e);
          alert('You clicked in this marker');
        },
        mouseover: function(e){
          if(console.log)
            console.log(e);
        }*/
      });
      map.addMarker({
        lat: <?php echo $driver_endlat; ?>,
        lng: <?php echo $driver_endlng; ?>,
        title: 'End Point',
        /*infoWindow: {
          content: '<p>HTML Content</p>'
        }*/
      });


	path = <?php echo $driver_latlog; ?>;

      map.drawPolyline({
        path: path,
        strokeColor: 'green',
        strokeOpacity: 0.6,
        strokeWeight: 6
      });


    });
  </script>

<?php } ?>
<style>
#map{
  display: block;
  width: 100%;
  height: 500px;
  margin-top:20px;	
  margin: 0 auto;
}
#map.large{
  height:500px;
}
</style>
<?php 
}
?>
