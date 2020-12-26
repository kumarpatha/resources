<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Customer;
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
        //echo "<pre>"; print_r($_FILES);print_r($request->all());
        $validator = $request->validate([
            'registerUsername' => 'required',
            'orgname' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'postal_area' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:customers,email',
            'note' => 'required',
            'imageFile' => 'required'
        ]);
        
        $client = new Customer;
        $client->client_name = $request->input('registerUsername');
        $client->org_number = $request->input('orgname');
        $client->address = $request->input('address');
        $client->postal_code = $request->input('postal_code');
        $client->postal_area = $request->input('postal_area');
        $client->name = $request->input('name');
        $client->email = $request->input('email');
        $client->mobile_number = $request->input('mobile');
        $client->note = $request->input('note');
        $client->status = 1;
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = '/uploads/customers/';
            $destinationPath = public_path($file_path);
            $image->move($destinationPath, $name);
            $client->image_path = $name;
        }
        if($client->save()) {
            return response()->json(['status'=>'1','message' => 'Successfully customer added.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in customer add.'], 422);
        }
    }

    public function customers() {
        $customers = Customer::where('status', '=', '1')->get();
        return response()->json(['status'=>'1','message' => 'Customer List', 'customers' => $customers, 'image_base_path' => url('/uploads/customers/')], 200);
    }

    public function search_customer(Request $request) {
        DB::enableQueryLog();
        $search_text = $request->input('query');
        $customers = Customer::where('status', '=', '1')
                     ->where(function($query) use ($search_text) {
                        $query->orWhere('client_name', 'Like', '%'.$search_text.'%')
                        ->orWhere('org_number', 'Like', '%'.$search_text.'%')
                        ->orWhere('name', 'Like', '%'.$search_text.'%')
                        ->orWhere('email', 'Like', '%'.$search_text.'%')
                        ->orWhere('postal_code', 'Like', '%'.$search_text.'%')
                        ->orWhere('postal_area', 'Like', '%'.$search_text.'%')
                        ->orWhere('mobile_number', 'Like', '%'.$search_text.'%');
                     })
                     ->get();
                     //print_r(DB::getQueryLog());
        return response()->json(['status'=>'1','message' => 'Customer List', 'customers' => $customers, 'image_base_path' => url('/uploads/customers/')], 200);
    }

}