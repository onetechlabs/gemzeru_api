<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;

class GameController extends Controller
{
    public function __construct(Request $request){
        $this->perpage = 10;
        $this->page = (null !== $request->input("page"))? (int)$request->input("page"):1;
        $this->spage = ($this->page > 1) ? ($this->page * $this->perpage) - $this->perpage : 0;
    }

    public function activeCategories(Request $request){
        $categories = DB::select(DB::raw('SELECT DISTINCT `category` FROM `games` LIMIT '.$this->spage.', '.$this->perpage.''));
        $total_category = Game::distinct("category")->count();
        $total_page = ceil($total_category / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_category,
              "current_page" => $this->page,
              "records"=>$categories
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function games(Request $request){
        $games = Game::limit($this->perpage)->offset($this->spage)->orderBy('id', 'desc')->get();
        $total_game = Game::all()->count();
        $total_page = ceil($total_game / $this->perpage);
        $out = [
            "message" => "Success",
            "data"    => [
              "total_page"=>$total_page,
              "total_data"=>$total_game,
              "current_page" => $this->page,
              "records"=>$games
            ],
            "code"    => 200
        ];
        return response()->json($out, $out['code']);
    }

    public function gameShow(Request $request, $id){
        $total_game = Game::where("id",$id)->count();
        if($total_game !==0){
          $game = Game::where("id",$id)->get();
          $out = [
              "message" => "Success",
              "data"    => [
                "record"=> $game
              ],
              "code"    => 200
          ];
        }else{
          $out = [
              "message" => "Failed to Show Game",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function gameCreate(Request $request){
        $validate=\Validator::make($request->all(),
        array(
          'title' => 'required|min:5',
          'cover_image' => 'required',
          'android_package' => 'required',
          'version' => 'required',
          'bundle_version' => 'required',
          'category' => 'required',
          'description' => 'min:10',
          'status_active' => 'required|in:active,inactive'
        ));

        if ($validate->fails()) {
            $out = [
                "message" => $validate->messages(),
                "code"    => 500
            ];
            return response()->json($out, $out['code']);
        }

        $title = $request->input("title");
        $cover_image= $request->input("cover_image");
        $android_package = $request->input("android_package");
        $version = $request->input("version");
        $bundle_version = $request->input("bundle_version");
        $category = $request->input("category");
        $description = $request->input("description");
        $status_active = $request->input("status_active");

        $data = [
            "title" => ucwords($title),
            "cover_image" => $cover_image,
            "android_package" => $android_package,
            "version" => $version,
            "bundle_version" => $bundle_version,
            "category" => ucwords($category),
            "description" => $description,
            "status_active" => $status_active
        ];

        if (Game::create($data)) {
            $out = [
                "message" => "Success",
                "code"    => 200,
            ];
        } else {
            $out = [
                "message" => "Failed to Create Game",
                "code"   => 500,
            ];
        }

        return response()->json($out, $out['code']);
    }

    public function gameUpdate(Request $request, $id){
        $total_game = Game::where("id",$id)->count();
        if($total_game !==0){
          $validate=\Validator::make($request->all(),
          array(
            'title' => 'required|min:5',
            'cover_image' => 'required',
            'android_package' => 'required',
            'version' => 'required',
            'bundle_version' => 'required',
            'category' => 'required',
            'description' => 'min:10',
            'status_active' => 'required|in:active,inactive'
          ));

          if ($validate->fails()) {
              $out = [
                  "message" => $validate->messages(),
                  "code"    => 500
              ];
              return response()->json($out, $out['code']);
          }

          $title = $request->input("title");
          $cover_image= $request->input("cover_image");
          $android_package = $request->input("android_package");
          $version = $request->input("version");
          $bundle_version = $request->input("bundle_version");
          $category = $request->input("category");
          $description = $request->input("description");
          $status_active = $request->input("status_active");

          $game = Game::find($id);
          $game->title = ucwords($title);
          $game->cover_image = $cover_image;
          $game->android_package = $android_package;
          $game->version = $version;
          $game->bundle_version = $bundle_version;
          $game->category = ucwords($category);
          $game->description = $description;
          $game->status_active = $status_active;
          $game->save();

          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Update Game",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }

    public function gameDelete(Request $request, $id){
        $total_game = Game::where("id",$id)->count();
        if($total_game !==0){
          $game = Game::find($id);
          $game->delete();
          $out = [
              "message" => "Success",
              "code"    => 200,
          ];
        }else{
          $out = [
              "message" => "Failed to Delete Game",
              "code"   => 500,
          ];
        }
        return response()->json($out, $out['code']);
    }
}
