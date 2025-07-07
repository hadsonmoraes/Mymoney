<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Conta;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    protected function authenticated(Request $request, $user)
    {
        try {
            $dataAtual = Carbon::now();

            $fixas = Conta::where('fixed', true)
                ->whereDate('maturity', '<', $dataAtual->startOfMonth())
                ->withoutTrashed()->get();

            foreach ($fixas as $fixa) {

                $jaExiste = Conta::where('name', $fixa->name)
                    ->where('value', $fixa->value)
                    ->where('situation', $fixa->situation)
                    ->where('user_id', $user->id)
                    ->where('category_id', $fixa->category_id)
                    ->where('type', $fixa->type)
                    ->where('note', $fixa->note)
                    ->whereYear('maturity', $dataAtual->year)
                    ->whereMonth('maturity', $dataAtual->month)
                    ->exists();

                if (!$jaExiste) {

                    $dia = Carbon::parse($fixa->maturity)->day;
                    Conta::create([
                        'name' => $fixa->name,
                        'value' => $fixa->value,
                        'maturity' => now()->copy()->setDay($dia),
                        'situation' => $fixa->situation,
                        'user_id' => $user->id,
                        'note' => $fixa->note,
                        'category_id' => $fixa->category_id,
                        'type' => $fixa->type,
                        'fixed' => false,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro Não gerado', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Conta não atualizada');
        }
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
