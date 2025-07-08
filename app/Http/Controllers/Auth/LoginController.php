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

                $Exist = $this->jaExiste($fixa, $user, $dataAtual);

                if (!$Exist) {
                    $this->create($fixa, $user);
                }
            }

            $repeats = Conta::where('repeat', '>', 0)
                ->whereDate('maturity', '<', $dataAtual->startOfMonth())
                ->withoutTrashed()->get();

            foreach ($repeats as $repeat) {

                $Exist = $this->jaExiste($repeat, $user, $dataAtual);

                $repeatGet = Conta::where('name', $repeat->name)
                    ->where('value', $repeat->value)
                    ->where('situation', $repeat->situation)
                    ->where('user_id', $user->id)
                    ->where('category_id', $repeat->category_id)
                    ->where('type', $repeat->type)
                    ->where('note', $repeat->note)
                    ->count();

                if (!$Exist && $repeatGet <= $repeat->repeat) {
                    $this->create($repeat, $user);
                }
            }
        } catch (Exception $e) {
            Log::error('Erro Não gerado', ['mensagem' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Conta não atualizada');
        }
    }

    protected function jaExiste($conta, $user, $dataAtual)
    {
        Conta::where('name', $conta->name)
            ->where('value', $conta->value)
            ->where('situation', $conta->situation)
            ->where('user_id', $user->id)
            ->where('category_id', $conta->category_id)
            ->where('type', $conta->type)
            ->where('note', $conta->note)
            ->whereYear('maturity', $dataAtual->year)
            ->whereMonth('maturity', $dataAtual->month)
            ->exists();
    }

    protected function create($conta, $user)
    {
        $dia = Carbon::parse($conta->maturity)->day;
        Conta::create([
            'name' => $conta->name,
            'value' => $conta->value,
            'maturity' => now()->copy()->setDay($dia),
            'situation' => $conta->situation,
            'user_id' => $user->id,
            'note' => $conta->note,
            'category_id' => $conta->category_id,
            'type' => $conta->type,
            'fixed' => false,
        ]);
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
