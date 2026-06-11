<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CategoryController extends Controller
{
    //
    public function index()
    {

        $user = auth()->user();
        $categories = Category::where('user_id', $user->id)->paginate(5)->withQueryString();

        return Inertia::render('Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('Categories/Form', [
            'category' => null,
            'mode' => 'create',
        ]);
    }

    public function store(CategoryRequest $request)
    {

        $request->validated();

        try {
            $user = auth()->user();
            $category = new Category;
            $category->name = $request->name;
            $category->user_id = $user->id;

            $category->save();

            return redirect('category')->with('success', 'Categoria criada com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao criar categoria.', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Erro ao criar categoria');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $categorys = Category::where('user_id', $user->id)->findOrFail($id);
        return Inertia::render('Categories/Form', [
            'category' => $categorys,
            'mode' => 'edit',
        ]);
    }

    public function update(CategoryRequest $request)
    {

        try {
            $data = $request->validated();
            $id = $request->id;
            Category::findOrFail($id)->update($data);

            return redirect('category')->with('success', 'Categoria atualizada com sucesso!');
        } catch (Exception $e) {
            Log::error('Categoria não atualizada.', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Categoria não atualizada');
        }
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect('category')->with('success', 'Categoria apagada!');
    }
}
