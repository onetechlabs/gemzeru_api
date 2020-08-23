<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function __construct(Request $request){
        $this->perpage = 10;
        $this->page = (null !== $request->input("page"))? (int)$request->input("page"):1;
        $this->spage = ($this->page > 1) ? ($this->page * $this->perpage) - $this->perpage : 0;
    }
    public function members(Request $request){
        $members = Member::limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
        $total_members = Member::all()->count();
        $total_page = ceil($total_members / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_members,
              "current_page" => $this->page,
              "records"=>$members
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function memberShow(Request $request, $id){
        $total_members = Member::where("id",$id)->count();
        if($total_members !==0){
          $member = Member::where("id",$id)->get();
          $out = [
              "message" => "Success",
              "data"    => [
                "record"=> $member
              ],
              "code"    => 200
          ];
        }else{
          $out = [
              "message" => "Failed to Show Member",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function memberCreate(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'gamecode' => 'required|unique:members|min:6',
          'fullname' => 'required|min:5',
          'address' => 'min:12',
          'phone' => 'required|unique:members|min:12',
          'email' => 'required|email|unique:members|max:255',
          'status_active' => 'required|in:active,inactive'
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $gamecode = $request->input("gamecode");
        $email = $request->input("email");
        $fullname = $request->input("fullname");
        $address = $request->input("address");
        $phone = $request->input("phone");
        $status_active = $request->input("status_active");

        $data = [
          "gamecode" => $gamecode,
          "email" => $email,
          "fullname" => $fullname,
          "address" => $address,
          "phone" => $phone,
          "status_active" => $status_active
        ];

        if (Member::create($data)) {
            $out = [
                "message" => "Success",
                "code"    => 200,
            ];
        } else {
            $out = [
                "message" => "Failed to Create Member",
                "code"   => 500,
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function memberUpdate(Request $request, $id){
        $total_members = Member::where("id",$id)->count();
        if($total_members !==0){
          $validate=\Validator::make($request->all(),
          array(
            'gamecode' => [
                "required",
                Rule::unique('members')->ignore($id),
                "max:6"
            ],
            'email' => [
                "required",
                "email",
                Rule::unique('members')->ignore($id),
                "max:255"
            ],
            'phone' => [
                "required",
                Rule::unique('members')->ignore($id),
                "max:12"
            ],
            'fullname' => 'required|min:5',
            'address' => 'min:12',
            'status_active' => 'required|in:active,inactive'
          ));

          if ($validate->fails()) {
              $out = [
                  "message" => $validate->messages(),
                  "code"    => 500
              ];
              return response()->json($out, $out['code']);
          }

          $gamecode = $request->input("gamecode");
          $email = $request->input("email");
          $fullname = $request->input("fullname");
          $address = $request->input("address");
          $phone = $request->input("phone");
          $status_active = $request->input("status_active");

          $member = Member::find($id);
          $member->gamecode = $gamecode;
          $member->fullname = $fullname;
          $member->address = $address;
          $member->phone = $phone;
          $member->email = $email;
          $member->status_active = $status_active;
          $member->save();

          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Update Member",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function memberDelete(Request $request, $id){
        $total_members = Member::where("id",$id)->count();
        if($total_members !==0){
          $member = Member::find($id);
          $member->delete();
          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Delete Member",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }
}
