// JavaScript Document


var SrcPath = $('#baseurl').val();
//    alert(SrcPath);

/******** Get company Taxi *********/
function getcompanytaxi(company_id)
{

		  $.ajax({
			url:SrcPath+"transaction/gettaxilist",
			type:"post",
			data:"company_id="+company_id,
			success:function(data){

			$('#taxi_list').html();
			$('#taxi_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}
/*********** Get Company Drivers *******/
function getcompanydriver(company_id)
{

		  $.ajax({
			url:SrcPath+"transaction/getdriverlist",
			type:"post",
			data:"company_id="+company_id,
			success:function(data){

			$('#driver_list').html();
			$('#driver_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

/*********** Get Company Drivers *******/
function getcompanypassengers(company_id)
{

		  $.ajax({
			url:SrcPath+"transaction/getpassengerlist",
			type:"post",
			data:"company_id="+company_id,
			success:function(data){

			$('#passenger_list').html();
			$('#passenger_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

/*********** Get Company Drivers *******/
function getcompanymanager(company_id)
{

		  $.ajax({
			url:SrcPath+"transaction/getmanagerlist",
			type:"post",
			data:"company_id="+company_id,
			success:function(data){
			$('#manager_list').html();
			$('#manager_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}


/******** Get Manager Taxi *********/
function getmanagertaxi(manager_id)
{
	var company_id = $('#filter_company').val();
	var manager_id = $('#manager_id').val();

		  $.ajax({
			url:SrcPath+"transaction/getmanager_taxilist",
			type:"post",
			data:"company_id="+company_id+"&manager_id="+manager_id,
			success:function(data){
			$('#taxi_list').html();
			$('#taxi_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}


/*********** Get Company Drivers *******/
function getmanagerdriver(manager_id)
{
		var company_id = $('#filter_company').val();
		var manager_id = $('#manager_id').val();
		
		$.ajax({
			url:SrcPath+"transaction/getmanager_driverlist",
			type:"post",
			data:"company_id="+company_id+"&manager_id="+manager_id,
			success:function(data){

			$('#driver_list').html();
			$('#driver_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}


/******** Get Manager Taxi *********/
function managertaxi(manager_id)
{
	
		  $.ajax({
			url:SrcPath+"transaction/getmanager_taxilist",
			type:"post",
			data:"company_id=&manager_id="+manager_id,
			success:function(data){
			$('#taxi_list').html();
			$('#taxi_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}


/*********** Get Company Drivers *******/
function managerdriver(manager_id)
{
		
		$.ajax({
			url:SrcPath+"transaction/getmanager_driverlist",
			type:"post",
			data:"company_id=&manager_id="+manager_id,
			success:function(data){

			$('#driver_list').html();
			$('#driver_list').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}
