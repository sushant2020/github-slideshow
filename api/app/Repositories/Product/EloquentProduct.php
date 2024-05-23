<?php

namespace App\Repositories\Product;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProduct implements ProductRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator
    {
        if (empty($search)) {
            return Product::paginate();
        }
        return Product::search($search)->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @return Product
     */
    public function findOrFail($productId): Product
    {
        return Product::whereId($productId)->firstOrFail();
    }


    /**
     * @param array $attributes
     * @return Product
     */
    public function store(array $attributes): Product
    {
        $product = new Product();
        $product->fill($attributes);
        $product->save();
        return $product;
    }


    /**
     * @param array $attributes
     * @param $productId
     * @return Product
     */
    public function update(array $attributes, $productId): Product
    {
        $product = $this->findOrFail($productId);
        $product->fill($attributes);
        $product->save();
        return $product;
    }


    /**
     * @param $productId
     * @param $forceDelete
     * @return Product
     * @throws Exception
     */
    public function destroyOrFail($productId, $forceDelete = false): Product
    {
        $product = $this->findOrFail($productId);
        if ($forceDelete) {
            $product->forceDelete();
        } else {
            $product->delete();
        }
        return $product;
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getRelationships($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->Relationships()->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getTags($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->Tags()->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductClassifications($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->ProductClassifications()->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductComments($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->ProductComments()->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductFeatures($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->ProductFeatures()->paginate()->appends('search', $search);
    }


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($productId, $search = '', $filter = null): LengthAwarePaginator
    {
        $product = $this->findOrFail($productId);
        return $product->PurchaseOrders()->paginate()->appends('search', $search);
    }
}
