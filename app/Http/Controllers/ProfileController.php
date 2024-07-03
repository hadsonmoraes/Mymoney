<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    public function edit($id)
    {

        // $user = auth()->user();
        $profile = User::findOrFail($id);

        return view('profile.edit', ['profile' => $profile]);
    }

    public function update(Request $request)
    {
        try {
            $data = $request->all();
            $id = $request->id;
            User::findOrFail($id)->update($data);

            return redirect('home')->with('success', 'Perfil atualizado com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Perfil não atualizado');
        }
    }
}
