<?php

namespace App\Http\Controllers;

use App\User;
use App\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //users
    public function userLogin(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'email' => 'required|email',
          'password' => 'required|min:6'
        ));

        $email = $request->input("email");
        $password = $request->input("password");

        $user = User::where("status_active","active")->where("email", $email)->first();

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
            return response()->json($out, $out['code']);
        }else if (!$user) {
            $out = [
                "message" => "Email not Found !",
                "code"    => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
            return response()->json($out, $out['code']);
        }

        if (Hash::check($password, $user->password)) {
            $newtoken  = $this->generateToken();

            $user->update([
                'token' => $newtoken
            ]);

            $out = [
                "message" => "Success",
                "code"    => 200,
                "result"  => [
                    "user_id" => $user->id,
                    "token" => $newtoken,
                ]
            ];
        } else {
            $out = [
                "message" => "Email and Password didn't Match !",
                "code"    => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function userChangePassword(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'password' => 'required|min:6'
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $id = $request->input("user_id");
        $password = $request->input("password");
        $hashPwd = Hash::make($password);

        $total_users = User::where("id",$id)->count();
        if($total_users !==0){
            $user = User::find($id);
            $user->password = $hashPwd;
            $user->save();

            $out = [
                "message" => "Success",
                "code"    => 200,
            ];
        } else {
            $out = [
                "message" => "Failed to Update User",
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
    //members

    public function memberCreate(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'fullname' => 'required|min:5',
          'address' => 'min:12',
          'phone' => 'required|unique:members|min:12',
          'email' => 'required|email|unique:members|max:255',
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $gamecode = date("d").date("Y").rand(10,100);
        $email = $request->input("email");
        $fullname = $request->input("fullname");
        $address = $request->input("address");
        $phone = $request->input("phone");

        $data = [
          "gamecode" => $gamecode,
          "email" => $email,
          "fullname" => $fullname,
          "address" => $address,
          "phone" => $phone,
          "status_active" => "active"
        ];

        if (Member::create($data)) {
            $user = Member::where("status_active","active")->where("email", $email)->first();

            if (!$user) {
                $out = [
                    "message" => "Email not Found !",
                    "code"    => 500,
                    "result"  => [
                        "token" => null,
                    ]
                ];
                return response()->json($out, $out['code']);
            }

            $newtoken  = $this->generateToken();

            $user->update([
                'token' => $newtoken
            ]);
            $out = [
                "message" => "Success",
                "code"    => 200,
                "result"  => [
                    "token" => $newtoken,
                    "user_id" => $user->id,
                    "gamecode" => $gamecode,
                    "email" => $email,
                    "fullname" => $fullname,
                    "address" => $address,
                    "phone" => $phone,
                    "status_active" => "active"
                ]
            ];
        } else {
            $out = [
                "message" => "Failed to Create Member",
                "code"   => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function memberLogin(Request $request){


        $email = $request->input("email");
        $gamecode = $request->input("gamecode");
        
        if($gamecode!==null){
            $validate=\Validator::make($request->all(),
            array(
            'gamecode' => 'required',
            ));
            $user = Member::where("status_active","active")->where("gamecode", $gamecode)->first();
        }else{
            $validate=\Validator::make($request->all(),
            array(
            'email' => 'required|email',
            ));
            $user = Member::where("status_active","active")->where("email", $email)->first();
        }


        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
            return response()->json($out, $out['code']);
        }else if (!$user) {
            $out = [
                "message" => "Email / Game Code not Found !",
                "code"    => 500,
                "result"  => [
                    "token" => null,
                ]
            ];
            return response()->json($out, $out['code']);
        }

        $newtoken  = $this->generateToken();

        $user->update([
            'token' => $newtoken
        ]);

        $out = [
            "message" => "Success",
            "code"    => 200,
            "result"  => [
                "user_id" => $user->id,
                "token" => $newtoken,
            ]
        ];

        return response()->json($out, $out['code']);
    }

    function generateToken($length = 80)
    {
        $chars = '012345678dssd9abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lengthChars = strlen($chars);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $lengthChars - 1)];
        }
        return $str;
    }
}
