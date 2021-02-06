<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Support\Facades\Storage;
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
            'customer' => 'required'
        ]);
        $count = Project::count();
        $project = new Project;
        $project->project_name = $request->input('project_name');
        $project->customer_id = $request->input('customer');
        $project->project_id = "PR-0000".($count+1);
        $project->user_id = $request->user()->id;
        $project->project_address = $request->input('project_address');
        $project->postal_area = $request->input('postal_area');
        $project->postal_code = $request->input('postal_code');
        $project->project_mang_name = $request->input('project_mang_name');
        $project->project_mang_mobile = $request->input('project_mang_mobile');
        $project->project_mang_email = $request->input('project_mang_email');
        
        $project->onsite_name = $request->input('onsite_name');
        $project->onsite_mobile = $request->input('onsite_mobile');
        $project->onsite_email = $request->input('onsite_email');
        $project->project_type = $request->input('project_type');
        $project->project_status = $request->input('project_status');
        $project->property_area = $request->input('property_area');
        $project->no_of_floors = $request->input('no_of_floors');
        $project->building_year = $request->input('building_year');
        $project->last_refurbished = $request->input('last_refurbished');
        $project->env_report = $request->input('env_report');
        $project->fdv_document = $request->input('fdv_document');
        $project->ambition = $request->input('ambition');
        $project->project_start_date = $request->input('project_start_date');
        $project->project_catalog_date = $request->input('project_catalog_date');
        $project->project_avail_date = $request->input('project_avail_date');
        $project->project_avail_end_date = $request->input('project_avail_end_date');
        $project->note = $request->input('note');
        $project->billing_project_company = $request->input('billing_project_company');
        $project->billing_orgno = $request->input('billing_orgno');
        $project->billing_project_number = $request->input('billing_project_number');
        $project->billing_customer_ref = $request->input('billing_customer_ref');
        $project->billing_address = $request->input('billing_address');
        $project->billing_postal_code = $request->input('billing_postal_code');
        $project->billing_postal_area = $request->input('billing_postal_area');
        $project->credit_period = $request->input('credit_period');
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = 'uploads/projects/';
            //$destinationPath = public_path($file_path);
            //$image->move($destinationPath, $name);
            Storage::disk('s3')->put($file_path.$name, file_get_contents($image));
            $project->project_image = $name;
        }
        $project->save();
        $project_id = $project->id;
        if($request->hasFile('imagemultiFile')){
            foreach($request->file('imagemultiFile') as $k=>$eachfile) {
                $name = pathinfo($eachfile->getClientOriginalName(), PATHINFO_FILENAME).'_'.time().'.'.$eachfile->getClientOriginalExtension();
                $file_path = 'uploads/projects/documents/';
                //$destinationPath = public_path($file_path);
                //$eachfile->move($destinationPath, $name);
                Storage::disk('s3')->put($file_path.$name, file_get_contents($eachfile));
                $projectlist = new ProjectFile;
                $projectlist->category_id =  $request->input('filecategory')[$k];
                $projectlist->file_name = $name;
                $projectlist->project_id = $project_id;
                $projectlist->save();
            }
        }
        if($project_id) {
            return response()->json(['status'=>'1','message' => 'Successfully project added.', 'project_id' => $project->project_id], 200);
        } else {
            return response()->json(['status'=>'0','message' => 'Error occured in project add.'], 422);
        }
    }

    public function projects() {
        $projects = Project::with(['customer' => function($query){
                        $query->select('name as customer_name', 'id', 'image_path');
                    }])->with(['projectdocs' => function($query){
                        $query->select('project_id', 'file_name');
                    }])->get()->toArray();
        return response()->json(['status'=>'1','message' => 'Project List', 'projects' => $projects, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/projects'], 200);
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
        return response()->json(['status'=>'1','message' => 'Project List', 'projects' => $projects, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/projects'], 200);
    }

    public function get_project_info($id){
        $project = Project::with(['customer' => function($query){
            $query->select('name as customer_name', 'id', 'image_path');
        }])->with(['projectdocs' => function($query){
            $query->select('id', 'category_id', 'project_id', 'file_name')->with('projectCategory');
        }])
        ->where('id', $id)
        ->first();
        return response()->json(['status'=>'1','message' => 'project info', 'project' => $project, 'image_base_path' => 'https://resources-products.s3.us-east-2.amazonaws.com/uploads/projects'], 200);
    }

    public function edit_project(Request $request)
    {
       
        $validator = $request->validate([
            'project_name' => 'required',
            'customer' => 'required'
        ]);
        
        $project = Project::find($request->input('id'));
        $project->project_name = $request->input('project_name');
        $project->customer_id = $request->input('customer');
        $project->user_id = $request->user()->id;
        $project->project_address = $request->input('project_address');
        $project->postal_area = $request->input('postal_area');
        $project->postal_code = $request->input('postal_code');
        $project->project_mang_name = $request->input('project_mang_name');
        $project->project_mang_mobile = $request->input('project_mang_mobile');
        $project->project_mang_email = $request->input('project_mang_email');
        
        $project->onsite_name = $request->input('onsite_name');
        $project->onsite_mobile = $request->input('onsite_mobile');
        $project->onsite_email = $request->input('onsite_email');
        $project->project_type = $request->input('project_type');
        $project->project_status = $request->input('project_status');
        $project->property_area = $request->input('property_area');
        $project->no_of_floors = $request->input('no_of_floors');
        $project->building_year = $request->input('building_year');
        $project->last_refurbished = $request->input('last_refurbished');
        $project->env_report = $request->input('env_report');
        $project->fdv_document = $request->input('fdv_document');
        $project->ambition = $request->input('ambition');
        $project->project_start_date = $request->input('project_start_date');
        $project->project_catalog_date = $request->input('project_catalog_date');
        $project->project_avail_date = $request->input('project_avail_date');
        $project->project_avail_end_date = $request->input('project_avail_end_date');
        $project->note = $request->input('note');
        $project->billing_project_company = $request->input('billing_project_company');
        $project->billing_orgno = $request->input('billing_orgno');
        $project->billing_project_number = $request->input('billing_project_number');
        $project->billing_customer_ref = $request->input('billing_customer_ref');
        $project->billing_address = $request->input('billing_address');
        $project->billing_postal_code = $request->input('billing_postal_code');
        $project->billing_postal_area = $request->input('billing_postal_area');
        $project->credit_period = $request->input('credit_period');
        if($request->hasFile('imageFile')) {
            $image = $request->file('imageFile');
            $name = time().rand().'.'.$image->getClientOriginalExtension();
            $file_path = 'uploads/projects/';
            //$destinationPath = public_path($file_path);
            //$image->move($destinationPath, $name);
            Storage::disk('s3')->put($file_path.$name, file_get_contents($image));
            $project->project_image = $name;
        }
        $project->save();
        $project_id = $project->id;
        if($request->hasFile('imagemultiFile')){
            foreach($request->file('imagemultiFile') as $k=>$eachfile) {
                $name = pathinfo($eachfile->getClientOriginalName(), PATHINFO_FILENAME).'_'.time().'.'.$eachfile->getClientOriginalExtension();
                $file_path = 'uploads/projects/documents/';
                //$destinationPath = public_path($file_path);
                //$eachfile->move($destinationPath, $name);
                Storage::disk('s3')->put($file_path.$name, file_get_contents($eachfile));
                $projectlist = new ProjectFile;
                $projectlist->category_id =  $request->input('filecategory')[$k];
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

    public function delete_project_doc($id){
        ProjectFile::where('id', $id)->delete();
        return response()->json(['status'=>'1','message' => 'Selected Project Document deleted Successfully'], 200);
    }

}