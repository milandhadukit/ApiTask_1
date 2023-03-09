<?php

namespace App\Services\V1;
use App\Repositories\V1\ProductRepository;
use App\Services\V1\BaseService;

class ProductService extends BaseService
{
    protected $productRepository;
    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }
    public function storeProduct($req)
    {
        return $this->productRepository->storeProduct($req);
    }
    public function updateProduct($req,$id)
    {
        return $this->productRepository->updateProduct($req,$id);
    }
    public function deleteProduct($id)
    {
        return $this->productRepository->deleteProduct($id);

    }
    public function viewProduct($req,$page, $pageLimit)
    {
        return $this->productRepository->viewProduct($req,$page, $pageLimit);
    }
}