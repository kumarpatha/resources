<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Support\Facades\Storage;
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
        $product->product_name = $request->input('product_name');
        $product->project_id = $request->input('project_id');
        $product->product_id = rand(0, 99999);
        $product->user_id = $request->user()->id;
        $product->description = $request->input('description');
        $product->category = $request->input('category');
        $product->building_part = $request->input('building_part');
        $product->quantity = $request->input('quantity');
        $product->unit = $request->input('unit');
        $product->length = $request->input('length');
        $product->width = $request->input('width');
        $product->height = $request->input('height');
        $product->production_year = $request->input('production_year');
        $product->location_building = $request->input('location_building');
        $product->brand_name = $request->input('brand_name');
        $product->documentation = $request->input('documentation');
        $product->product_info = $request->input('product_info');
        $product->color = $request->input('color');
        $product->hazardous = $request->input('hazardous');
        $product->evaluvation = $request->input('evaluvation');
        $product->precondition = $request->input('precondition');
        $product->reuse = $request->input('reuse');
        $product->recommendation = $request->input('recommendation');
        $product->price_new_product = $request->input('price_new_product');
        $product->price_used_product = $request->input('price_used_product');
        $product->price_sold_product = $request->input('price_sold_product');
        $product->status = $request->input('status');
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

    public function products() {
        $products = Product::with(['project' => function($query){
                        $query->select('project_name', 'id', 'project_image');
                    }])->with(['productdocs' => function($query){
                        $query->select('id','category_id','product_id', 'file_name')->with('productCategory');
                    }])->with('category')->get();
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
        $product->product_name = $request->input('product_name');
        $product->project_id = $request->input('project_id');
        $product->product_id = $request->input('product_id');
        $product->user_id = $request->user()->id;
        $product->description = $request->input('description');
        $product->category = $request->input('category');
        $product->building_part = $request->input('building_part');
        $product->quantity = $request->input('quantity');
        $product->unit = $request->input('unit');
        $product->length = $request->input('length');
        $product->width = $request->input('width');
        $product->height = $request->input('height');
        $product->production_year = $request->input('production_year');
        $product->location_building = $request->input('location_building');
        $product->brand_name = $request->input('brand_name');
        $product->documentation = $request->input('documentation');
        $product->product_info = $request->input('product_info');
        $product->color = $request->input('color');
        $product->hazardous = $request->input('hazardous');
        $product->evaluvation = $request->input('evaluvation');
        $product->precondition = $request->input('precondition');
        $product->reuse = $request->input('reuse');
        $product->recommendation = $request->input('recommendation');
        $product->price_new_product = $request->input('price_new_product');
        $product->price_used_product = $request->input('price_used_product');
        $product->price_sold_product = $request->input('price_sold_product');
        $product->status = $request->input('status');
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

}