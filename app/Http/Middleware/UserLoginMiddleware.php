<?php

namespace App\Http\Middleware;

use App\User;
use App\Member;
use Closure;

class UserLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if ($request->input('token')) {
          $checkByUserAdmin =  User::where('token', $request->input('token'))->first();
          $checkByUserMember =  Member::where('token', $request->input('token'))->first();
          if ($checkByUserAdmin) {
              return $next($request);
          } else if ($checkByUserMember) {
              return $next($request);
          } else {
              $out = [
                  "message" => "Token Invalid!",
                  "code"    => 401,
                  "result"  => [
                      "token" => null,
                  ]
              ];
              return response()->json($out, $out['code']);
          }
      } else {
          $out = [
              "message" => "Enter Token Please!",
              "code"    => 401,
              "result"  => [
                  "token" => null,
              ]
          ];
          return response()->json($out, $out['code']);
      }
    }
}
