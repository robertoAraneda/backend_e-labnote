<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Response;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{

      /**
   * Property for make a response.
   *
   * @var  App\Helpers\Response  $response
   */
    protected $response;

    public function __construct(Response $response = null)
    {
        $this->response = $response;
    }

    /**
     * Validate the description field.
     *
     * @param  \Illuminate\Http\Request  $request
     */
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


    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
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

            return $this->response->created($user->fresh());
        } catch (\Exception $ex) {
            return $this->response->exception($ex->getMessage());
        }
    }


    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
      'rut' => 'required|string',
      'password' => 'required|string',
      'remember_me' => 'boolean'
    ]);

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
}
