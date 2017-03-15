@extends('layout.template')

@section('content')
<link href="{{ asset('css/customStyle.css') }}" rel="stylesheet">
    <h1>Update Buyer</h1>
	@if($errors->any())
		<div class="alert alert-danger">
			@foreach($errors->all() as $error)			
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif
	
	@if(Session::has('success'))
		<div class="alert alert-success">
		{{Session::get('success')}}
		</div>
	@endif	
	
	@if(Session::has('errorMessage'))
		<div class="alert alert-danger">
		{{Session::get('errorMessage')}}
		</div>
	@endif	
	
    {!! Form::open(['url' => 'buyers/updateBuyerData', 'method' => 'POST','id'=>'buyerFormSubmission','ng-controller'=>'updateBuyerController']) !!}
    <div class="form-group">
		{!! Form::label('Name', 'Name:') !!}
		{!! Form::text('name',$userDetails[0]['attributes']['name'],['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
        {!! Form::label('Email', 'Email:') !!}
		{!! Form::email('email',$userDetails[0]['attributes']['email'],['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
		{!! Form::label('Password', 'Password:') !!}		
		{!! Form::input('password', 'password',$userDetails[0]['attributes']['decrypt_password'],['class'=>'form-control']) !!}
	</div>
	
		{!! Form::hidden('buyerId',$buyerId,['id'=>'buyerId']) !!}
		<?php
		if($countStatus == 1):
			?>
		<table class="table table-striped table-bordered table-hover">
	<tr>
		<thead>
			<th>#</th>
			<th>Departments</th>
			<th>Sellers</th>
		</thead>
	</tr>
	
	@foreach($sellerDetail as $deptPos=>$sellerData)
	<?php
	
	$counter=$deptPos+1; 
	$departmentRowId=$buyerId.''.$depts[$deptPos]->departmentId; ?>
		
		

		 <tr id="departmentRowId_{{ $departmentRowId }}"><td>{{ $counter }}</td><td>{{ $depts[$deptPos]->departmentName }}</td>
			<td>
			
			@foreach($sellerData as $sellerPos=>$sellerData2)
			<?php $sellerCellId=$buyerId.''.$sellerDetail[$deptPos][$sellerPos]['departmentIdBuyerTb'].''.$sellerDetail[$deptPos][$sellerPos]['sellerIdBuyerTb']; ?>
				
			<div class="sellerTotal" id="sellerCell_{{ $sellerCellId }}"><span id="sellerStyle">{{ $sellerDetail[$deptPos][$sellerPos]['sellerName'] }}</span>
			<a href="javascript:void(0)" onclick="deleteSeller({{ $buyerId }},{{ $sellerDetail[$deptPos][$sellerPos]['departmentIdBuyerTb'] }},{{ $sellerDetail[$deptPos][$sellerPos]['sellerIdBuyerTb'] }},{{ $sellerCellId }},{{ $departmentRowId }})" class="btn btn-danger deleteButton">X</a>
			
			</div>
			@endforeach
			
			

		</td>
		</tr>
		
	@endforeach
		
	
	
	</table>
		@endif
		
	<div class="form-group">	
		{!! Form::label('DEPARTMENT', 'Add New Department::') !!}
		<div>
		@foreach($departmentsList as $singleDepartment)
		<?php
		$checkedDept=$singleDepartment["id"];
		?>
			<label class="checkbox-inline">{!! Form::checkbox('dept_id', $singleDepartment['id'], NULL, ['class' => 'dept_id','ng-click'=>"updateDepartmentClick($checkedDept)" ]) !!}	{{  $singleDepartment['department_name'] }}</label>
			
		@endforeach
		</div>
	</div>
		
		
		
	
	<div class="form-group">	
		{!! Form::label('SELLER', 'Add New Seller:') !!}
		<div id="sellerIds">
			<div class="sellerDetails" ng-if="deptSellersData.length == 0">No seller is available.</div>
			<div class="sellerDetails" ng-if="deptSellersData.length > 0" ng-repeat="singleDeptSeller in deptSellersData">
				<label><% singleDeptSeller.departmentName %></label>
				<span ng-if="singleDeptSeller.sellerDetails.length > 0" ng-repeat="singleSellerDetail in singleDeptSeller.sellerDetails">
				<label class="checkbox-inline">{!! Form::checkbox('seller_id[]','1',NULL,['class'=>'seller_id','ng-click'=>"updateSellerClick(singleDeptSeller.departmentId,singleSellerDetail.sellerId)", 'ng-checked'=>"singleSellerDetail.status"])  !!} <% singleSellerDetail.sellerName %></label>
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">		
		{!! Form::label('PRODUCT', 'Products:') !!}
		<div id="productIds">
			<div class="productDetails" ng-if="productsList.length == 0" >No product is available.</div>
			<div class="productDetails" ng-if="productsList.length > 0" ng-repeat="singleProductList in productsList">
				<label><% singleProductList.departmentName %> </label><label><% singleProductList.sellerName %></label>
				<span ng-if="singleProductList.productNames.length > 0" ng-repeat="productName in singleProductList.productNames">
					<% productName %>
				</span>
			</div>
		</div>
	</div>
    
    <div id="app_url" style="display:none"><?php echo asset('/');?></div>
	 <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="form-group">
        {!! Form::submit('Save', ['class' => 'btn btn-primary form-control','id'=>'buyerSubmit']) !!}
    </div>
    {!! Form::close() !!}
	<script>
$( 'document' ).ready(function() {
  
  // remove selected index of department by clicking on submit
  $("#buyerFormSubmission").on('submit',function(){
	   $(".dept_id").attr('name','dept_id[]');
	   $(".seller_id").attr('name','seller_id[]');
  });
  
  
  
/*	$( ".dept_id" ).change(function() {
		var selectedDept=[];
		jQuery(jQuery('input[name="dept_id"]:checked')).each(function(key,value){			
			selectedDept.push(this.value);
		});
		
		if(selectedDept == '')
		{
			selectedDept='0';
		} 
		
		var buyerId=jQuery("#buyerId").val();
		var baseUrl=$("#app_url").text();
		var url=baseUrl+'buyers/getsellerForBuyer';
		
		$.ajax({
			 headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
			url: url,			
			data: {selectedDept:selectedDept, buyerId:buyerId},
			type: 'POST',
			datatype: 'JSON',
			
			success: function (resp)
			{

				var response=JSON.parse(resp);
				$("#sellerIds .sellerDetails").empty();
				$("#productIds .productDetails").empty();
				$("#productIds .productDetails").append("<div class='productDetails'>No product is available.</div>");
				if(response.success == 0)
				{
					
					$("#sellerIds .sellerDetails").append("No seller is available.");
				}
				else
				{
					
					$.each(response.deptData,function(deptKey,deptValue)
					{
						$(".sellerDetails").append("<div id='sellerDept_"+deptKey+"'></div>");
						$(".sellerDetails div#sellerDept_"+deptKey).append("<label for='SELLER'>"+deptValue.departmentName+" : &nbsp;</label>");
						
						
						 $.each(response.sellerData[deptKey], function (sellerKey, Sellervalue) {	
						
						 if(Sellervalue.status == 1)
						 {

							$(".sellerDetails div#sellerDept_"+deptKey).append("<label class='checkbox-inline'><input type='checkbox' class='seller_id' name='seller_id' value='"+deptValue.departmentId +' '+ Sellervalue.id +"' checked='checked' disabled /> "+ Sellervalue.name +"</label>");
						 }
						 else
						 {

							$(".sellerDetails div#sellerDept_"+deptKey+"").append("<label class='checkbox-inline'><input type='checkbox' class='seller_id' name='seller_id' value='"+deptValue.departmentId +' '+ Sellervalue.id +"'/> "+ Sellervalue.name +"</label>");
							
												 
						 }
						 
						 
						}); 

						 
					});
					
				}
				
				
				
	
			}
		});

	}); 	*/
	


/*	$("#sellerIds").on('click',".seller_id",function() {
		var selectedDept=[];
		
		jQuery(jQuery('input[name="seller_id"]:checked')).each(function(key,value){									
			selectedDept.push(this.value);
			
		});
		
		if(selectedDept == '')
		{
			selectedDept='0';
		} 
	
		var baseUrl=$("#app_url").text();
		var url=baseUrl+'buyers/getproduct';
	
		$.ajax({
			 headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
			url: url,
			
			data: {selectedSellers:selectedDept},
			type: 'POST',
			datatype: 'JSON',
			
			success: function (resp)
			{
				// $("#productIds").empty();
				// $('#product_id').append('<option value="">Select Product</option>');
				var response=JSON.parse(resp);
				$("#productIds .productDetails").empty();
				if(response.success == '0')
				{
					
					$("#productIds .productDetails").append("<div class='productDetails'>No product is available.</div>");
				}
				else
				{
					console.log(response);
					console.log(response.sellerNames);
					// alert(response.productNames.length);
					// console.log(response.productNames.length);
					
					if(response.productNames.length > 0)
					{
						// $("#productIds").append("<div class='productDetails'></div>");
						
						$.each(response.sellerNames,function(sellerKey,sellerValue)
						{
							$(".productDetails").append("<div id='sellerProduct_"+sellerKey+"'></div>");							
							// alert(response.sellerNames[sellerKey]);
							if(sellerValue != 0)
							{
								// $('#product_id').append('<optgroup label="'+sellerValue+'">');
								$(".productDetails div#sellerProduct_"+sellerKey).append("<label for='PRODUCT'>"+sellerValue+" : </label>");
								
								$.each(response.productNames[sellerKey], function (productKey, productValue) {			
								 // $('#product_id').append('<option value='+ productValue.id +'>'+ productValue.product_name +'</option>');
								 
								 $(".productDetails div#sellerProduct_"+sellerKey).append("<label class='checkbox-inline'> "+ productValue.product_name +"</label>");
								 });
							 // $('#product_id').append('</optgroup>');
							//console.log(resp);
							}
							
						});
					}
				}
				
				

				
			}
		});

	}); 	*/
	
	
});

// function to delete seller department on behalf of buyer id.
function deleteSeller(buyerId, departmentId, sellerId, sellerCellId,departmentRowId)
{	
	var buyerId=buyerId;
	var departmentId=departmentId;
	var sellerId=sellerId;
	var baseUrl=$("#app_url").text();
	var url=baseUrl+'buyers/deleteSeller';
	$.ajax({
			 headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
			url: url,
			
			data: {buyerId:buyerId, departmentId:departmentId, sellerId:sellerId},
			type: 'POST',
			datatype: 'JSON',			
			success: function (resp)
			{				
				var response=JSON.parse(resp);
				
				if(response.success == 1)
				{
					jQuery("#sellerCell_"+sellerCellId).remove();
					jQuery(".dept_id").attr('checked',false);
					$("#sellerIds .sellerDetails").empty();
					$("#productIds .productDetails").empty();					
					$("#sellerIds .sellerDetails").append("No seller is available.");
					$("#productIds .productDetails").append("No product is available.");
					
					if(jQuery("#departmentRowId_"+departmentRowId+" .sellerTotal").length == 0)
					{
						jQuery("#departmentRowId_"+departmentRowId).remove();
					}					
				}
				else
				{
					alert('something goes wrong.');
				}
				return false;
			}
		});
	
	return false;
}

</script>
@stop
