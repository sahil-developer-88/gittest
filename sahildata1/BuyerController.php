<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\Buyer;
use App\Product;
use App\Seller;
use App\Department;
use App\Http\Requests;
use DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator;

use Illuminate\Http\Request;
use Mail;
// use Request;

class BuyerController extends Controller
{
    //
/*	protected $fillable=['name',
			'email',
			'password',
			'dept_id',
			'seller_id'	
	]; */
	 public function __construct()
	 {
	   $this->departmentList = ['' => 'Select Department'] + Department::lists('department_name', 'id')->all();
	   $this->departmentsList=Department::all();
	   $this->SellerList = ['' => 'No Seller'];
	   $this->productList = ['' => 'No Product'];

		
		$this->middleware('auth');
/*		
		$action = app('request')->route()->getAction();
 
        $controller = class_basename($action['controller']);
		
*/
		
	}
	public function index()
	{
        $buyers=Buyer::groupBy('user_id')->get();
		foreach($buyers as $buyerPos=>$singleBuyer)
		{			
			// get username corresponds to user id
			$userDetails=User::where('id','=',$singleBuyer->user_id);
			
			$singleBuyer->buyerId=$singleBuyer->user_id;
			$singleBuyer->mainId=$singleBuyer->id;
			$userName=$userDetails->get();
			if(count($userName)>0)
			{
				$singleBuyer->user_id=$userName[0]['attributes']['name'];
			}
			
			// get department name corresponds to department id
			$deptDetails=Department::where('id','=',$singleBuyer->dept_id);
			$singleBuyer->departmentId=$singleBuyer->dept_id;
			$deptName=$deptDetails->get();
			if(count($deptName)>0)
			{
				$singleBuyer->dept_id=$deptName[0]['attributes']['department_name'];
			}
			
			 
			// get seller name corresponds to seller id
			$sellerDetails=Seller::where('id','=',$singleBuyer->seller_id);
			$sellerName=$sellerDetails->get();
			if(count($sellerName)>0)
			{
				$singleBuyer->seller_id=$sellerName[0]['attributes']['name'];
			} 
			
			// get product name corresponds to product id
			$productDetails=Product::where('id','=',$singleBuyer->product_id);
			$productName=$productDetails->get();
			if(count($productName)>0)
			{
				$singleBuyer->product_id=$productName[0]['attributes']['product_name'];
			}						
			

		
		}

											
		
        return view('buyers.index',compact('buyers'));
    }
	
	// ajax action to get seller names corresponds to department names
	public function getseller(Request $request)
	{		
		// $selectedDept=$_POST['selectedDept'];	
		$selectedDept=$request->input('selectedDept');		
		
		if($selectedDept == 0)
		{
			echo json_encode(array('success'=>'0'));
			exit;
		}
		else
		{
			foreach($selectedDept as $deptPos=>$singleDept)
			{
				// join query to get seller details on behalf of department id.
				$getSellers=DB::table('departments')
				->join('departmentSellerRelation','departmentSellerRelation.departmentId','=','departments.id')
				->join('sellers','sellers.id','=','departmentSellerRelation.sellerId')
				->select('sellers.id as sellerId','sellers.name as sellerName')		
				->where ('departments.id','=',$singleDept)
				->get();
				
				
				if(count($getSellers) >0)
				{
					// $success="1";
					$deptData=Department::where('id',$singleDept)->get();
					
					 $deptArr[$deptPos]['departmentName']=$deptData[0]['attributes']['department_name'];
					 $deptArr[$deptPos]['departmentId']=$deptData[0]['attributes']['id'];
					
					foreach($getSellers as $pos=>$singleSeller)
					{
						$success='1';
						/* $sellerArr[$deptPos][$pos]['id']=$singleSeller->sellerId;
						$sellerArr[$deptPos][$pos]['name']=$singleSeller->sellerName; */						$deptArr[$deptPos]['sellerList'][$pos]['id']=$singleSeller->sellerId;						$deptArr[$deptPos]['sellerList'][$pos]['name']=$singleSeller->sellerName;																								//$deptArr[$deptPos]['departmentName']=$deptData[0]['attributes']['department_name'];						//$deptArr[$deptPos]['departmentId']=$deptData[0]['attributes']['id'];
					}						
				}
				

				else	
				{
					$sellerArr=array();
				}
			}
		}
			
		
			
		
		
		echo json_encode(array('success'=>'1','deptData'=>$deptArr));
		exit;
		
	}



