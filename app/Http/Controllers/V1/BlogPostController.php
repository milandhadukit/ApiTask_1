<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\V1\BlogPostService;
use Validator;
use App\Models\BlogPost;

class BlogPostController extends Controller
{
    private $blogPostService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->blogPostService = new BlogPostService();
    }

    /**
     * @OA\Post(
     *    path="/api/sore-blogpost",
     *    tags={"Blog Post"},
     *    summary="store",
     *    operationId="store ",
     *
     *    @OA\Parameter(
     *        name="title",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="body",
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

    public function storeBlogPost(Request $request)
    {
        try {
            $req = $request->all();
            $validator = Validator::make($req, [
                'title' => 'required|min:3',
                'body' => 'required|min:3',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $data = $this->blogPostService->storeBlogPost($req);
            // return $data;
            return $this->sendResponse('success', 'Add Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *    path="/api/update-blogpost/{id}",
     *    tags={"Blog Post"},
     *    summary="update",
     *    operationId="update ",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="title",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="body",
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
     *    security={{ "apiAuth": {}  ,"APIKEY":{}}},
     *)
     */

    public function updateBlogPost(Request $request, $id)
    {
        try {
            $req = $request->all();
            $validator = Validator::make($req, [
                'title' => 'required|min:3',
                'body' => 'required|min:3',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $updateBlogPost = BlogPost::where('id', $id)->first();
            if (empty($updateBlogPost)) {
                return response()->json('No Data Found', 404);
            }
            $data = $this->blogPostService->updateBlogPost($req, $id);
            // return $data;
            return $this->sendResponse('success', 'Update Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/delete-blogpost/{id}",
     *    tags={"Blog Post"},
     *    summary="Delete",
     *    operationId="Delete ",
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

    public function deleteBlogPost($id)
    {
        try {
            $deleteBlogPost = BlogPost::where('id', $id)->first();
            if (empty($deleteBlogPost)) {
                return response()->json('No Data Found', 404);
            }
            $data = $this->blogPostService->deleteBlogPost($id);
            return $this->sendResponse('success', 'Delete Successfully ');
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }
    /**
     * @OA\Get(
     *    path="/api/view-blogpost",
     *    tags={"Blog Post"},
     *    summary="View",
     *    operationId="View",
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
     *    @OA\Parameter(
     *        name="SearchTitle",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="SearchBody",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="order_by",
     *        in="query",
     *        required=false,
     *        @OA\Schema(
     *            type="string"
     *        )
     *    ),
     *    @OA\Parameter(
     *        name="sort_by",
     *        in="query",
     *        required=false,
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
     *    security={{ "apiAuth": {}  ,"APIKEY":{}}},
     *)
     */

    public function viewBlogPost(Request $request)
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

            $data = $this->blogPostService->viewBlogPost($page, $pageLimit,$req);
            return $this->sendResponse('success', $data);
        } catch (\Exception $e) {
            return $this->sendError('error', $e->getMessage());
        }
    }
}
