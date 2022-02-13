<?php


namespace App\Http\Services;


use App\Jobs\SendMail;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function create($request)
    {
        $qty = (int)$request->input('num_product');
        $product_id = (int)$request->input('product_id');

        if ($qty <= 0 || $product_id <= 0) {
            Session::flash('error', 'Số lượng hoặc Sản phẩm không chính xác');
            return false;
        }
        //lấy toàn bộ thông tin của cart
        $carts = Session::get('carts');
        // Nếu giỏ hàng chưa có thì tạo giỏ hàng
        if (is_null($carts)) {
            Session::put('carts', [
                //Truyền vào sản phẩm và số lượng
                $product_id => $qty
            ]);
            return true;
        }

        $exists = Arr::exists($carts, $product_id);
        // Kiểm tra nếu true
        if ($exists) {
            // Lấy ra id_SP + Số lượng
            $carts[$product_id] = $carts[$product_id] + $qty;
            Session::put('carts', $carts);
            return true;
        }

        $carts[$product_id] = $qty;
        Session::put('carts', $carts);

        return true;
    }

    public function getProduct()
    {
        //lấy toàn bộ thông tin của cart
        $carts = Session::get('carts');
        // Nếu giỏ hàng chưa có thì return về 1 mảng trống
        if (is_null($carts)) return [];

        $productId = array_keys($carts); // Lấy ra toàn bộ KEY (product_id) của cart
        return Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->whereIn('id', $productId)//Kiểm tra giá trị id nằm trong mảng $productId
            ->get();
    }

    public function update($request)
    {
        Session::put('carts', $request->input('num_product'));

        return true;
    }
    // Xóa giỏ hàng
    public function remove($id)
    {
        $carts = Session::get('carts');
        unset($carts[$id]);//Xóa phần tử

        Session::put('carts', $carts);//Cập nhật lại giỏ hàng
        return true;
    }

    public function addCart($request)
    {
        try {
            DB::beginTransaction();//Bắt đầu các HĐ trên CSDL
            //lấy toàn bộ thông tin của cart
            $carts = Session::get('carts');

            if (is_null($carts))
                return false;
            //Lấy thông tin của khách hàng
            $customer = Customer::create([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'email' => $request->input('email'),
                'content' => $request->input('content')
            ]);

            $this->infoProductCart($carts, $customer->id);

            DB::commit();//Commit dữ liệu khi hoàn thành kiểm tra
            Session::flash('success', 'Đặt Hàng Thành Công');

            #Queue
            SendMail::dispatch($request->input('email'))->delay(now()->addSeconds(2));

            Session::forget('carts');

        } catch (\Exception $err) {
            DB::rollBack();//Gặp lỗi quay lại phần đặt hàng
            Session::flash('error', 'Đặt Hàng Lỗi, Vui lòng thử lại sau');
            return false;
        }

        return true;
    }

    protected function infoProductCart($carts, $customer_id)
    {
        $productId = array_keys($carts);
        $products = Product::select('id', 'name', 'price', 'price_sale', 'thumb')
            ->where('active', 1)
            ->whereIn('id', $productId)
            ->get();

        $data = [];
        // Lấy thông tin của giỏ hàng
        foreach ($products as $product) {
            $data[] = [
                'customer_id' => $customer_id,
                'product_id' => $product->id,
                'pty'   => $carts[$product->id],
                'price' => $product->price_sale != 0 ? $product->price_sale : $product->price
            ];
        }

        return Cart::insert($data);
    }

    public function getCustomer()
    {
        return Customer::orderByDesc('id')->paginate(15);
    }

    public function getProductForCart($customer)
    {
        return $customer->carts()->with(['product' => function ($query) {
            $query->select('id', 'name', 'thumb');
        }])->get();
    }
}