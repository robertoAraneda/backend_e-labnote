<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;
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
     * @OA\Post(
     *      path="/auth/signup",
     *      tags={"Auth"},
     *      summary="Devuelve un usuario creado",
     *      description="Devuelve un usuario creado",
    *       @OA\RequestBody(
     *    		@OA\MediaType(
     *    			mediaType="application/json",
     *    			@OA\Schema(
     *    				 @OA\Property(property="names",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *    				 @OA\Property(property="lastname",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *     			 @OA\Property(property="mother_lastname",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *     			 @OA\Property(property="rut",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *     			 @OA\Property(property="email",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *     	        @OA\Property(property="password",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *    			),
     *    		),
     *    	),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
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

            return response()->json($user->fresh(), 201);
        } catch (\Exception $ex) {
            return $this->response->exception($ex->getMessage());
        }
    }


    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Auth"},
     *      summary="Devuelve un token jwt",
     *      description="Devuelve un token de autenticación",
     *       @OA\RequestBody(
     *    		@OA\MediaType(
     *    			mediaType="application/json",
     *    			@OA\Schema(
     *    				 @OA\Property(property="rut",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *    				 @OA\Property(property="password",
     *    					type="string",
     *    					example="",
     *    					description=""
     *    				),
     *    			),
     *    		),
     *    	),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function login(AuthRequest $request): JsonResponse
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

        /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        $searchUser = User::find($user->id);

        return response()->json([
      'user' => $searchUser
    ], 200);
    }
}
