<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    //
    public function index()
    {
        $categorys = Category::all();
        return view('category.index', ['categorys' => $categorys]);
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $category = new Category;
        $category->name = $request->name;

        $category->save();

        return redirect('category')->with('success', 'Categoria criada com sucesso!');
    }
}
