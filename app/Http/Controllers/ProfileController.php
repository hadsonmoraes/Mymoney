<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function edit($id)
    {

        $profile = User::findOrFail($id);

        return view('profile.edit', ['profile' => $profile]);
    }

    public function update(UserRequest $request)
    {
        $request->validated();
        try {

            $user = User::find($request->id);

            if ($user) {

                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->password != "") {
                    $user->password = Hash::make($request->password);
                }

                return redirect()->route('profile.edit', ['id' => $user->id])->with('success', 'Perfil atualizado com sucesso!');
            }
        } catch (Exception $e) {
            Log::error('Erro ao atualizar ou criar o perfil.', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Erro ao atualizar ou criar o perfil');
        }
    }

    public function toggleSidebar(Request $request){

   $request->validate([
        'sidebar_open' => 'required|boolean',
    ]);

    $user = auth()->user();
    $user->sidebar = $request->sidebar_open;
    $user->save();

    return response()->json(['success' => true]);
    }

    public function darkMode(Request $request){

   $request->validate([
        'theme' => 'required|in:theme-light,theme-dark',
    ]);

    $user = auth()->user();
    $user->darkmode = $request->theme;
    $user->save();

    return response()->json(['success' => true]);
    }
}
