<?php

namespace App\Repositories\Product;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    /**
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function index($search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @return Product
     */
    public function findOrFail($productId): Product;


    /**
     * @param array $attributes
     * @return Product
     */
    public function store(array $attributes): Product;


    /**
     * @param array $attributes
     * @param $productId
     * @return Product
     */
    public function update(array $attributes, $productId): Product;


    /**
     * @param $productId
     * @param $forceDelete
     * @return Product
     * @throws Exception
     */
    public function destroyOrFail($productId, $forceDelete = false): Product;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getRelationships($productId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getTags($productId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductClassifications($productId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductComments($productId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getProductFeatures($productId, $search = '', $filter = null): LengthAwarePaginator;


    /**
     * @param $productId
     * @param $search
     * @param $filter
     * @return LengthAwarePaginator
     */
    public function getPurchaseOrders($productId, $search = '', $filter = null): LengthAwarePaginator;
}
