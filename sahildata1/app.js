var myApp=angular.module('kennedyApp',[],function($interpolateProvider) 
{        
	$interpolateProvider.startSymbol('<%');        
	$interpolateProvider.endSymbol('%>');    
});

// controller for creating buyer	 
	myApp.controller('buyerCreateFormController',function($scope,$http){			
	$scope.sellerAvailable=false;		
	var selectedSellers=[];	
	$scope.deptsAvailable=[];			
	$scope.ProductList = [];		
	$scope.deptSellerList=[];		
	$scope.deletedSellers=[];		

	$scope.deptClick=function(checkedDept){						
		$scope.selectedDept=[];
		var deptFlag=0;		
		// note: if deptFlag = 0 that means dept is selected. if 1 that means dept is unselected.		
		var sellerDetailsDiv=angular.element(document.querySelector("#sellerIds .sellerDetails"));						
		if($scope.deptSellerList.length > 0)		{
		// to remove sellers
		angular.forEach($scope.deptSellerList,function(val){				 
			if(val.departmentId == checkedDept)				 
			{					 
				deptFlag=1;					 
				$scope.deptSellerList.splice($scope.deptSellerList.indexOf(val),1);					
				var m=$scope.deptSellerList;
				
			}			 
		 });
		 // to remove the products.
		angular.forEach($scope.ProductList,function(products){			
			if(products.departmentId == checkedDept)
			{
				deptFlag=1;
				$scope.ProductList.splice($scope.ProductList.indexOf(products),1);
			}
		});
		 if(deptFlag == 0)			
		{				
			$scope.selectedDept.push(checkedDept);						
		}		
		}		
		else		
		{			
			$scope.selectedDept.push(checkedDept);					
		}		
		if(deptFlag == 0)		
		{			
			var url="http://"+location.host+"/buyers/getseller";			
			var data={selectedDept:$scope.selectedDept};					
			$http.post(url,data).success(function(result){					
			$scope.output=result;								
			if($scope.output.success == 0){									
			$scope.deptSellerList=[];					
			//actualUncheckedDept	
										
		}				
		else				
		{					
			// to check wheather unclicked department checkbox value exist or not															
			var ddd=$scope;					
			angular.forEach($scope.output.deptData,function(data){						
				$scope.deptSellerList.push(data);					
			});									
		}											
		});			
		}				
		return false;					
	}				
	$scope.sellerClick=function(deptValue,sellerValue){	
		// alert(deptValue+' '+sellerValue);			
		var sellerDetailsDiv=angular.element(document.querySelector("#productIds .productDetails"));		
		var sellerId=sellerValue;		var selectedSellers=[];		var flag=0;		
		// to check in selectedSellers array wheather deptId and sellerId match or not, if already exist then that array position delete.
						
			if($scope.ProductList.length > 0)		
			{				
				angular.forEach($scope.ProductList,function(productValues)
				{				
				if(productValues.departmentId == deptValue && productValues.sellerId == sellerValue)				
				{					
					flag=1;
					$scope.ProductList.splice($scope.ProductList.indexOf(productValues),1);
					}				
					if(flag == 0)				
					{					
						selectedSellers.push({deptId : deptValue, sellerId : sellerValue});				
					}			
				});		
			}		
			else		
			{			
		selectedSellers.push({deptId : deptValue, sellerId : sellerValue});		
		}						
		console.log(selectedSellers);		
		if(flag == 0)		
		{			
			var url="http://"+location.host+"/buyers/getproduct";					
			var data={selectedSellers:selectedSellers};			
			$http.post(url,data).success(function(output){							
				response=output;							
				if(response.success == '0')				
				{
					
				}				
				else				
				{					
					$scope.ProductList.push(response.productNames[0]);			
				}			
			});		
		} 		
		return false;
	}				
});


///////////////////////////////////////////////////////////////////////////
myApp.controller('updateBuyerController',function($scope,$http){
	var selectedDept='';
	$scope.deptSellersData=[];
	$scope.productsList=[];
	
	$scope.updateDepartmentClick=function(departmentValue){
		var tempProductArray=[];
		var uri_url=location.pathname.split("/");
		var buyerId=uri_url[uri_url.length-1];		
		var deptFlag=0;
		// check department id exist or not
		if($scope.deptSellersData.length > 0)
		{
			angular.forEach($scope.deptSellersData,function(deptValue){
				if(deptValue.departmentId == departmentValue)
				{					
					deptFlag=1;
					$scope.deptSellersData.splice($scope.deptSellersData.indexOf(deptValue));
				}
			});
		}
		
		if($scope.productsList.length > 0)
		{
			angular.forEach($scope.productsList,function(checkDepartmentId){
				if(checkDepartmentId.departmentId == departmentValue)
				{
					
				}
				else
				{
					tempProductArray.push(checkDepartmentId);
				}
			});
			$scope.productsList=tempProductArray;
		}
		
		
		
		if(deptFlag == 0)
		{
			var url="http://"+location.host+"/buyers/getsellerForBuyer";
			var data={selectedDept:departmentValue,buyerId:buyerId};
			$http.post(url,data).success(function(output){
				if(output.success == '1')
				{
					$scope.deptSellersData.push(output.sellerData);
					angular.forEach($scope.deptSellersData,function(deptData){
						angular.forEach(deptData,function(allData){
							if(sellerData.status == '1')
							{
								// $scope.productsList.push();
								
							}
						});
					});
				}
				
				var p=$scope.deptSellersData;
			});
		}
		
	}
	
	
	
	
	
	
	
	$scope.updateSellerClick=function(departmentId,sellerId){
		var productFlag=0;
		if($scope.productsList.length > 0)
		{
			angular.forEach($scope.productsList,function(productCategory){
				if(productCategory.departmentId == departmentId && productCategory.sellerId == sellerId)
				{
					productFlag=1;
					$scope.productsList.splice($scope.productsList.indexOf(productCategory));
				}
			});
			var t=$scope.productsList;
		}
		if(productFlag == 0)
		{
			var url="http://"+location.host+"/buyers/getproduct";				
			var data={departmentId:departmentId,sellerId:sellerId};
			$http.post(url,data).success(function(output){
				if(output.success == '1')
				{
					$scope.productsList.push(output.productNames);
				}
				var p=$scope.productsList;
			});			
		}
	}	
});