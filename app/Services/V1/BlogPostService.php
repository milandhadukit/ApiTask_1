<?php

namespace App\Services\V1;
use App\Repositories\V1\BlogPostRepository;
use App\Services\V1\BaseService;

class BlogPostService extends BaseService
{
    protected $blogPostRepository;
    public function __construct()
    {
        $this->blogPostRepository = new BlogPostRepository();
    }

    public function storeBlogPost($req)
    {
        return $this->blogPostRepository->storeBlogPost($req);
    }
    public function updateBlogPost($req, $id)
    {
        return $this->blogPostRepository->updateBlogPost($req, $id);
    }
    public function deleteBlogPost($id)
    {
        return $this->blogPostRepository->deleteBlogPost($id);
    }
    public function viewBlogPost($page, $pageLimit, $req)
    {
        return $this->blogPostRepository->viewBlogPost($page, $pageLimit, $req);
    }
}
