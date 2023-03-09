<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\V1\ProductService;
use Illuminate\Http\Request;
use Validator;

class ProductController extends Controller
{
    private $productService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->productService = new ProductService();
    }

    /**
     * @OA\Post(
     *    path="/api/store-product",
     *    tags={"Product"},
     *    summary="store product",
     *    operationId="store product",

     *    @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *           @OA\Schema(
     *   required={"image","name","description","price","category",},
     *             @OA\Property(
     *                      property="image",
     *                      type="file",
     *                      description="Validations: filetype=jpg,jpeg,png",
     *                 ),
     *             @OA\Property(
     *                property="name",
     *                type="string",
     *            ),
     *            @OA\Property(
     *                property="description",
     *                type="string",
     *            ),
     *  @OA\Property(
     *                property="price",
     *                type="string",
     *            ),
     *       @OA\Property(
     *                property="category",
     *                type="string",
     *            ),
     *            ),
     *      ),
     *   ),
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
    public function storeProduct(Request $request)
    {
        try {
            $req = $request->all();
            $validator = Validator::make($req, [
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'price' => 'required|integer',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $data = $this->productService->storeProduct($req);
            // return $data;
            return $this->sendResponse('success', 'Add Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *    path="/api/update-product/{id}",
     *    tags={"Product"},
     *    summary="Update product",
     *    operationId="Update product",
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *    @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *           @OA\Schema(
     *   required={"image","name","description","price","category",},
     *             @OA\Property(
     *                      property="image",
     *                      type="file",
     *                      description="Validations: filetype=jpg,jpeg,png",
     *                 ),
     *             @OA\Property(
     *                property="name",
     *                type="string",
     *            ),
     *            @OA\Property(
     *                property="description",
     *                type="string",
     *            ),
     *  @OA\Property(
     *                property="price",
     *                type="string",
     *            ),
     *       @OA\Property(
     *                property="category",
     *                type="string",
     *            ),
     *            ),
     *      ),
     *   ),
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

    public function updateProduct(Request $request, $id)
    {
        try {
            $req = $request->all();
            $validator = Validator::make($req, [
                'name' => 'required|min:3',
                'description' => 'required|min:3',
                'price' => 'required|integer',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $data = $this->productService->updateProduct($req, $id);

            return $this->sendResponse('success', 'Update Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/delete-product/{id}",
     *    tags={"Product"},
     *    summary="Delete Product",
     *    operationId="Delete Product",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
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
    public function deleteProduct($id)
    {
        try {
            $data = $this->productService->deleteProduct($id);
            return $this->sendResponse('success', 'Delete Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *    path="/api/view-product",
     *    tags={"Product"},
     *    summary="View Product",
     *    operationId="View Product",
     *    @OA\Parameter(
     *        name="page",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="perPage",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *       @OA\Parameter(
     *        name="filter_by",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
  
     *       @OA\Parameter(
     *        name="filter_to",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),


     * @OA\Parameter(
     *          name="filter_data",
     *         in="query",
     *         description=" Filter data",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"contains", "notContains", "equals","notEqual","startsWith","endsWith","inRange"},
     *             default="contains",
     *         )
     *      ),
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
     *    security={{ "apiAuth": {}  ,"APIKEY":{}}},
     *)
     */
    public function viewProduct(Request $request)
    {
        try {
            $req = $request->all();
            $validator = Validator::make($req, [
                'page' => 'nullable|integer',
                'perPage' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            // pagination
            $pageNumber =
            isset($request['page']) && !empty($request['page'])
            ? $request['page']
            : 1;
            $pageLimit =
            isset($request['perPage']) && !empty($request['perPage'])
            ? $request['perPage']
            : 5;
            $page = ($pageNumber - 1) * $pageLimit;

          

            $data = $this->productService->viewProduct($page, $pageLimit, $req);
            return $this->sendResponse('success', $data);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }
}
