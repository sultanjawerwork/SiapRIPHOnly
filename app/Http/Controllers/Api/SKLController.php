<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SKLResources;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Completed;


class SKLController extends Controller
{
    /**
     * @OA\Get(
     *      path="/getSKL/{npwp}",
     *      operationId="getSKL",
     *      tags={"SKL"},
     *      summary="Get list of completed skl",
     *      description="Returns list of skl",
     *      security={{"simethrisToken": {}}},
     *      @OA\Parameter(
     *          name="npwp",
     *          description="npwp importir",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function getSKL(Request $request)
    {

        $str = $request->npwp;
        // $npwp = str_replace(['.', '-', '/'], '', $str);
        return new SKLResources(Completed::where('npwp', '=', $str)->get()); 

    }
}