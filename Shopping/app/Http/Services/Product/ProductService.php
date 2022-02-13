<?php


namespace App\Http\Services\Product;


use App\Models\Product;

class ProductService
{
    // Giới hạn 20 SP
    const LIMIT = 16;

    public function get($page = null)
    {
        //Lấy ra các trường
        return Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->orderByDesc('id') //Sắp xếp thep id
            ->when($page != null, function ($query) use ($page) {
                $query->offset($page * self::LIMIT);
            })
            ->limit(self::LIMIT)
            ->get();
    }

    public function show($id)
    {
        return Product::where('id', $id)
            ->where('active', 1)
            ->with('menu')
            ->firstOrFail();
    }
    // Sản phẩm liên quan
    public function more($id)
    {
        return Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->where('id', '!=', $id)
            ->orderByDesc('name')
            ->limit(4)
            ->get();
    }
}