		// ajax action to get seller names corresponds to department names
	public function getsellerForBuyer(Request $request)
	{		
		$singleDept=$request->input('selectedDept');		
		

		$buyerId=$request->input('buyerId');
		if($singleDept == 0)
		{
			echo json_encode(array('success'=>'0'));
			exit;

		}
		else
		{
/*			foreach($selectedDept as $deptPos=>$singleDept)
			{	*/


				// join query to get seller details on behalf of department id.
				$getSellers=DB::table('departments')
				->join('departmentSellerRelation','departmentSellerRelation.departmentId','=','departments.id')
				->join('sellers','sellers.id','=','departmentSellerRelation.sellerId')
				->select('sellers.id as sellerId','sellers.name as sellerName')		
				->where ('departments.id','=',$singleDept)
				->get();
				


			
				if(count($getSellers) >0)
				{
					// $success="1";
					$deptData=Department::where('id',$singleDept)->get();
					
					$sellerArr['departmentName']=$deptData[0]['attributes']['department_name'];
					$sellerArr['departmentId']=$deptData[0]['attributes']['id'];
					
					foreach($getSellers as $pos=>$singleSeller)
					{
						$checkCurrentBuyer=Buyer::where('user_id','=',$buyerId)
								->where('dept_id','=',$sellerArr['departmentId'])
								->where('seller_id','=',$singleSeller->sellerId)
								->get();
						if(count($checkCurrentBuyer) >0)		
						{
							$sellerArr['sellerDetails'][$pos]['status']=1;
							
							
							
							$productNames = Product::where('seller_id', $singleSeller->sellerId);
							$products=$productNames->get();
							if(count($products)>0)
							{
								foreach($products as $productPos=>$product)
								{
									$sellerArr['productNames'][$productPos]=$product['product_name'];
								}									
							}
							else
							{
								$sellerArr['productNames']=array();
							}
							
							
							
							
						}
						else
						{
							$sellerArr['sellerDetails'][$pos]['status']=0;
							$sellerArr=array();
						}
						$success='1';
						$sellerArr['sellerDetails'][$pos]['sellerId']=$singleSeller->sellerId;
						$sellerArr['sellerDetails'][$pos]['sellerName']=$singleSeller->sellerName;
					}						
				}	
				

				else	
				{
					$sellerArr=array();
				}	
//			}	
			
			
			echo json_encode(array('success'=>'1','sellerData'=>$sellerArr));
			exit;
		}

		
	}

	
	// ajax action to get product names corresponds to seller names
	public function getproduct(Request $request)
	{		 
		 // $selectedSellers=$_POST['selectedSellers'];		 
		 $departmentId=$request->input('departmentId');
		 $singleSellerId=$request->input('sellerId');
		 
		 
		 if($departmentId == 0)
		{
			echo json_encode(array('success'=>'0'));
			exit;
		}
		else
		{
			  /* foreach($selectedSellers as $sellerPos=>$singleSeller)
			 { 	*/			
				
				// $singleSeller=explode(".",$singleSeller)[1];
//				$singleSellerId=$singleSeller['sellerId'];				
//				$departmentId=$singleSeller['deptId'];				 
				$departmentDetail=Department::where('id','=',$departmentId)->get();				
				$departmentName=$departmentDetail[0]['department_name'];
				$results = Product::where('seller_id', $singleSellerId);
				$products=$results->get();
				
				$sellerDetails=Seller::where('id',$singleSellerId);
				$getSellerDetails=$sellerDetails->get();
				if(count($products)>0)
				{
					$productArr['sellerId']=$getSellerDetails[0]['id'];					
					$productArr['sellerName']=$getSellerDetails[0]['name'];										
					$productArr['departmentId']=$departmentId;					
					$productArr['departmentName']=$departmentName;										
					foreach($products as $productPos=>$product)
					{					
						$productArr['productNames'][$productPos]=$product['product_name'];						
					}	
				}	
				else	
				{
					$productPos=$productPos+1;
					$productArr[$sellerPos][$productPos]['id']='0';
					$productArr[$sellerPos][$productPos]['product_name']='0';
					$sellerNames[$sellerPos]=$getSellerDetails[0]['attributes']['name'];
				}				
// 			 }
			echo json_encode(array('success'=>'1','productNames'=>$productArr));
			exit;
		}

		
	}

