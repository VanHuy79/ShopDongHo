<?php


namespace App\Http\View\Composers;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CartComposer
{
    protected $users;

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $carts = Session::get('carts');
        // Nếu giỏ hàng chưa có thì return rỗng
        if (is_null($carts)) return [];

        $productId = array_keys($carts);// Lấy ra toàn bộ KEY (product_id) của cart
        $products = Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->whereIn('id', $productId)//Kiểm tra giá trị id nằm trong mảng $productId
            ->get();

        $view->with('products', $products);
    }
}
