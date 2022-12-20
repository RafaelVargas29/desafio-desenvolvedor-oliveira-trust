<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\DadosFormController;
use App\Http\Requests\StoreConversao;
use App\Services\CalculaService;
use App\Services\ConversaoApiService;
use Illuminate\Support\Facades\Http;

class ConsomeApiController extends Controller
{
    protected $conversaoApiService;
    protected $calculaService;

    public function __construct(ConversaoApiService $conversaoApiService, CalculaService $calculaService){
        $this->conversaoApiService = $conversaoApiService;
        $this->calculaService = $calculaService;
    }

    public function index(){
        $traducaoMoeda = $this->conversaoApiService->getLegenda();

        return view('/conversor/home', ['traducaoMoeda' => $traducaoMoeda]); 
    }

    public function storeConversao(StoreConversao $request){
        $cotacao = $this->conversaoApiService->getCotacao($request->base, $request->destino);
        $valores = $this->calculaService->calculaTaxa($cotacao, $request->valor, $request->pagamento);

        return $dadosSaida = [
            'moedadaOrigem' => $request->base,
            'moedaDestino' => $request->destino,
            'valorConversao' => number_format($request->valor, 2, ',', '.'),
            'formaPagamento' => $request->pagamento,
            'valorMoedaDestino' => number_format((1 / $cotacao), 2, ',', '.'),
            'valorComprado' => number_format($valores[0], 2, ',', '.'),
            'taxaPagamento' => number_format($valores[1], 2, ',', '.'),
            'taxaConversao' => number_format($valores[2], 2, ',', '.'),
            'valorUtilizado' => number_format($valores[3], 2, ',', '.'),
        ];

    }
}
