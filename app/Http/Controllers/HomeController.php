<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ContaRequest;
use App\Models\Category;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $perPage = $request->input('perPage', 5);

        $contasQuery = Conta::where('user_id', $user->id)
            ->when($request->has('name'), function ($whenQuery) use ($request) {
                $whenQuery->where('name', 'like', '%' . $request->name . '%');
            })
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'))
            ->when($request->filled('situation'), function ($whenQuery) use ($request) {
                $whenQuery->where('situation', $request->situation);
            })
            ->when($request->filled('type'), function ($whenQuery) use ($request) {
                $whenQuery->where('type', $request->type);
            })
            ->orderByDesc('created_at');

        $contas = $contasQuery->paginate($perPage)->withQueryString();


        return view('home', [
            'contas' => $contas,
            'name' => $request->name,
            'data_inicio' =>  $dataInicio,
            'data_fim' => $dataFim,
            'situation' => $request->situation,
            'type' => $request->type,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();

        return view('contas.create', [
            'categorys' => $categorys,
        ]);
    }

    public function store(ContaRequest $request)
    {
        $request->validated();

        try {

            $contas = new Conta;

            $contas->name = $request->name;
            $contas->value = str_replace(',', '.', str_replace('.', '', $request->value));
            $contas->maturity = $request->maturity;
            $contas->situation = $request->situation;
            $contas->category_id = $request->category_id;
            $contas->type = $request->type;
            $contas->note = $request->note;

            if ($contas->value <= 0 || $contas->value === "") {
                return back()->withInput()->with('error', 'O valor precisa ser maior que zero');
            }

            $user = Auth::user();
            $contas->user_id = $user->id;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;

                $extension = $requestImage->extension();

                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

                $requestImage->move(public_path('img/comprovantes' . $contas->user_id), $imageName);

                $contas->image =  $imageName;
            }

            $contas->save();

            return redirect('home')->with('success', 'Conta criada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Conta não Cadastrada');
        }
    }

    public function show($id)
    {

        $user = auth()->user();
        $contas = Conta::findOrFail($id);
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();
        return view('contas.show', ['contas' => $contas, 'categorys' => $categorys]);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $contas = Conta::findOrFail($id);
        $categorys = Category::where('user_id', $user->id)->orderBy('name', 'asc')->get();
        return view('contas.edit', ['contas' => $contas, 'categorys' => $categorys]);
    }

    public function update(ContaRequest $request)
    {
        try {
            $user_id = auth()->user()->id;
            $data = $request->all();
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;

                $extension = $requestImage->extension();

                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

                $requestImage->move(public_path('img/comprovantes' . $user_id), $imageName);

                $data['image'] =  $imageName;
            }
            $id = $request->id;
            Conta::findOrFail($id)->update($data);

            return redirect('home')->with('success', 'Conta atualizada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Conta não atualizada');
        }
    }

    public function destroy($id)
    {
        Conta::findOrFail($id)->delete();
        return redirect('home')->with('success', 'Conta apagada!');
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $contas = Conta::where('user_id', $user->id)
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'));

        $allContas = $contas->get();

        $contasPagasValor = $allContas->where('situation', 'paid')->sum('value');
        $contasPagasQuantidade = $allContas->where('situation', 'paid')->count();

        $contasPendentes = $allContas->where('situation', 'pending');
        $contasPendentesValor = $contasPendentes->sum('value');
        $contasPendentesQuantidade = $contasPendentes->count();

        $contasCanceladasValor = $allContas->where('situation', 'canceled')->sum('value');
        $contasCanceladasQuantidade = $allContas->where('situation', 'canceled')->count();

        $contasEntrada = $allContas->where('type', 'entrada');
        $contasEntradaValor = $contasEntrada->sum('value');
        $contasEntradaQuantidade = $contasEntrada->count();

        $contasSaida = $allContas->where('type', 'saida');
        $contasSaidaValor = $contasSaida->sum('value');
        $contasSaidaQuantidade = $contasSaida->count();

        $MyTotal = ($contasEntradaValor - $contasSaidaValor);


        $total = $allContas->sum('value');
        $totalquantidade = $allContas->count();

        return view('dashboard', [
            'contasPagasValor' => $contasPagasValor,
            'contasPagasQuantidade' => $contasPagasQuantidade,
            'contasPendentesValor' => $contasPendentesValor,
            'contasPendentesQuantidade' => $contasPendentesQuantidade,
            'contasCanceladasValor' => $contasCanceladasValor,
            'contasCanceladasQuantidade' => $contasCanceladasQuantidade,
            'contasEntradaValor' => $contasEntradaValor,
            'contasEntradaQuantidade' => $contasEntradaQuantidade,
            'contasSaidaValor' => $contasSaidaValor,
            'contasSaidaQuantidade' => $contasSaidaQuantidade,
            'MyTotal' => $MyTotal,
            'total' => $total,
            'totalquantidade' => $totalquantidade,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ]);
    }


    public function gerarCsv(Request $request)
    {
        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $perPage = $request->input('perPage', 5);

        $contasQuery = Conta::where('user_id', $user->id)
            ->when($request->has('name'), function ($whenQuery) use ($request) {
                $whenQuery->where('name', 'like', '%' . $request->name . '%');
            })
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'))
            ->when($request->filled('situation'), function ($whenQuery) use ($request) {
                $whenQuery->where('situation', $request->situation);
            })
            ->when($request->filled('type'), function ($whenQuery) use ($request) {
                $whenQuery->where('type', $request->type);
            })
            ->orderByDesc('created_at');

        $contas = $contasQuery->paginate($perPage)->withQueryString();

        $totalValor = $contas->sum('value');

        // Criar o arquivo temporário
        $csvNomeArquivo = tempnam(sys_get_temp_dir(), 'csv_' . Str::ulid());

        // Abrir o arquivo na forma de escrita
        $arquivoAberto = fopen($csvNomeArquivo, 'w');

        // Criar o cabeçalho do Excel - Usar a função mb_convert_encoding para converter carateres especiais
        $cabecalho = ['id', 'Nome', 'Vencimento', mb_convert_encoding('Situação', 'ISO-8859-1', 'UTF-8'), 'Categoria', 'Tipo', 'Valor'];

        // Escrever o cabeçalho no arquivo
        fputcsv($arquivoAberto, $cabecalho, ';');

        // Ler os registros recuperados do banco de dados
        foreach ($contas as $conta) {

            $situation = $conta->situation;
            if ($situation == "paid") {
                $situation = "Pago";
            } elseif ($situation == "pending") {
                $situation = "Pendente";
            } else {
                $situation = "Cancelado";
            }

            // Criar o array com os dados da linha do Excel
            $contaArray = [
                'id' => $conta->id,
                'nome' => mb_convert_encoding($conta->name, 'ISO-8859-1', 'UTF-8'),
                'vencimento' => date('d/m/Y', strtotime($conta->maturity)),
                'situacao' => mb_convert_encoding($situation, 'ISO-8859-1', 'UTF-8'),
                'categoria' => mb_convert_encoding($conta->category->name, 'ISO-8859-1', 'UTF-8'),
                'tipo' => mb_convert_encoding($conta->type, 'ISO-8859-1', 'UTF-8'),
                'valor' => number_format($conta->value, 2, ',', '.'),
            ];

            // Escrever o conteúdo no arquivo
            fputcsv($arquivoAberto, $contaArray, ';');
        }


        // Criar o rodapé do Excel
        $rodape = ['', '', '', '', number_format($totalValor, 2, ',', '.')];

        // Escrever o conteúdo no arquivo
        fputcsv($arquivoAberto, $rodape, ';');

        // Fechar o arquivo após a escrita
        fclose($arquivoAberto);

        // Realizar o download do arquivo
        return response()->download($csvNomeArquivo, 'relatorio_contas_' . Str::ulid() . '.csv');
    }
}
