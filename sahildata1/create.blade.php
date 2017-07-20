@extends('layout.template')

@section('content')

    <h1>Create Buyer</h1>
	@if($errors->any())
		<div class="alert alert-danger">
			@foreach($errors->all() as $error)			
				<p>{{ $error }}</p>
			@endforeach
		</div>
	@endif
	
	@if(Session::has('msg'))
		<div class="alert alert-danger">
		{{Session::get('msg')}}
		</div>
	@endif
	
    {!! Form::open(['url' => 'buyers', 'method' => 'POST','id'=>'buyerFormSubmission','ng-controller'=>'buyerCreateFormController']) !!}
    <div class="form-group">
		{!! Form::label('Name', 'Name:') !!}
		{!! Form::text('name',null,['class'=>'form-control','ng-click'=>'addFunc()']) !!}
	</div>
	<div class="form-group">
        {!! Form::label('Email', 'Email:') !!}
		{!! Form::email('email',null,['class'=>'form-control']) !!}
	</div>
	<div class="form-group">
		{!! Form::label('Password', 'Password:') !!}
		{!! Form::password('password',['class'=>'form-control']) !!}
	</div>
	
	<div class="form-group">
		{!! Form::label('DEPARTMENT', 'DEPARTMENT:') !!}
	<div>		
	
		@foreach($departmentsList as $index=>$singleDepartment)
		<?php
	$unchecked=$singleDepartment["id"];
	?>
			<label class="checkbox-inline">{!! Form::checkbox('dept_id', $singleDepartment['id'], NULL, ['class' => 'dept_id','ng-click'=>"deptClick($unchecked)"]) !!}	{{  $singleDepartment['department_name'] }}</label>
			
		@endforeach
		</div>
	</div>
	


	<div class="form-group">
		{!! Form::label('SELLER', 'Add New Seller:') !!}
								<div ng-if="deptSellerList.length == 0">			
								No seller is available.		
								</div>			
								<div class="deptSellerArea" ng-if="deptSellerList.length > 0" ng-repeat="dept in deptSellerList">				
									<div>				
										<label><% dept.departmentName %> :</label>				
										<span ng-repeat="seller in dept.sellerList">				
										<label class='checkbox-inline'><input type='checkbox' name='seller_id[]' ng-value='dept.departmentId+" "+seller.id' ng-click='sellerClick(dept.departmentId , seller.id )'/> <% seller.name %></label>
										</span>				
									</div>			
								</div>				
	</div>
	
	<div class="form-group">		
		{!! Form::label('PRODUCT', 'Products:') !!}
		<div id="productIds">		
		<div ng-if="ProductList.length == 0">			
		No product is available.		
		</div>
			<div class="productDetails" ng-repeat="sellerProduct in ProductList">				
			<div  ng-if="ProductList.length > 0" >					
			<label><% sellerProduct.departmentName %> <% sellerProduct.sellerName %>: </label>					
			<label class='checkbox-inline' ng-if="sellerProduct.productNames.length>0" ng-repeat="product in sellerProduct.productNames">
			<% product %>
			</label>
			</div>			
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
  
  
 

/* 	$(".dept_id").change(function() {
		var selectedDept=[];
		jQuery(jQuery('input[name="dept_id"]:checked')).each(function(key,value){			
			selectedDept.push(this.value);
		});
		
		if(selectedDept == '')
		{
			selectedDept='0';
		} 
		// var selectedDept=jQuery('input[name="dept_id"]:checked');
		
		// var selectedDept=$("#dept_id").val();
		var baseUrl=$("#app_url").text();
		var url=baseUrl+'buyers/getseller';
		
		$.ajax({
			 headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
			url: url,
			
			data: {selectedDept:selectedDept},
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
					console.log(response.sellerData);
					$.each(response.deptData,function(deptKey,deptValue)
					{
						 $(".sellerDetails").append("<div id='sellerDept_"+deptKey+"'></div>");
						 $(".sellerDetails div#sellerDept_"+deptKey).append("<label for='SELLER'>"+deptValue.departmentName+" : &nbsp;</label>");
						 $.each(response.sellerData[deptKey], function (sellerKey, Sellervalue) {				
						 $(".sellerDetails div#sellerDept_"+deptKey+"").append("<label class='checkbox-inline'><input type='checkbox' class='seller_id' name='seller_id' value='"+deptValue.departmentId +' '+ Sellervalue.id +"'/> "+ Sellervalue.name +"</label>");
						 });

					});
				}

			}
		});

	});		*/

 
 
 
/* $("#sellerIds").on('click',".seller_id",function() {	
 		
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
			success: function(resp)
			{
				var response=JSON.parse(resp);
				$("#productIds .productDetails").empty();
				if(response.success == '0')
				{
					$("#productIds .productDetails").append("<div class='productDetails'>No product is available.</div>");
				}
				else
				{					
					$.each(response.sellerNames,function(sellerKey,sellerValue)
					{
						$(".productDetails").append("<div id='sellerProduct_"+sellerKey+"'></div>");
							$(".productDetails div#sellerProduct_"+sellerKey).append("<label for='PRODUCT'>"+sellerValue+" : </label>");
								
							
							$.each(response.productNames[sellerKey], function (productKey, productValue) {			
							 
							 
							 
							$(".productDetails div#sellerProduct_"+sellerKey).append("<label class='checkbox-inline'> "+ productValue.product_name +"</label>");

						});

					});
					
					
				

				
			}
		}

	});






 
	
	

});	*/


	
});

</script>
@stop
