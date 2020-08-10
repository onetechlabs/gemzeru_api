<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(Request $request){
        $this->perpage = 10;
        $this->page = (null !== $request->input("page"))? (int)$request->input("page"):1;
        $this->spage = ($this->page > 1) ? ($this->page * $this->perpage) - $this->perpage : 0;
    }
    public function users(Request $request){
        $users = User::limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
        $total_users = User::all()->count();
        $total_page = ceil($total_users / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_users,
              "current_page" => $this->page,
              "records"=>$users
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function userShow(Request $request, $id){
        $total_users = User::where("id",$id)->count();
        if($total_users !==0){
          $user = User::where("id",$id)->get();
          $out = [
              "message" => "Success",
              "data"    => [
                "record"=> $user
              ],
              "code"    => 200
          ];
        }else{
          $out = [
              "message" => "Failed to Show User",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function userCreate(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'email' => 'required|email|unique:users|max:255',
          'password' => 'required|min:6',
          'status_active' => 'required|in:active,inactive'
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $email = $request->input("email");
        $password = $request->input("password");
        $status_active = $request->input("status_active");

        $hashPwd = Hash::make($password);

        $data = [
            "email" => $email,
            "password" => $hashPwd,
            "status_active" => $status_active
        ];

        if (User::create($data)) {
            $out = [
                "message" => "Success",
                "code"    => 200,
            ];
        } else {
            $out = [
                "message" => "Failed to Create User",
                "code"   => 500,
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function userUpdate(Request $request, $id){
        $total_users = User::where("id",$id)->count();
        if($total_users !==0){
          $validate=\Validator::make($request->all(),
          array(
            'email' => [
                "required",
                "email",
                Rule::unique('users')->ignore($id),
                "max:255"
            ],
            'password' => 'required|min:6',
            'status_active' => 'required|in:active,inactive'
          ));

          if ($validate->fails()) {
              $out = [
                  "message" => $validate->messages(),
                  "code"    => 500
              ];
              return response()->json($out, $out['code']);
          }

          $email = $request->input("email");
          $password = $request->input("password");
          $status_active = $request->input("status_active");

          $hashPwd = Hash::make($password);

          $user = User::find($id);
          $user->email = $email;
          $user->password = $hashPwd;
          $user->status_active = $status_active;
          $user->save();

          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Update User",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function userDelete(Request $request, $id){
        $total_users = User::where("id",$id)->count();
        if($total_users !==0){
          $user = User::find($id);
          $user->delete();
          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Delete User",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }
}
