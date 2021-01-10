<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Project;
use App\Models\ProjectFile;
use DB;

class ProjectController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function add_project(Request $request)
    {
       
        $validator = $request->validate([
            'project_name' => 'required',
            'customer' => 'required',
            'description' => 'required',
            'category' => 'required',
            'building_part' => 'required',
            'quantity' => 'required',
            'unit' => 'required',
            'dimensions' => 'required',
            'production_year' => 'required',
            'location_building' => 'required',
            'brand_name' => 'required',
            'documentation' => 'required',
            'product_info' => 'required',
            'color' => 'required',
            'hazardous' => 'required',
            'evaluvation' => 'required',
            'precondition' => 'required',
            'reuse' => 'required',
            'recommendation' => 'required',
            'price_new_product' => 'required',
            'price_used_product' => 'required',
            'price_sold_product' => 'required',
            'imageFile' => 'required'
        ]);
        
        $project = new Project;
        $project->project_name = $request->input('project_name');
        $project->customer_id = $request->input('customer');
        $project->user_id = $request->user()->id;
        $project->description = $request->input('description');
        $project->category = $request->input('category');
        $project->building_part = $request->input('building_part');
        $project->quantity = $request->input('quantity');
        $project->unit = $request->input('unit');
        $dimention = explode('x', $request->input('dimensions'));
        $project->length = $dimention[0];
        $project->width = $dimention[1];
        $project->height = $dimention[2];
        $project->production_year = $request->input('production_year');
        $project->location_building = $request->input('location_building');
        $project->brand_name = $request->input('brand_name');
        $project->documentation = $request->input('documentation');
        $project->product_info = $request->input('product_info');
        $project->color = $request->input('color');
        $project->hazardous = $request->input('hazardous');
        $project->evaluvation = $request->input('evaluvation');
        $project->precondition = $request->input('precondition');
        $project->reuse = $request->input('reuse');
        $project->recommendation = $request->input('recommendation');
        $project->price_new_product = $request->input('price_new_product');
        $project->price_used_product = $request->input('price_used_product');
        $project->price_sold_product = $request->input('price_sold_product');
        $project->status = $request->input('status');
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = '/uploads/projects/';
            $destinationPath = public_path($file_path);
            $image->move($destinationPath, $name);
            $project->project_image = $name;
        }
        $project->save();
        $project_id = $project->id;
        if($request->hasFile('imagemultiFile')){
            foreach($request->file('imagemultiFile') as $eachfile) {
                $name = time().rand().'.'.$eachfile->getClientOriginalExtension();
                $file_path = '/uploads/projects/documents/';
                $destinationPath = public_path($file_path);
                $eachfile->move($destinationPath, $name);
                $projectlist = new ProjectFile;
                $projectlist->file_name = $name;
                $projectlist->project_id = $project_id;
                $projectlist->save();
            }
        }
        if($project_id) {
            return response()->json(['status'=>'1','message' => 'Successfully project added.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in project add.'], 422);
        }
    }

    public function projects() {
        $projects = Project::with(['customer' => function($query){
                        $query->select('name as customer_name', 'id', 'image_path');
                    }])->with(['projectdocs' => function($query){
                        $query->select('project_id', 'file_name');
                    }])->get();
        return response()->json(['status'=>'1','message' => 'Project List', 'projects' => $projects, 'image_base_path' => url('/uploads/projects/')], 200);
    }

    public function search_project(Request $request) {
        DB::enableQueryLog();
        $search_text = $request->input('query');
        $projects = Project::with(['customer' => function($query){
                                $query->select('name as customer_name', 'id', 'image_path');
                            }])->with(['projectdocs' => function($query){
                                $query->select('project_id', 'file_name');
                            }])
                    ->where('status', '=', '1')
                    ->where(function($query) use ($search_text) {
                        $query->orWhere('project_name', 'Like', '%'.$search_text.'%')
                        ->orWhere('description', 'Like', '%'.$search_text.'%')
                        ->orWhere('category', 'Like', '%'.$search_text.'%');
                     })
                     ->get();
        return response()->json(['status'=>'1','message' => 'Project List', 'projects' => $projects, 'image_base_path' => url('/uploads/projects/')], 200);
    }

    public function get_project_info($id){
        $project = Project::with(['customer' => function($query){
            $query->select('name as customer_name', 'id', 'image_path');
        }])->with(['projectdocs' => function($query){
            $query->select('project_id', 'file_name');
        }])
        ->where('id', $id)
        ->first();
        return response()->json(['status'=>'1','message' => 'project info', 'project' => $project, 'image_base_path' => url('/uploads/projects/')], 200);
    }

    public function edit_project(Request $request)
    {
       
        $validator = $request->validate([
            'project_name' => 'required',
            'customer' => 'required',
            'description' => 'required',
            'category' => 'required',
            'building_part' => 'required',
            'quantity' => 'required',
            'unit' => 'required',
            'height' => 'required',
            'length' => 'required',
            'width' => 'required',
            'production_year' => 'required',
            'location_building' => 'required',
            'brand_name' => 'required',
            'documentation' => 'required',
            'product_info' => 'required',
            'color' => 'required',
            'hazardous' => 'required',
            'evaluvation' => 'required',
            'precondition' => 'required',
            'reuse' => 'required',
            'recommendation' => 'required',
            'price_new_product' => 'required',
            'price_used_product' => 'required',
            'price_sold_product' => 'required'
        ]);
        
        $project = Project::find($request->input('id'));
        $project->project_name = $request->input('project_name');
        $project->customer_id = $request->input('customer');
        $project->user_id = $request->user()->id;
        $project->description = $request->input('description');
        $project->category = $request->input('category');
        $project->building_part = $request->input('building_part');
        $project->quantity = $request->input('quantity');
        $project->unit = $request->input('unit');
        $project->length = $request->input('length');
        $project->width = $request->input('width');
        $project->height = $request->input('height');
        $project->production_year = $request->input('production_year');
        $project->location_building = $request->input('location_building');
        $project->brand_name = $request->input('brand_name');
        $project->documentation = $request->input('documentation');
        $project->product_info = $request->input('product_info');
        $project->color = $request->input('color');
        $project->hazardous = $request->input('hazardous');
        $project->evaluvation = $request->input('evaluvation');
        $project->precondition = $request->input('precondition');
        $project->reuse = $request->input('reuse');
        $project->recommendation = $request->input('recommendation');
        $project->price_new_product = $request->input('price_new_product');
        $project->price_used_product = $request->input('price_used_product');
        $project->price_sold_product = $request->input('price_sold_product');
        $project->status = $request->input('status');
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = '/uploads/projects/';
            $destinationPath = public_path($file_path);
            $image->move($destinationPath, $name);
            $project->project_image = $name;
        }
        $project->save();
        $project_id = $project->id;
        if($request->hasFile('imagemultiFile')){
            foreach($request->file('imagemultiFile') as $eachfile) {
                $name = time().rand().'.'.$eachfile->getClientOriginalExtension();
                $file_path = '/uploads/projects/documents/';
                $destinationPath = public_path($file_path);
                $eachfile->move($destinationPath, $name);
                $projectlist = new ProjectFile;
                $projectlist->file_name = $name;
                $projectlist->project_id = $project_id;
                $projectlist->save();
            }
        }
        if($project_id) {
            return response()->json(['status'=>'1','message' => 'Successfully project edited.'], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in project edit.'], 422);
        }
    }

    public function deleteProject($id){
        Project::where('id', '=', $id)->delete();
        return response()->json(['status'=>'1','message' => 'Project deleted Successfully'], 200);
    }

}