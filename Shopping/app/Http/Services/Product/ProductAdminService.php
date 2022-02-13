<?php


namespace App\Http\Services\Product;


use App\Models\Menu;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class ProductAdminService
{
    // Đổ dữ liệu ra option
    public function getMenu()
    {
        return Menu::where('active', 1)->get();
    }
    // Kiểm tra giá gốc phải lớn hơn giảm giá
    protected function isValidPrice($request)
    {
        if ($request->input('price') != 0 && $request->input('price_sale') != 0
            && $request->input('price_sale') >= $request->input('price')) 
        {
            Session::flash('error', 'Giá giảm phải nhỏ hơn giá gốc');
            return false;
        }

        if ($request->input('price_sale') != 0 && (int)$request->input('price') == 0) {
            Session::flash('error', 'Vui lòng nhập giá gốc');
            return false;
        }

        return  true;
    }
    // Hàm thêm
    public function insert($request)
    {
        $isValidPrice = $this->isValidPrice($request);
        if ($isValidPrice === false) return false;

        try {
            $request->except('_token');
            Product::create($request->all());

            Session::flash('success', 'Thêm sản phẩm thành công');
        } catch (\Exception $err) {
            Session::flash('error', 'Thêm sản phẩm lỗi');
            \Log::info($err->getMessage());
            return  false;
        }

        return  true;
    }
    // Sắp xếp theo thứ tự và phân trang
    public function get()
    {
        return Product::with('menu')->paginate(10);
    }
    // Hàm cập nhật
    public function update($request, $product)
    {
        $isValidPrice = $this->isValidPrice($request);
        if ($isValidPrice === false) return false;

        try {
            $product->fill($request->input());
            $product->save();
            Session::flash('success', 'Cập nhật thành công');
        } catch (\Exception $err) {
            Session::flash('error', 'Có lỗi cập nhật không thành công');
            \Log::info($err->getMessage());
            return false;
        }
        return true;
    }
    // Hàm xóa
    public function delete($request)
    {
        // gửi yêu cầu đến id và trả về bản ghi đầu tiên
        $product = Product::where('id', $request->input('id'))->first();
        // Kiểm tra nếu đúng thì delete
        
        if ($product) {
            $product->delete();
            return true;
        }

        return false;
    }
}