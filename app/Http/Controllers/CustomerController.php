<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use DB;

class CustomerController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function add_customer(Request $request)
    {
        $validator = $request->validate([
            'customerName' => 'required',
            'orgname' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'postal_area' => 'required',
            'country' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:customers,email',
            'note' => 'required'
        ]);
        $count = Customer::count();
        $customer = new Customer;
        $customer->customer_name = $request->input('customerName');
        $customer->user_id = $request->user()->id;
        $customer->org_number = $request->input('orgname');
        $customer->customer_id = "CU-0000".($count+1);
        $customer->country = $request->input('country');
        $customer->address = $request->input('address');
        $customer->postal_code = $request->input('postal_code');
        $customer->postal_area = $request->input('postal_area');
        $customer->name = $request->input('name');
        $customer->email = $request->input('email');
        $customer->mobile_number = $request->input('mobile');
        $customer->note = $request->input('note');
        $customer->status = 1;
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = 'uploads/customers/';
            //$destinationPath = public_path($file_path);
            //$image->move($destinationPath, $name);
            Storage::disk('s3')->put($file_path.$name, file_get_contents($image));
            $customer->image_path = $name;
        }
        if($customer->save()) {
            return response()->json(['status'=>'1','message' => 'Successfully customer added.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in customer add.'], 422);
        }
    }

    public function customers() {
        $customers = Customer::where('status', '=', '1')->withCount(['projects'])
                        ->with(['projects'=> function($query){
                            $query->select('id', 'customer_id')->withCount('products');
                        }])
                        ->get();
        if(count($customers) > 0) {
            foreach($customers as $k=>$eachcutomer) {
                $product_count = 0;
                if(count($eachcutomer['projects']) > 0) {
                    foreach($eachcutomer['projects'] as $eachproject){
                        $product_count = $product_count + $eachproject->products_count;
                    }
                }
                $customers[$k]['products_count'] = $product_count;
            }
        }
        return response()->json(['status'=>'1','message' => 'Customer List', 'customers' => $customers, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/customers'], 200);
    }

    public function get_customer_info($id) {
        $customer = Customer::where('id', '=', $id)->first();
        return response()->json(['status'=>'1','message' => 'Client Information', 'customer' => $customer, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/customers'], 200);
    }

    public function search_customer(Request $request) {
        //DB::enableQueryLog();
        $search_text = $request->input('query');
        $customers = Customer::where('status', '=', '1')
                     ->where(function($query) use ($search_text) {
                        $query->orWhere('org_number', 'Like', '%'.$search_text.'%')
                        ->orWhere('name', 'Like', '%'.$search_text.'%')
                        ->orWhere('email', 'Like', '%'.$search_text.'%')
                        ->orWhere('postal_code', 'Like', '%'.$search_text.'%')
                        ->orWhere('postal_area', 'Like', '%'.$search_text.'%')
                        ->orWhere('mobile_number', 'Like', '%'.$search_text.'%');
                     })
                     ->get();
                     //print_r(DB::getQueryLog());
        return response()->json(['status'=>'1','message' => 'Customer List', 'customers' => $customers, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/customers'], 200);
    }

    public function edit_customer(Request $request){
        $validator = $request->validate([
            'customerName' => 'required',
            'orgname' => 'required',
            'id' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'postal_area' => 'required',
            'country' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email',
            'note' => 'required'
        ]);

        $customer = Customer::find($request->input('id'));
        $customer->customer_name = $request->input('customerName');
        $customer->user_id = $request->user()->id;
        $customer->org_number = $request->input('orgname');
        $customer->address = $request->input('address');
        $customer->country = $request->input('country');
        $customer->postal_code = $request->input('postal_code');
        $customer->postal_area = $request->input('postal_area');
        $customer->name = $request->input('name');
        $customer->email = $request->input('email');
        $customer->mobile_number = $request->input('mobile');
        $customer->note = $request->input('note');
        $customer->status = 1; 
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = 'uploads/customers/';
            //$destinationPath = public_path($file_path);
            //$image->move($destinationPath, $name);
            Storage::disk('s3')->put($file_path.$name, file_get_contents($image));
            $customer->image_path = $name;
        }
        if($customer->save()) {
            return response()->json(['status'=>'1','message' => 'Successfully customer edited.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in customer edit.'], 422);
        }

    }

    public function deleteCustomer($id){
        Customer::where('id', '=', $id)->delete();
        return response()->json(['status'=>'1','message' => 'Customer deleted Successfully'], 200);
    }

}