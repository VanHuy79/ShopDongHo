<?php

namespace App\Http\Services;

class UploadService 
{
    public function store($request)
    {
        if ($request->hasFile('file')) {
            try {
                // Lấy tên file
                $name = $request->file('file')->getClientOriginalName();
                // Phân chia theo thư mục 
                $pathFull = 'uploads/' . date("Y/m/d");

                $request->file('file')->storeAs(
                    'public/' . $pathFull, $name
                );

                return '/storage/' . $pathFull . '/' . $name;
            } catch (\Exception $error) {
                return false;
            }
        }
    }
}