	// buyer creation action
	public function create(Request $request)
	{
			$departmentList = $this->departmentList;
			$departmentsList = $this->departmentsList;
			$sellerList = $this->SellerList;
			$productList = $this->productList;			
			return view('buyers.create',compact('departmentList','sellerList','productList','departmentsList'));	
	}

	
	// ajax action to get products corresponds buyer
	public function unarchieve(Request $request)
	{
		$buyerId=$request->input('buyerId');
		$getProducts=Buyer::where('user_id','=',$buyerId)
							->where('archieve_status','=',1)
							->get();
		if(count($getProducts) > 0)
		{
			foreach($getProducts as $pos=>$singleProduct)
			{
				$getProductName=Product::where('id','=',$singleProduct['attributes']['product_id'])->get();
				
				$result['productId'][$pos]=$singleProduct['attributes']['product_id'];
				$result['productName'][$pos]=$getProductName[0]['attributes']['product_name'];
				$result['departmentId'][$pos]=$singleProduct['attributes']['dept_id'];
			}
			//$result['success']=print_r($getProducts,true);
			$result['success']='1';
			$result['count']=count($getProducts);
			
		}
		else
		{
			$result['success']='0';
			$result['count']='0';
			$result['productId']='0';
			$result['productName']='0';
			$result['departmentId']='0';
		}
		echo json_encode($result);
		
		exit;
	}

	// action to convert products into unarchieve mode
	public function unarchieveOperation(Request $request)
	{
		$selectedProduct=$request->input('selectedProduct');
		
		foreach($selectedProduct as $singleProduct)
		{
			$checkBuyer=DB::table('buyers')->where('user_id',explode(" ",$singleProduct)[1])
											->where('dept_id',explode(" ",$singleProduct)[0])											
											->where('product_id',explode(" ",$singleProduct)[2])
											->update(array('archieve_status'=>0));			
						
			
		}
		if($checkBuyer > 0)
		{
			session()->flash('success','Selected products are archieved now.');
		}
		return redirect('buyers');
		
	}

	public function deleteBuyer(Request $request)
	{
		$buyerId=$request->input('buyerId');
		
		// delete buyer details from buyer table
		$checkDeleteBuyerTb=DB::table('buyers')->where('user_id',$buyerId)->delete();
		// delete buyer details from user table
		$checkDeleteUserTb=DB::table('users')->where('id',$buyerId)->delete();
		// delete token details from usersToken table
		$checkDeleteUsersTokenTb=DB::table('usersTokens')->where('userId',$buyerId)->delete();
		session()->flash('success','Buyer successfully deleted.');
/*		if($checkDeleteBuyerTb > 0 && $checkDeleteUserTb > 0 && $checkDeleteUsersTokenTb > 0)
		{
			session()->flash('success','Buyer successfully deleted.');
		}
		else
		{
			session()->flash('errorMsg','Buyer deletion error.');
		}	*/
		return redirect('buyers');
	}
	
	/*
	public function updateBuyer(Request $request)
	{		
		$buyerId=$request->input('buyerMainId');
		$departmentList = $this->departmentList;
		$sellerList = $this->SellerList;
		$productList = $this->productList;
		
				// join query to get department details on behalf of departments and departmentSellerRelation tables and distinct departments.
				
		$depts=DB::table('buyers')												
											->join('departments','departments.id','=','buyers.dept_id')
											->select('departments.id as departmentId','departments.department_name as departmentName')
											->groupBy('buyers.dept_id')
											->where('user_id','=',$buyerId)
											->get();
		
		foreach($depts as $deptPos=>$singleDept)
		{
			$sellerDetails=DB::table('buyers')
		->join('sellers','sellers.id','=','buyers.seller_id')
		->select('sellers.name as sellerName','sellers.id as sellerId')
		->groupBy('buyers.seller_id')
		->where('buyers.dept_id','=',$singleDept->departmentId)
		->where('user_id','=',$buyerId)
		->get();
			
			foreach($sellerDetails as $sellerPos=>$singleSellerDetail)
			{
				$sellerDetail[$deptPos][$sellerPos]['sellerId']=$singleSellerDetail->sellerId;
				$sellerDetail[$deptPos][$sellerPos]['sellerName']=$singleSellerDetail->sellerName;				
			} 
		}	
		
		$userDetails=User::where('id','=',$buyerId)->get();
		
		return view('buyers.updateBuyer',compact('departmentList','sellerList','productList','depts','sellerDetail','userDetails','buyerId'));
	} */
	
