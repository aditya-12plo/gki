<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response,View,Input,Auth,Session,Validator,File,Hash,DB,Mail;
use Illuminate\Support\Facades\Crypt;


use App\Models\User;
use App\Models\LogActivity;

class UserController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page;
        $search = $request->filter;
        $min = $request->min;
        $max = $request->max;
        $query = User::with('role')->whereNotIn('id',[Auth::user()->id])->orderBy('id','DESC');
        if ($search) {
            $like = "%{$search}%";
            $query = $query->where('email', 'LIKE', $like)->orWhere('name', 'LIKE', $like);
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
            'role_id' => 'required|numeric|not_in:0',
            'name' => 'required|max:255',
            'email' => 'required|max:255|unique:users,email',
            'password' => 'required|max:255',
            'status' => 'in:active,inactive', 
        ]);
        $masuk = array('role_id' => $request->role_id, 'name' => $request->name , 'email' => $request->email , 'password' => Hash::make($request->password) ,'status' =>$request->status); 
        User::create($masuk);
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'users' ,'action' => 'insert', 'data' => json_encode($masuk)]);
        return response()->json(['status'=>200,'data'=>'','message'=>'Add Successfully']);
    }


    public function destroy($id)
    {
        $cek = User::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }else{
            LogActivity::create(['name' => Auth::user()->name, 'email' => Auth::user()->email, 'table'=>'users' ,'action' => 'delete', 'data' => json_encode($cek)]);
            User::where('id',$id)->delete();
            return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
        } 
    }

    public function update(Request $request, $id)
    {
        $cek = User::findOrFail($id);
        if(!$cek)
        {
            return response()->json(['status'=>404,'data'=>'','message'=>['error'=>['Data Not Found']]]);
        }
        else
        {
            if($request->password == '')
            {
                $valid = $this->validate($request, [
                    'role_id' => 'required|numeric|not_in:0',
                    'name' => 'required|max:255',
                    'email' => 'required|max:255|unique:users,email,'.$id,
                    'status' => 'in:active,inactive', 
                ]);
                $edit = array('role_id' => $request->role_id, 'name' => $request->name , 'email' => $request->email , 'status' =>$request->status); 
            }else{ 
                $valid = $this->validate($request, [
                    'role_id' => 'required|numeric|not_in:0',
                    'name' => 'required|max:255',
                    'email' => 'required|max:255|unique:users,email,'.$id,
                    'password' => 'required|max:255',
                    'status' => 'in:active,inactive', 
                ]);
                $edit = array('role_id' => $request->role_id, 'name' => $request->name , 'email' => $request->email , 'password' => Hash::make($request->password) ,'status' =>$request->status); 
            }
            
            LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'users' ,'action' => 'update', 'data' => json_encode($cek)]);
            $cek->update($edit); 
            return response()->json(['status'=>200,'data'=>'','message'=>'Update Successfully']);
        }
    }

    
    public function getProfile()
    { 
        $cek = User::with('role')->findOrFail(Auth::user()->id);
        if(!$cek){
            return response()->json(['status'=>404,'data'=>'','message'=>'']);
        }else{
            return response()->json(['status'=>200,'data'=>$cek,'message'=>'']);
        } 
    }

    public function changePassword(Request $request)
    {
        $valid = $this->validate($request, [
                'password' => 'required|max:255',
                'password_confirmation' => 'required|max:255|same:password'
            ]);
            User::where("id",Auth::user()->id)->update(['password' => Hash::make($request->password)]);
            return response()->json(['status'=>200,'data'=>'','message'=>'Your password has been change.']); 
        
    }
 

    public function deleteAll($id)
    {
        $get =  User::whereIn('id',explode(",",$id))->get();
        LogActivity::create(['name' => Auth::user()->id, 'email' => Auth::user()->email, 'table'=>'users' ,'action' => 'delete', 'data' => json_encode($get)]); 
        User::whereIn('id',explode(",",$id))->delete();
        return response()->json(['status'=>200,'data'=>'','message'=>'Delete Successfully']);
    }


    public function getSuperAdmin()
    { 
        if(Auth::user()->role_id == 1){
            return response()->json(['status'=>200,'data'=>'','message'=>'']);
        }else{
            return response()->json(['status'=>404,'data'=>'','message'=>'']);
        }
    }
    
    public function getops()
    { 
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 6){
            return response()->json(['status'=>200,'data'=>'','message'=>'']);
        }else{
            return response()->json(['status'=>404,'data'=>'','message'=>'']);
        }
    }
    
    public function getHrd()
    { 
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 3 || Auth::user()->role_id == 6){
            return response()->json(['status'=>200,'data'=>'','message'=>'']);
        }else{
            return response()->json(['status'=>404,'data'=>'','message'=>'']);
        }
    }

    public function getUkk()
    { 
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 4 || Auth::user()->role_id == 7){
            return response()->json(['status'=>200,'data'=>'','message'=>'']);
        }else{
            return response()->json(['status'=>404,'data'=>'','message'=>'']);
        }
    }

}
