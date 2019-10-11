<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;


use App\Models\LogActivity;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->per_page;
        $search = $request->filter;
        $min = $request->min;
        $max = $request->max;
        $query = Role::orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('role_name', 'LIKE', $like)->orWhere('role_code', 'LIKE', $like);
        }
        if($min && !$max)
        {
            $query = $query->whereDate('created_at','=',$min);
        }
        if(!$min && $max)
        {
            $query = $query->whereDate('created_at','=',$max);
        }
        if($min && $max)
        {
            $query = $query->whereDate('created_at','>=',$min)->whereDate('created_at','<=',$max);
        }
         
        return $query->paginate($perPage);
    }

    public function store(Request $request)
    { 
        $valid = $this->validate($request, [ 
            'role_code' => 'required|max:191|without_spaces|unique:roles,role_code', 
            'role_name' => 'required|max:191',  
        ]);
        $masuk = array('role_name' => $request->role_name,'role_code' => $request->role_code); 
        Role::create($masuk);
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'roles' ,'action' => 'insert', 'data' => json_encode($masuk)]);
        return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
    }


    public function destroy($id)
    {
        $cek = Role::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'roles' ,'action' => 'delete', 'data' => json_encode($cek)]);
            Role::where('id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 

    }

    public function update(Request $request, $id)
    {
        $cek = Role::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            $valid = $this->validate($request, [ 
                'role_code' => 'required|max:191|without_spaces|unique:roles,role_code,'.$id,
                'role_name' => 'required|max:191', 
            ]);
            $edit = array('role_code' => $request->role_code, 'role_name' => $request->role_name);
            $cek->update($edit);
            LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'roles' ,'action' => 'update', 'data' => json_encode($cek)]);
            return response()->json(['status'=>200,'data'=>'','message'=>'Edit Successfully']);
        }

    }

    
    public function getRole()
    { 
        $data = Role::orderBy('id','ASC')->get(); 
            return response()->json(['status'=>200,'data'=>$data,'message'=>'']); 
    } 

    public function deleteAll($id)
    {
        $get =  Role::whereIn('id',explode(",",$id))->get();
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'roles' ,'action' => 'delete', 'data' => json_encode($get)]); 
        Role::whereIn('id',explode(",",$id))->delete();
        return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
    }

}