	public function updateBuyer($id)
	{		
		$buyerId=$id;
		$departmentList = $this->departmentList;		
		$departmentsList=$this->departmentsList;
		$sellerList = $this->SellerList;
		$productList = $this->productList;
		$countStatus=0;
		
		// join query to get department details on behalf of departments and departmentSellerRelation tables and distinct departments.
				
		$depts=DB::table('buyers')												
											->join('departments','departments.id','=','buyers.dept_id')
											->select('departments.id as departmentId','departments.department_name as departmentName','buyers.dept_id as departmentIdBuyerTb')
											->groupBy('buyers.dept_id')
											->where('user_id','=',$buyerId)
											->get();
		
		foreach($depts as $deptPos=>$singleDept)
		{
			$sellerDetails=DB::table('buyers')
		->join('sellers','sellers.id','=','buyers.seller_id')
		->select('sellers.name as sellerName','sellers.id as sellerId','buyers.seller_id as sellerIdBuyerTb')
		->groupBy('buyers.seller_id')
		->where('buyers.dept_id','=',$singleDept->departmentId)
		->where('user_id','=',$buyerId)
		->get();
			
			foreach($sellerDetails as $sellerPos=>$singleSellerDetail)
			{
				$sellerDetail[$deptPos][$sellerPos]['sellerId']=$singleSellerDetail->sellerId;
				$sellerDetail[$deptPos][$sellerPos]['sellerName']=$singleSellerDetail->sellerName;
				$sellerDetail[$deptPos][$sellerPos]['sellerIdBuyerTb']=$singleSellerDetail->sellerIdBuyerTb;  // seller id from buyer table
				$sellerDetail[$deptPos][$sellerPos]['departmentIdBuyerTb']=$singleDept->departmentIdBuyerTb;  // department id from buyer table
				$countStatus=1;
			} 
		}	
		
		$userDetails=User::where('id','=',$buyerId)->get();
		
		
		return view('buyers.updateBuyer',compact('departmentList','sellerList','productList','depts','sellerDetail','userDetails','buyerId','countStatus','departmentsList'));
		
	}
	public function updateBuyerData(Request $request)
	{
		
		$this->validate($request,[
			'name'=>'required',
			'email'=>'required | email',
			'password'=>'required | min:6',
			'dept_id'=>'required',
			'seller_id'=>'required'			
		]);
		

		
		$email=trim($request->input('email'));
		$password=trim($request->input('password'));
		$name=trim($request->input('name'));
		$sellerIds=$request->input('seller_id');
		$buyerId=$request->input('buyerId');
		// check email verification
		$getEmail=User::where('email','=',$email)
								->where('id','!=',$buyerId);
		$checkEmail=$getEmail->get();
		if(count($checkEmail) > 0)
		{
			session()->flash('errorMessage','Email already exists.');
			return redirect()->back();
		}
		
		$hashpassword=Hash::make($password);
		$updateUser=DB::table('users')->where('id',$buyerId)
											->update(array('name'=>$name,'email'=>$email,'password'=>$hashpassword,'decrypt_password'=>$password,'remember_token'=>$request->input('_token')));
		


/*		$user = new User;
		$user->name = $name;
		$user->email = $email;
		$user->password = $hashpassword;
		$user->decrypt_password = $password;
		$user->remember_token = $request->input('_token');
		$user->role = 1;	*/
		if($updateUser == 1)
		{
			$userid=$buyerId;
			foreach($sellerIds as $singleSellerId)
			{
				$individualId=explode(" ",$singleSellerId);
				$singleSellerId=$individualId[1];
				$departmentId=$individualId[0];
				// join query to get products, and their corresponding department ids for the selected sellers.
				$getData=DB::table('sellers')
				->join('products','products.seller_id','=','sellers.id')
				->select('products.id as productId','sellers.id as sellerId','sellers.department_id')		
				->where ('sellers.id','=',$singleSellerId)
				->get(); 
				foreach($getData as $pos=>$singleData)
				{
					$product_id=$singleData->productId;
					$seller_id=$singleData->sellerId;
					$department_id=$departmentId;
					
					
					$checkBuyerData=Buyer::where('user_id','=',$userid)								
								->where('dept_id','=',$department_id)
								->where('seller_id','=',$seller_id)
								->get();
					if(count($checkBuyerData) > 0)
					{
						// no insertion because this seller already exists.
					}
					else
					{
						$insertProductDetails=new Buyer;
						$insertProductDetails->user_id=$userid;
						$insertProductDetails->dept_id=$department_id;
						$insertProductDetails->seller_id=$seller_id;
						$insertProductDetails->product_id=$product_id;
						$insertProductDetails->updated_at=date('Y-m-d h:i:s');
						$insertProductDetails->save();
					}
					
				}
			}
			session()->flash('success','buyer data successfully saved.');
			return redirect('buyers');
		}
		else
		{
			session()->flash('errorMessage','Updation failure error.');
			return redirect()->back();
		}
		
		return redirect('buyers');
	}
	
