<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Menu\CreateFormRequest;
use App\Http\Services\Menu\MenuService;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    protected $menuService;

    public function __construct(MenuService $menuService) {
        $this->menuService = $menuService;
    }
    // Hàm thêm
    public function create() {
        return view('admin.menu.add', [
            'title' => "Thêm Danh Mục Mới",
            'menus' => $this->menuService->getParent(),
        ]);
    }

    public function store(CreateFormRequest $request) {
        $this->menuService->create($request);
        return redirect()->back();
    }
    // Trang danh sách danh mục
    public function index() {
        return view('admin.menu.list', [
            'title' => 'Danh Sách Danh Mục',
            'menus' => $this->menuService->getAll(),
        ]);
    }
    // Hàm xóa
    public function destroy(Request $request) {
        $result = $this->menuService->destroy($request);

        if($result) {
            return response()->json([
                'error' =>false,
                'message' => 'Xóa thành công'
            ]);
        }
        return response()->json([
            'error' => true,
        ]);
    }
    // Show ra trang sửa danh mục
    public function show(Menu $menu) {
        return view('admin.menu.edit', [
            'title' => "Sửa Danh Mục". $menu->name,
            'menu' => $menu,
            'menus' => $this->menuService->getParent(),
        ]);
    }

    public function update(Menu $menu, CreateFormRequest $request)
    {
        $this->menuService->update($request, $menu);

        return redirect('/admin/menus/list');
    }
}