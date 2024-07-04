<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            return back()->withInput()->with('error', 'Erro ao atualizar ou criar o perfil: ' . $e->getMessage());
        }
    }
}
