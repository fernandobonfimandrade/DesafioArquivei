<?php

namespace App\Http\Controllers;

use App\Models\NotasFiscais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Classes\NfeDOMDocument;

class NotaFiscalController extends Controller
{
    public function sync(){
        $data = Http::withOptions(['verify' => false])->withHeaders([
            'Content-Type' => 'application/json',
            'x-api-id' => 'f96ae22f7c5d74fa4d78e764563d52811570588e',
            'x-api-key' => 'cc79ee9464257c9e1901703e04ac9f86b0f387c2'
        ])->get('https://sandbox-api.arquivei.com.br/v1/nfe/received');
        
        $qtd = 0;
        $erro = 0;
        $sucesso = 0;
        $sincronizado = 0;
        if($data->status() == 200){
            $danfes = json_decode($data->body());
            foreach ($danfes->data as $key => $nota) {
                $qtd++;
                
                $xml = base64_decode($nota->xml);
                //NfeDOMDocument classe que busca os dados dentro do xml da nfe
                $dom = new NfeDOMDocument();
                $dom->loadXML($xml);
               
                $notaFiscal = array('chaveAcesso' => $dom->getChaveAcesso(),  'valorTotal' => $dom->getValorTotalNota());
                switch ($this->store($notaFiscal)) {
                    case -1:
                        //nfe ja sincronizada anteriormente, BD retorna 
                        $sincronizado++;
                        break;
                    case 0:
                        $erro++;
                        break;
                    case 1:
                        $sucesso++;
                        break;
                 }
            }
            return response()->json([
                'notasEncontradas' => $qtd,
                'notasImportadas' => $sucesso,
                'notasSincronizadasAnteriormente' => $sincronizado,
                'erros' => $erro
            ]);
        }else{
            return response()->json([
                "error" => 'Falha ao conectar com Arquivei',
                "response" =>  json_decode($data->body())
            ]);
        }
        
    }

    private function store($notaFiscal){
        try {
            $create = NotasFiscais::create($notaFiscal);
            if ($create) {
                //cadastrado com sucesso
                return 1;
            }else{
                //falha ao cadastar no banco 
                return 0;
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            //captura excessao do SQL
            if($ex->getCode() == 23000){
                //chave de acesso existente ja que ela é um campo unique
                return -1;
            }else{
                // algum outro erro de SQL
                0;
            }
        }
        
    } 
    
    public function show(Request $request){
        $notaFiscal = NotasFiscais::where('chaveAcesso', $request->danfe)->first();
        if($notaFiscal){
            $response = array('sucesso' => true , 'valorTotal' => $notaFiscal->valorTotal);
        }else{
            $response = array('sucesso' => false , 'mensagem' => 'Nota não encontrada');
        }
        
        return response()->json($response);
    }
}
