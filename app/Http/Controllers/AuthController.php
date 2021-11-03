<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Response;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Resources\Collections\PermissionResourceCollection;

class AuthController extends Controller
{

    protected Response $response;

    public function __construct(Response $response = null)
    {
        $this->response = $response;
    }

    protected function validateData($request)
    {
        return Validator::make($request, [
      'rut' => 'required|max:12|string',
      'names' => 'required|max:200|string',
      'lastname' => 'required|max:200|string',
      'mother_lastname' => 'required|max:200|string',
      'email' => 'required|max:255|email|unique:users',
      'password' => 'required|string'
    ]);
    }

    public function signup(Request $request)
    {
        try {
            if (!request()->isJson()) {
                return $this->response->badRequest();
            }

            $validate = $this->validateData(request()->all());

            if ($validate->fails()) {
                return $this->response->customMessageResponse(($validate->errors()), 406);
            }

            $user = new User([
                'rut' => $request->rut,
                'names' => $request->names,
                'lastname' => $request->lastname,
                'mother_lastname' => $request->mother_lastname,
                'email' => $request->email,
                'password' => bcrypt($request->password)
              ]);

            $user->save();

            return response()->json($user->fresh(), 201);
        } catch (\Exception $ex) {
            return $this->response->exception($ex->getMessage());
        }
    }

    public function login(AuthRequest $request)
    {

        if(!$request->validated()){
            return response()->json($request->messages(), 400);
        }

        $credentials = request(['rut', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
        'message' => 'Unauthorized'
      ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response()->json([
      'access_token' => $tokenResult->accessToken,
      'token_type' => 'Bearer',
      'expires_at' => Carbon::parse(
          $tokenResult->token->expires_at
      )->toDateTimeString()
    ]);
    }


    public function user(Request $request)
    {
        $user = $request->user();

        $searchUser = User::find($user->id);

        $nameRol = $searchUser->getRoleNames()[0];

        $role = Role::where('name', $nameRol)->first();

        return response()->json([
            'user' => [
                'id' => $searchUser->id,
                'rut' => $searchUser->rut,
                'names' => $searchUser->names,
                'lastname' => $searchUser->lastname,
                'mother_lastname' => $searchUser->mother_lastname,
                'email' => $searchUser->email,
                'active' => $searchUser->active,
                'roles' => $searchUser->roles,
                'laboratory_id' => $searchUser->laboratory_id,
                ],
            'role' => $role,
            'permissions' => new PermissionResourceCollection($searchUser->getAllPermissions())
    ], 200);
    }
}
