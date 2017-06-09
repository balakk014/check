<?php defined('SYSPATH') OR die("No direct access allowed."); 
$select_pack_id ='';  ?>
<?php /* <script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery-1.6.3.min.js"></script> */ ?>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>
<style>thead{border:1px solid #CDCDCD;}table{ background:#fff; }
#dr_full
{
	width:800px;
	padding:10px;
	margin-left: 20%;
}
.dr_left
{
	width:350px;
	float:left;
}
.dr_right
{
	width:350px;
	float:left;
}

p{
	background:none;
	padding:10px;
}
h1{margin-left:-200px;}


</style>
<!-- time picker start-->
<link rel="stylesheet" href="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/css/ui-lightness/jquery-ui-1.8.11.custom.css" />
<?php /* <script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-1.5.1.min.js"></script> */ ?>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-1.8.11.custom/js/jquery-ui-1.8.11.custom.min.js"></script>
<script src="<?php echo URL_BASE;?>public/common/js/datetimehrspicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/validation/jquery.validate.js"></script>

<!-- time picker start-->

<?php
	$start_booking_date = date('Y/m/d H:i:s');echo"<br>";
	$date = strtotime("+10 day", time());
	$end_booking_date= date('Y/m/d H:i:s', $date);
	$latitude="34.0500";
	$longitude="-118.2500";
	
	
?>
<?php 

 if(count($driver_details) && count($passenger_details) > 0){?>
 <?php /*
<table cellspacing="2" cellpadding="10" width="60%" align="center" class="sTable responsive">
	<h1> Passenger Login Details</h1>
<thead>
		<tr>
		<td align="left" width="5%"><b><?php echo __('sno'); ?></b></td>
		<td align="left" width="5%"><b><?php echo __('Username/Mobile'); ?></b></td>
		<td align="left" width="5%"><b><?php echo __('password'); ?></b></td>
		</tr>
</thead>
<tbody>	
<?php
		/*$sno=""; 
		
		 krsort($driver_details);
		 foreach($driver_details as $listings) 
		 {
			  $sno++;
			  $trcolor=($sno%2==0) ? 'oddtr' : 'eventr';   ?>
			  
			 <tr class="<?php echo $trcolor; ?>">
			 <td align="center"  width="5%"><?php echo $sno; ?></td>
			 <td align="center"  width="5%"><?php  echo $listings['phone'];?></td>
			 <td align="center"  width="5%"><?php echo $listings['org_password']; ?></td>
			 </tr>
			
			<?php  
		 }*/?>
		 
 </tbody>	
 </table>	
 
 <?php /*
<table cellspacing="1" cellpadding="10" width="60%" align="center" class="sTable responsive">
<h1 > Driver Login Details</h1>
<thead>
	<tr>
		<td align="left" width="5%"><b><?php echo __('sno'); ?></b></td>
		<td align="left" width="5%"><b><?php echo __('Username/Mobile'); ?></b></td>
		<td align="left" width="5%"><b><?php echo __('password'); ?></b></td>
	</tr>
</thead>
<tbody>	

<?php
		$sno=""; 
		
		 foreach($driver_details as $listings) 
		 {
			  $sno++;
			  $trcolor=($sno%2==0) ? 'oddtr' : 'eventr'; ?>			  
			 <tr class="<?php echo $trcolor; ?>">
			 <td align="center"  width="5%"><?php echo $sno; ?></td>
			 <td align="center"  width="5%"><?php  echo $listings['phone'];?></td>
			 <td align="center"  width="5%"><?php echo $listings['org_password']; ?></td>
			 </tr>
			
			<?php  
		 }?>
		  </tbody>	
			  </table>*/	?>
		<div id="dr_full">
				<div class="dr_left">
					<h1 align="center"> Driver Login Details</h1>
					
					<?php
					 krsort($driver_details);
					 foreach($driver_details as $listings) 
						 { ?>
					  <p>Username/Mobile Number&nbsp;:&nbsp;<?php  echo $listings['phone'];?></p>
					  <p>Password&nbsp;:&nbsp;<?php echo $listings['org_password']; ?></p>
					<?php }?>

				</div>


				<div class="dr_right">
					<h1 align="center" > Passenger Login Details</h1>
					<?php 
					 krsort($passenger_details);
					foreach($passenger_details as $listings) 
						 { ?>
						<p>Username/Mobile Number&nbsp;:&nbsp;<?php  echo $listings['phone'];?></p>
						<p>Password&nbsp;:&nbsp;<?php echo $listings['org_password']; ?></p>
					<?php }?>
				</div>		  
			  
			  
		</div>
		 
  <?php      
}
else
{
?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle create_loginouter">    
         <form name="addcompany_form" class="form" id="addcompany_form" action="" method="post" enctype="multipart/form-data">
         <table border="0" cellpadding="5" cellspacing="0" width="100%">
			 
	    <tr>
			<td><b><?php echo __('Personal information'); ?></b></td>
			<td> <input type="hidden" title=""  name="salutation" value="Mr"  /></td>	
	    </tr>     
	   
	    <tr>
			<td valign="top" width="20%"><label><?php echo __('firstname'); ?></label><span class="star">*</span></td>        
			<td>
			<div class="new_input_field">
				<input type="text" title="<?php echo __('enterfirstname_msg'); ?>" class="required" name="firstname" id="firstname" value="<?php if(isset($postvalue) && array_key_exists('firstname',$postvalue)){ echo $postvalue['firstname']; }?>"  minlength="4" maxlength="30" />
            <?php if(isset($errors) && array_key_exists('firstname',$errors)){ echo "<span class='error'>".ucfirst($errors['firstname'])."</span>";}?>
			</div>
			</td>   	
        </tr>        
           
		<tr>
			<td valign="top" width="20%"><label><?php echo __('lastname'); ?></label><span class="star">*</span></td>        
			<td>
			<div class="new_input_field">
				<input type="text" title="<?php echo __('enterfirstname_msg'); ?>" class="required" name="lastname" id="lastname" value="<?php if(isset($postvalue) && array_key_exists('lastname',$postvalue)){ echo $postvalue['lastname']; }?>"  minlength="4" maxlength="30" />
            <?php if(isset($errors) && array_key_exists('lastname',$errors)){ echo "<span class='error'>".ucfirst($errors['lastname'])."</span>";}?>
			</div>
			</td>   	
		</tr>        
       
         <tr>
           <td valign="top" width="20%"><label><?php echo __('mobile'); ?></label><span class="star">*</span></td>        
			<td>
			<div class="new_input_field">
              <input type="text" title="<?php echo __('entermobileno'); ?>" name="phone" id="phone" class="required" value="<?php if(isset($postvalue) && array_key_exists('phone',$postvalue)){ echo $postvalue['phone']; }?>" minlength="6"   maxlength="20" />
              <?php if(isset($errors) && array_key_exists('phone',$errors)){ echo "<span class='error'>".ucfirst($errors['phone'])."</span>";}?>
		    </div>
           </td>   	
        </tr>  
        
        <tr>
	<?php $field_type =''; if(isset($postvalue) && array_key_exists('company_id',$postvalue) && empty($cid)){ $field_type =  $postvalue['company_id']; } else if(!empty($cid)) { $field_type = $cid; } ?>
	<td valign="top" width="20%"><label><?php echo __('taxicompany'); ?></label><span class="star">*</span></td>
	<td>



	<div class="formRight new_input_field total_login">
	<div class="selector" id="uniform-user_type">
	<span><?php echo __('select_label'); ?></span>
	<div id="taxicompany_list">
		<select name="company_id" id="company_id">
		<option value="">--Select--</option>
		<?php
		foreach($taxicompany_details as $company_list) {  ?>
		<option value="<?php echo $company_list['cid']; ?>" <?php if($field_type == $company_list['cid']) { echo 'selected=selected'; } ?> ><?php echo ucfirst($company_list["company_name"]); ?></option>
		<?php	} ?>
		</select>
	</div>	
		</div></div>
              <?php if(isset($errors) && array_key_exists('company_id',$errors)){ echo "<span class='error'>".ucfirst($errors['company_id'])."</span>"; }?>
        </td>      
	</tr>
           
        <tr>
           <td valign="top" width="20%"><label><?php echo __('Number of Login'); ?></label><span class="star">*</span></td>        
			<td>
		    <div class="new_input_field total_login">
			<?php $field_type =''; if(isset($postvalue) && array_key_exists('no_of_login',$postvalue)){ $field_type =  $postvalue['no_of_login']; } ?>
			<select name='no_of_login'>
			<option value="">--Total Login --</option>
			<?php for($i=1;$i<=5;$i++){ ?>
				<option value="<?php echo $i; ?>" <?php if($field_type == $i) { echo 'selected=selected'; } ?> ><?php echo $i; ?></option>
			<?php } ?>
			</select>
			</div>
			<?php if(isset($errors) && array_key_exists('no_of_login',$errors)){ echo "<span class='error'>".ucfirst($errors['no_of_login'])."</span>";}?>
       <span class="ref-text-info">If you select more than "1", then it will generate mobile number added with random numbers</span>
        <tr>
               
	   <td>
		   <div class="new_input_field">
			  <input type="hidden"  name="password" value="qwerty"   />
              <input type="hidden" title="" name="taxi_number" value=""   />
              <input type="hidden" title="" name="taxi_model" value="1"   />
              <input type="hidden" title="" name="company_name" value="demo"   />
              <!--<input type="hidden" title="" name="company_id" value="1"   />-->
              <input type="hidden" title="" name="booking_limit" value="55"   />
              <input type="hidden" title="" name="country" value="9"   />
              <input type="hidden" title="" name="state" value="5"   />
              <input type="hidden" title="" name="city" value="6"   />
              <input type="hidden" title="" name="longitude" value="<?php echo $longitude; ?>"   />
              <input type="hidden" title="" name="latitude" value="<?php echo $latitude; ?>"   />
              <input type="hidden" title="" name="gender" value=""   />
              <input type="hidden" title="" name="taxi_owner_name" value="NDOT"   />
              <input type="hidden" title="" name="dob" value="1990-05-05"   />
              <input type="hidden" title="" name="taxi_company" value="1"   />
              <input type="hidden" title="" name="exp_date" value="2018-05-05"   />
              <input type="hidden" title="" name="start_booking_date" value="<?php echo $start_booking_date; ?>"   />
              <input type="hidden" title="" name="end_booking_date" value="<?php echo $end_booking_date ?>"   />
              
            
		   </div>
           </td>   	
           </tr>        
             
 	<tr>
	<td width="20%" class="empt_cel">&nbsp;</td>
	<td colspan="" class="star">*<?php echo __('required_label'); ?></td>
	</tr>   
                    <tr>
						<td width="20%">&nbsp;</td>
                        <td colspan="">
                            <br/>
                    		<input type="hidden" name="type"  value="2" />
                            <div class="button blackB">     <input type="button" value="<?php echo __('button_back'); ?>" onclick="window.history.go(-1)" /></div>
                            <div class="button dredB">   <input type="reset" value="<?php echo __('button_reset'); ?>" title="<?php echo __('button_reset'); ?>" /></div>
                            <div class="button greenB">  <input type="submit" value="<?php echo __('submit' );?>" name="submit_addcompany" title="<?php echo __('submit' );?>" /></div>
                            <div class="clr">&nbsp;</div>
                        </td>
                    </tr> 

                </table>

        </form>
        
        
        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  
<?php }?>


<script type="text/javascript">
$(document).ready(function(){
 
$("#firstname").focus();	

	 jQuery("#addcompany_form").validate();

function nospaces(t){
  if(t.value.match(/\s/g)){
    alert("Do Not Allow Space");
    t.value=t.value.replace(/\s/g,'');
  }

}


	
</script>