	// ajax action to delete seller and department on behalf of buyer id.
	public function deleteSeller(Request $request)
	{
		$buyerId=$request->input('buyerId');
		$departmentId=$request->input('departmentId');
		$sellerId=$request->input('sellerId');
		
		$deleteSeller=DB::table('buyers')->where('user_id',$buyerId)
					->where('dept_id',$departmentId)
					->where('seller_id',$sellerId)
					->delete();
					
		if($deleteSeller > 0)
		{
			$result['success']='1';
		}
		else
		{
			$result['success']='0';
		}
		return json_encode($result);
	}
	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	 // by default action to save the buyer details.
	public function store(Request $request)
	{

		$this->validate($request,[
			'name'=>'required',
			'email'=>'required | email',
			'password'=>'required | min:6',
			'dept_id'=>'required',
			'seller_id'=>'required'			
		]);

		
		$email=trim($request->input('email'));
		$password=trim($request->input('password'));
		$name=trim($request->input('name'));
		$sellerIds=$request->input('seller_id');
		
		
		
		// check email verification
		$getEmail=User::where('email','=',$email);
		$checkEmail=$getEmail->get();
		if(count($checkEmail) > 0)
		{
			session()->flash('msg','Email already exists.');
			return redirect()->back();
		}
		else
		{
			$data=array();
			$data = array('name'=>$name,'email' => $email,'password'=>$password);
			$emailDetails['userEmailTo']=$email;
			// $emailDetails['userEmailTo']="sahil.j@iapptechnologies.com";
			
			
/*			$mailStatus=Mail::send('emails.loginDetails', $data, function ($message) use ($emailDetails) {
			$message->from('demovisions@gmail.com', 'Kennedy');
			$message->to($emailDetails['userEmailTo'])->subject('Kennedy Login Details');			
			}); */
			$mailStatus=1;
			if($mailStatus == 1)
			{				
				$hashpassword=Hash::make($password);
				$user = new User;
				$user->name = $name;
				$user->email = $email;
				$user->password = $hashpassword;
				$user->decrypt_password = $password;
				$user->remember_token = $request->input('_token');
				$user->role = 1;
				if($user->save())
				{
					$userid=$user->id;
					foreach($sellerIds as $singleSellerId)
					{
						$individualId=explode(" ",$singleSellerId);
						$singleSellerId=$individualId[1];
						$departmentId=$individualId[0];
						
						
						// join query to get products, and their corresponding department ids for the selected sellers.
						$getData=DB::table('sellers')
						->join('products','products.seller_id','=','sellers.id')
						->select('products.id as productId','sellers.id as sellerId','sellers.department_id')		
						->where ('sellers.id','=',$singleSellerId)
						->get(); 
						foreach($getData as $pos=>$singleData)
						{
							$product_id=$singleData->productId;
							$seller_id=$singleData->sellerId;
							$department_id=$departmentId;
							$insertProductDetails=new Buyer;
							$insertProductDetails->user_id=$userid;
							$insertProductDetails->dept_id=$department_id;
							$insertProductDetails->seller_id=$seller_id;
							$insertProductDetails->product_id=$product_id;
							$insertProductDetails->updated_at=date('Y-m-d h:i:s');
							$insertProductDetails->save();
						}
					}
				}					
			}
		}
		session()->flash('success','Buyer successfully created.');
		
		return redirect('buyers');
	}
	
	public function update($id)
	{
		echo $id;
		exit;
	}
	
}

  