<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Simethris API Documentation",
     *      description="API untuk mendapatkan informasi SKL",
     *      @OA\Contact(
     *          email="sultanjawer.work@gmail.com"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Simethris API Server"
     * )

     *
     * @OA\Tag(
     *     name="Simethris",
     *     description="API Endpoints"
     * )
     */
class AuthController extends Controller
{
    /**
     * 
     * @OA\Post(
     *      path="/getToken",
     *      operationId="getToken",
     *      tags={"Get Access Token"},
     *      summary="Post your username and password to get token. Use the token in the 'Authorization' header like so 'Bearer YOUR_TOKEN'",
     *      description="get token for Access API",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *          mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="username", type="string", example="user"),
     *                  @OA\Property(property="password", type="string", example="mypass")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent()
     *          
     *      ),
     *      @OA\Response(
     *          response=422, 
     *          description="The provided credentials are incorrect."
     *      ),
     *  )
     */   
    public function getToken(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        
        $credentials = request(['username', 'password']);
        if (!auth()->attempt($credentials)) {
            
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'password' => [
                        'Invalid credentials'
                    ],
                ]
            ], 422);
        }

        $user = User::where('username', $request->username)->first();
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $authToken,
        ]);
    }
}