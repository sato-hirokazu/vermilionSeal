<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    /**
     * カテゴリー一覧を取得する。
     *
     * @return string
     */
    public function index()
    {
        $data = Category::orderBy('id', 'asc')->get();
        if (empty($data)) abort(404);

        return response()->json($data);
    }

}
