<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use Illuminate\Support\Str;

/**
 *  @OA\Info(
 *      description="L5 Swagger OpenApi description",
 *      version="1.0.0",
 *      title="Blog OpenApi Documentation",
 * )
* @OA\SecurityScheme(
 *	 securityScheme="APIKEY",
 *   description="Default Token :static",
 *	 type="apiKey",
 *	 in="header",
 *	 name="APIKEY"
 * )
 **/




class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
 

    /**
     * @OA\Post(
     *    path="/api/login",
     *    tags={"Auth"},
     *    summary="Login",
     *    operationId="login",
     *
     *    @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *    security={{ "APIKEY":{} }},
     *)
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!($token = auth()->attempt($validator->validated()))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }

    /**
     * @OA\Post(
     *    path="/api/register",
     *    tags={"Auth"},
     *    summary="register",
     *    operationId="register",
     *
     *    @OA\Parameter(
     *        name="name",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password confirmation",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *    security={{ "apiAuth": {}  ,"APIKEY":{} }},
     *)
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(
            array_merge($validator->validated(), [
                'password' => bcrypt($request->password),
            ])
        );
        return response()->json(
            [
                'message' => 'User successfully registered',
                'user' => $user,
            ],
            200
        );
    }

    /**
	     * @OA\Post(
	     *    path="/api/logout",
	     *    tags={"Auth"},
	     *    summary="Logout",
	     *    operationId="logout",

	     *    @OA\Response(
	     *        response=200,
	     *        description="Success",
	     *        @OA\MediaType(
	     *            mediaType="application/json",
	     *        )
	     *    ),
	     *    @OA\Response(
	     *        response=401,
	     *        description="Unauthorized"
	     *    ),
	     *    @OA\Response(
	     *        response=400,
	     *        description="Invalid request"
	     *    ),
	     *    @OA\Response(
	     *        response=403,
	     *        description="Unauthorized Access"
	     *    ),
	     *    @OA\Response(
	     *        response=404,
	     *        description="not found"
	     *    ),
	     *   security={{ "apiAuth": {}  ,"APIKEY":{} }}    
	     *)
	     */

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
	     * @OA\Get(
	     *    path="/api/user-profile",
	     *    tags={"Auth"},
	     *    summary="User Profile",
	     *    operationId="User Profile",

	     *    @OA\Response(
	     *        response=200,
	     *        description="Success",
	     *        @OA\MediaType(
	     *            mediaType="application/json",
	     *        )
	     *    ),
	     *    @OA\Response(
	     *        response=401,
	     *        description="Unauthorized"
	     *    ),
	     *    @OA\Response(
	     *        response=400,
	     *        description="Invalid request"
	     *    ),
	     *    @OA\Response(
	     *        response=403,
	     *        description="Unauthorized Access"
	     *    ),
	     *    @OA\Response(
	     *        response=404,
	     *        description="not found"
	     *    ),
	     *   security={{ "apiAuth": {}  ,"APIKEY":{} }}    
	     *)
	     */

    public function userProfile()
    {
        return $this->sendResponse('success', auth()->user());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>
                auth()
                    ->factory()
                    ->getTTL() * 540,
            'user' => auth()->user(),
        ]);
    }

    /**
     * @OA\Post(
     *    path="/api/change-password",
     *    tags={"Auth"},
     *    summary="Change-Password",
     *    operationId="Change-Password",
     *
     *    @OA\Parameter(
     *        name="old_password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="new_password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password_confirmation",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Unauthorized Access"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *   security={{ "apiAuth": {}  ,"APIKEY":{}}}
     *)
     */

    public function changepassword(Request $request)
    {
        $req = $request->all();
        $validator = Validator::make($req, [
            'old_password' => 'required|min:6',
            'new_password' => 'required|same:password_confirmation',
            'password_confirmation' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            $request['email'] = Auth::user()->email;
            $request['password'] = $request->old_password;
            $credentials = $request->only('email', 'password');

            if ($token = auth()->attempt($credentials)) {
                $data = ['password' => bcrypt($request->new_password)];

                $res = User::where('email', Auth::user()->email)->update($data);
                if ($res > 0) {
                    /* After change password login in again */
                    $request['password'] = $request->new_password;
                    $credentials = $request->only('email', 'password');

                    if ($token = auth()->attempt($credentials)) {
                        return $this->sendResponse(
                            'success',
                            'Password Changed Successfull'
                        );
                    } else {
                        $errors['email'] = ['Something went wrong'];
                        return response()->json(['errors' => $errors], 422);
                    }
                } else {
                    $errors['password'] = ['Please enter valid old password'];
                    return response()->json(['errors' => $errors], 422);
                }
            } else {
                $errors['password'] = ['Please enter valid old password'];
                return response()->json(['errors' => $errors], 422);
            }
        }
    }

    // second way to change password
    public function passwordChnage(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|same:password_confirmation',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json('Old Password Doesnt match', 422);
        }
        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Password Changed Successfull',
        ]);
    }

    /**
     * @OA\Put(
     *    path="/api/update-profile/{id}",
     *    tags={"Auth"},
     *    summary="Update Profile",
     *    operationId="Update Profile",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="name",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *    security={{ "apiAuth": {} ,"APIKEY":{} }},
     *)
     */

    public function updateProfile(Request $request, $id)
    {
        $req = $request->all();
        $validator = Validator::make($req, [
            'name' => 'required|string|between:2,100',
            // 'email' => 'required|string|email|max:100|unique:users'|Rule::unique('users')->ignore(auth()->user()->id),
            'email' => [
                'required',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = User::where('id', $id)->first();
        if (empty($userId)) {
            return response()->json('No Data Found', 404);
        }
        $updateUser = [
            'name' => $req['name'],
            'email' => $req['email'],
        ];
        $updateProfile = User::find($id);
        $updateProfile->update($updateUser);
        return $this->sendResponse('success', 'Update Successfully ');
    }

    /**
     * @OA\Post(
     *    path="/api/forget-password",
     *    tags={"Auth"},
     *    summary="Forget-Password",
     *    operationId="Forget-Password",
     *
     *    @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Unauthorized Access"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *   security={{ "apiAuth": {} ,"APIKEY":{} }}
     *)
     */

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('email.forgetPassword', ['token' => $token], function (
            $message
        ) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json([
            'status' => 200,
            'message' => 'We have e-mailed your password reset link!',
        ]);
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

    /**
     * @OA\Post(
     *    path="/api/reset-password",
     *    tags={"Auth"},
     *    summary="Reset-Password",
     *    operationId="Reset-Password",
     *
     *    @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="password_confirmation ",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *        )
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized"
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Invalid request"
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Unauthorized Access"
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="not found"
     *    ),
     *   security={{ "apiAuth": {} ,"APIKEY":{} }}
     *)
     */

    public function submitResetPasswordForm(Request $request)
    {
        $req = $request->all();
        $validator = Validator::make($req, [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6',
            // 'password_confirmation' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        //   $updatePassword = DB::table('password_resets')
        //                       ->where([
        //                         'email' => $request->email,
        //                         'token' => $request->token
        //                       ])
        //                       ->first();

        //   if(!$updatePassword){

        //       return response()->json( 'Invalid Data!',422);
        //   }


        $token = DB::table('password_resets')
         ->where('email','=',$request->email)
         ->where('created_at','>',Carbon::now()->subHours(1))->latest()
         ->first();
            
         if(empty($token))
         {
            return 'NO Found';
         }
     
            $user = User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_resets')
                ->where(['email' => $request->email])
                ->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Your password has been changed!',
            ]);
     
    }
}
