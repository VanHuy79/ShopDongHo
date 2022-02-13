<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Services\CartService;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $result = $this->cartService->create($request);
        // Kiểm tra nếu false
        if ($result === false) {
            // Trả về create
            return redirect()->back();
        }
        // Ko thì trả về view carts
        return redirect('/carts');
    }

    public function show()
    {
        $products = $this->cartService->getProduct();

        return view('carts.list', [
            'title' => 'Giỏ Hàng',
            'products' => $products,
            'carts' => Session::get('carts')
        ]);
    }
    // Cập nhật giỏ hàng
    public function update(Request $request)
    {
        $this->cartService->update($request);

        return redirect('/carts');
    }
    // Xóa giỏ hàng
    public function remove($id = 0)
    {
        $this->cartService->remove($id);

        return redirect('/carts');
    }
    
    public function addCart(Request $request)
    {
        $this->cartService->addCart($request);

        return redirect()->back();
    }
}