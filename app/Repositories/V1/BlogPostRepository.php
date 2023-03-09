<?php
namespace App\Repositories\V1;
use App\Repositories\BaseRepository\V1;
use Illuminate\Support\Facades\DB;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class BlogPostRepository extends BaseRepository
{
    public function storeBlogPost($req)
    {
        $slug = $req['title'];
        $num = Str::random(1);
        $uniqueSlug = BlogPost::where('title', $slug)->count();

        if ($uniqueSlug > 0) {
            $slug = Str::slug($slug . '-' . $num);
            // isset($uniqueSlug) && ($slug = Str::slug($slug . $num));
        }

        $blogPost = [
            'title' => $req['title'],
            'author' => auth()->user()->id,
            'body' => $req['body'],
            'slug' => Str::slug($slug),
        ];

        $blogPostStore = BlogPost::create($blogPost);
        return $blogPostStore;
    }
    public function updateBlogPost($req, $id)
    {
        $blogPost = [
            'title' => $req['title'],
            'body' => $req['body'],
        ];
        $updateBlogPost = BlogPost::where('id', $id)->update($blogPost);
        return $updateBlogPost;
    }
    public function deleteBlogPost($id)
    {
        $deleteData = BlogPost::find($id);
        return $deleteData->delete();
    }
    public function viewBlogPost($page, $pageLimit, $req)
    {
        $viewData = BlogPost::leftJoin(
            'users',
            'users.id',
            '=',
            'blog_posts.author'
        )
            ->select('blog_posts.*', 'users.name as author')
            ->skip($page)
            ->take($pageLimit)
            ->get();

        //  Filtering search on specific colomn

        if (request('SearchTitle')) {
            $searchData = BlogPost::select('title')
                ->where('title', 'LIKE', '%' . $req['SearchTitle'] . '%')
                ->get();

            if (count($searchData) < 1) {
                return 'no Data Found';
            } else {
                return $searchData;
            }
        }

        if (request('SearchBody')) {
            $searchDataBody = BlogPost::select('body')
                ->where('body', 'LIKE', '%' . $req['SearchBody'] . '%')

                ->get();

            if (count($searchDataBody) < 1) {
                return 'No Data Found';
            } else {
                return $searchDataBody;
            }
        }

      
        if(!empty($req['sort_by']) ||!empty($req['order_by'])) {

            $orderType = (isset($req['order_by'])) ? $req['order_by'] : 'asc';
            $orderBy= (isset($req['sort_by'])) ? $req['sort_by'] : 'id';
            $products = BlogPost::orderBy($orderBy,$orderType)->skip($page)->take($pageLimit)->get();
            return $products;

        }
      
        // return query
        return $viewData;
    }
}
