<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


use App\Models\Product;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProductFile;
use Illuminate\Support\Facades\Storage;
use PDF;

use DataTables;
use DB;

class ProductController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function add_product(Request $request)
    {
       
        $validator = $request->validate([
            'product_name' => 'required',
            'project_id' => 'required',
            'description' => 'required',
            // 'category' => 'required',
            // 'building_part' => 'required',
            // 'quantity' => 'required',
            // 'unit' => 'required',
            // 'height' => 'required',
            // 'width' => 'required',
            // 'length' => 'required',
            // 'production_year' => 'required',
            // 'location_building' => 'required',
            // 'brand_name' => 'required',
            // 'documentation' => 'required',
            // 'product_info' => 'required',
            // 'color' => 'required',
            // 'hazardous' => 'required',
            // 'evaluvation' => 'required',
            // 'precondition' => 'required',
            // 'reuse' => 'required',
            // 'recommendation' => 'required',
            // 'price_new_product' => 'required',
            // 'price_used_product' => 'required',
            // 'price_sold_product' => 'required',
            // 'imageFile' => 'required'
        ]);
        
        $product = new Product;
        $product->product_name = $this->filter_data($request->input('product_name'));
        $product->project_id = $this->filter_data($request->input('project_id'));
        $product->product_id = rand(0, 99999);
        $product->user_id = $request->user()->id;
        $product->description = $this->filter_data($request->input('description'));
        $product->category = $this->filter_data($request->input('category'));
        $product->building_part = $this->filter_data($request->input('building_part'));
        $product->quantity = $this->filter_data($request->input('quantity'));
        $product->unit = $this->filter_data($request->input('unit'));
        $product->length = $this->filter_data($request->input('length'));
        $product->width = $this->filter_data($request->input('width'));
        $product->height = $this->filter_data($request->input('height'));
        $product->production_year = $this->filter_data($request->input('production_year'));
        $product->location_building = $this->filter_data($request->input('location_building'));
        $product->brand_name = $this->filter_data($request->input('brand_name'));
        $product->documentation = $this->filter_data($request->input('documentation'));
        $product->product_info = $this->filter_data($request->input('product_info'));
        $product->color = $this->filter_data($request->input('color'));
        $product->hazardous = $this->filter_data($request->input('hazardous'));
        $product->evaluvation = $this->filter_data($request->input('evaluvation'));
        $product->precondition = $this->filter_data($request->input('precondition'));
        $product->reuse = $this->filter_data($request->input('reuse'));
        $product->recommendation = $this->filter_data($request->input('recommendation'));
        $product->price_new_product = $this->filter_data($request->input('price_new_product'));
        $product->price_used_product = $this->filter_data($request->input('price_used_product'));
        $product->price_sold_product = $this->filter_data($request->input('price_sold_product'));
        $product->status = $this->filter_data($request->input('status'));
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = 'uploads/products/';
            //$destinationPath = public_path($file_path);
            //$image->move($destinationPath, $name);
            Storage::disk('s3')->put($file_path.$name, file_get_contents($image));
            $product->product_image = $name;
        }
        $product->save();
        $product_id = $product->id;
        if($request->hasFile('imagemultiFile')){
            foreach($request->file('imagemultiFile') as $k=>$eachfile) {
                $name = pathinfo($eachfile->getClientOriginalName(), PATHINFO_FILENAME).'_'.time().'.'.$eachfile->getClientOriginalExtension();
                $file_path = 'uploads/products/documents/';
                //$destinationPath = public_path($file_path);
                //$eachfile->move($destinationPath, $name);
                Storage::disk('s3')->put($file_path.$name, file_get_contents($eachfile));
                $productlist = new ProductFile;
                $productlist->category_id =  $request->input('filecategory')[$k];
                $productlist->file_name = $name;
                $productlist->product_id = $product_id;
                $productlist->save();
            }
        }
        if($product_id) {
            return response()->json(['status'=>'1','message' => 'Successfully product added.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in product add.'], 422);
        }
    }

    public function products(Request $request) {
        $products = Product::with(['project' => function($query){
                        $query->select('project_name', 'id', 'project_image');
                    }])->with(['productdocs' => function($query){
                        $query->select('id','category_id','product_id', 'file_name')->with('productCategory');
                    }])->with('category')
                    ->join('projects', 'products.project_id', '=', 'projects.id')
                    ->join('customers', 'customers.id', '=', 'projects.customer_id')
                    ->join('users', 'users.id', '=', 'products.user_id')
                    ->select('products.*')
                    ->where('users.client_id', $request->user()->client_id);
        if($request->input('project')){
            $products =  $products->where('products.project_id', $request->input('project'));
        }
        if($request->input('customer_filter')){
            $products =  $products->whereIn('customers.id', $request->input('customer_filter'));
        }
        if($request->input('project_filter')){
            $products =  $products->whereIn('products.project_id', $request->input('project_filter'));
        }        
         //echo "<pre>";
         //print_r($products->get()); //die;
        return DataTables::eloquent($products)
                    ->addColumn('image_base_path', 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/products')
                    ->addIndexColumn('index')
                    ->editColumn('status', function($product) {
                        return  ($product->status) ? "Available" : "Not Available";
                    })
                    ->editColumn('product_id', function($product) {
                        return  "<a href='/view-product/$product->id'>".$product->product_id."</a>";
                    })
                    ->editColumn('product_name', function($product) {
                        return  "<a href='/view-product/$product->id'>".$product->product_name."</a>";
                    })
                    ->addColumn('product_name_raw', function($product) {
                        return  $product->product_name;
                    })
                    ->addColumn('dimention', function($product) {
                        if($product->height && $product->length && $product->width){
                            return $product->height."X".$product->length."X".$product->width;
                        }else {
                            return '';
                        }
                    })
                    // ->addColumn('category_name', function($product) {
                    //     if(isset($product->category->category_name)){
                    //         return $product->category->category_name;
                    //     }else {
                    //         return '';
                    //     }
                    // })
                    ->rawColumns(['product_id', 'product_name'])
                    ->make();
        //return response()->json(['status'=>'1','message' => 'product List', 'products' => $products, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/products'], 200);
    }

    public function productList(){
        $products = Product::select('id', 'product_name')->get();
        return response()->json(['status'=>'1','message' => 'product List', 'products' => $products, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/products'], 200);
    }

    public function search_product(Request $request) {
        DB::enableQueryLog();
        $search_text = $request->input('query');
        $products = Product::with(['project' => function($query) {
                                $query->select('project_name', 'id', 'project_image');
                            }])->with(['productdocs' => function($query){
                                $query->select('id','category_id','product_id', 'file_name')->with('productCategory');
                            }])
                            ->where('status', '=', '1')
                            ->where(function($query) use ($search_text) {
                                $query->orWhere('product_name', 'Like', '%'.$search_text.'%')
                                ->orWhere('product_id', 'Like', '%'.$search_text.'%')
                                ->orWhere('description', 'Like', '%'.$search_text.'%');
                            })
                            ->get();
        return response()->json(['status'=>'1','message' => 'product List', 'products' => $products, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/products'], 200);
    }

    public function get_product_info($id){
        $product = Product::with(['project' => function($query){
            $query->select('project_name', 'id', 'project_image');
        }])->with(['productdocs' => function($query){
            $query->select('id','category_id','product_id', 'file_name')->with('productCategory');
        }])
        ->where('id', $id)
        ->first();
        return response()->json(['status'=>'1','message' => 'product info', 'product' => $product, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/products'], 200);
    }

    public function edit_product(Request $request){
        $validator = $request->validate([
            'product_name' => 'required',
            'id' => 'required',
            'project_id' => 'required',
            'description' => 'required',
            // 'category' => 'required',
            // 'building_part' => 'required',
            // 'quantity' => 'required',
            // 'unit' => 'required',
            // 'height' => 'required',
            // 'width' => 'required',
            // 'length' => 'required',
            // 'production_year' => 'required',
            // 'location_building' => 'required',
            // 'brand_name' => 'required',
            // 'documentation' => 'required',
            // 'product_info' => 'required',
            // 'color' => 'required',
            // 'hazardous' => 'required',
            // 'evaluvation' => 'required',
            // 'precondition' => 'required',
            // 'reuse' => 'required',
            // 'recommendation' => 'required',
            // 'price_new_product' => 'required',
            // 'price_used_product' => 'required',
            // 'price_sold_product' => 'required'
        ]);
        
        $product = Product::find($request->input('id'));
        $product->product_name = $this->filter_data($request->input('product_name'));
        $product->project_id = $this->filter_data($request->input('project_id'));
        $product->product_id = $this->filter_data($request->input('product_id'));
        $product->user_id = $request->user()->id;
        $product->description = $this->filter_data($request->input('description'));
        $product->category = $this->filter_data($request->input('category'));
        $product->building_part = $this->filter_data($request->input('building_part'));
        $product->quantity = $this->filter_data($request->input('quantity'));
        $product->unit = $this->filter_data($request->input('unit'));
        $product->length = $this->filter_data($request->input('length'));
        $product->width = $this->filter_data($request->input('width'));
        $product->height = $this->filter_data($request->input('height'));
        $product->production_year = $this->filter_data($request->input('production_year'));
        $product->location_building = $this->filter_data($request->input('location_building'));
        $product->brand_name = $this->filter_data($request->input('brand_name'));
        $product->documentation = $this->filter_data($request->input('documentation'));
        $product->product_info = $this->filter_data($request->input('product_info'));
        $product->color = $this->filter_data($request->input('color'));
        $product->hazardous = $this->filter_data($request->input('hazardous'));
        $product->evaluvation = $this->filter_data($request->input('evaluvation'));
        $product->precondition = $this->filter_data($request->input('precondition'));
        $product->reuse = $this->filter_data($request->input('reuse'));
        $product->recommendation = $this->filter_data($request->input('recommendation'));
        $product->price_new_product = $this->filter_data($request->input('price_new_product'));
        $product->price_used_product = $this->filter_data($request->input('price_used_product'));
        $product->price_sold_product = $this->filter_data($request->input('price_sold_product'));
        $product->status = $this->filter_data($request->input('status'));

        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = '/uploads/products/';
            $destinationPath = public_path($file_path);
            $image->move($destinationPath, $name);
            $product->product_image = $name;
        }
        $product->save();
        $product_id = $product->id;
        if($request->hasFile('imagemultiFile')){
            //ProductFile::where('product_id', $product_id)->delete();
            foreach($request->file('imagemultiFile') as $k=>$eachfile) {
                $name = pathinfo($eachfile->getClientOriginalName(), PATHINFO_FILENAME).'_'.time().'.'.$eachfile->getClientOriginalExtension();
                $file_path = '/uploads/products/documents/';
                $destinationPath = public_path($file_path);
                $eachfile->move($destinationPath, $name);
                $productlist = new ProductFile;
                $productlist->category_id =  $request->input('filecategory')[$k];
                $productlist->file_name = $name;
                $productlist->product_id = $product_id;
                $productlist->save();
            }
        }
        if($product_id) {
            return response()->json(['status'=>'1','message' => 'Successfully product edited.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in product edit.'], 422);
        }
    }

    public function deleteProduct($id){
        Product::where('id', '=', $id)->delete();
        return response()->json(['status'=>'1','message' => 'Product deleted Successfully'], 200);
    }

    public function delete_product_doc($id){
        ProductFile::where('id', $id)->delete();
        return response()->json(['status'=>'1','message' => 'Selected Product Document deleted Successfully'], 200);
    }

    public function filter_data($input){
        if(strtolower($input) == 'null') {
            return NULL;
        }
        return $input;
    }
    public function filter_product(Request $request) {
        $customers = Customer::select('id', 'customer_name', DB::raw('0 as isChecked'))->where('status', '1')->where('user_id', $request->user()->id)->get();
        $customers_count = $customers->count();
        $projects = Project::select('id', 'customer_id', 'project_name', DB::raw('0 as isChecked'))->where('user_id', $request->user()->id)->get();
        $projects_count = $projects->count();
        $projects = $projects->groupBy('customer_id');
        return response()->json(['status'=>'1','customer_count' => $customers_count, 'customers' => $customers, 'project_count' => $projects_count, 'projects' => $projects], 200);
    }

    public function pdf() {
        $pdf = PDF::loadView('pdf.catalog');
        return $pdf->download('invoice.pdf');
    }

    public function pdf2() {
        $pdf = PDF::loadView('pdf.catalog2');
        return $pdf->download('invoice2.pdf');
    }

}