<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Exception;


class CategoryController extends Controller
{
    //
    public function index()
    {

        $user = auth()->user();
        $categories = Category::where('user_id', $user->id)->paginate(5)->withQueryString();

        return view('category.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $category = new Category;
        $category->name = $request->name;
        $category->user_id = $user->id;

        $category->save();

        return redirect('category')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit($id)
    {
        $categorys = Category::findOrFail($id);
        return view('category.edit', ['categorys' => $categorys]);
    }

    public function update(Request $request)
    {

        try {
            $data = $request->all();

            $id = $request->id;
            Category::findOrFail($id)->update($data);

            return redirect('category')->with('success', 'Categoria atualizada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Categoria não atualizada');
        }
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect('category')->with('success', 'Categoria apagada!');
    }
}
