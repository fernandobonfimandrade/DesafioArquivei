<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    /** @test */
    public function ComunicacaoOk()
    {
        $response = $this->get('/api/sincronizarArquivei');
        $response->assertStatus(200);
        $response->assertJson([
            "notasEncontradas" => 50,
        ]);
    }    
    /** @test */
    public function SincronizacaoSemErros()
    {
        $response = $this->get('/api/sincronizarArquivei');
        $response->assertJson([
            "erros" => 0,
        ]
        );
    }
    /** @test */
    public function BuscarChave()
    {
        $response = $this->get('/api/notaFiscal/35171183932854000133550000000155921244819092');
        $response->assertStatus(200);
    }
}
