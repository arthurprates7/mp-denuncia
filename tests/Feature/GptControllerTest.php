<?php

namespace Tests\Feature;

use App\Models\Processo;
use App\Services\EmbeddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery;
use OpenAI\Client;

class GptControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $mockEmbeddingService;
    protected $mockOpenAIClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock do EmbeddingService
        $this->mockEmbeddingService = Mockery::mock(EmbeddingService::class);
        $this->app->instance(EmbeddingService::class, $this->mockEmbeddingService);

        // Mock do OpenAI Client
        $this->mockOpenAIClient = Mockery::mock(Client::class);
        $this->app->instance(Client::class, $this->mockOpenAIClient);

        // Limpar o cache antes de cada teste
        Cache::flush();
    }

    public function test_stream_partes_com_multiplos_agentes()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andReturn([
                (object) ['choices' => [(object) ['delta' => (object) ['content' => 'Resposta do GPT']]]]
            ]);

        // Número de agentes para testar simultaneamente
        $numeroAgentes = 5;
        $promises = [];

        // Criar múltiplas requisições simultâneas
        for ($i = 0; $i < $numeroAgentes; $i++) {
            $promises[] = $this->get('/api/gpt/stream-partes', [
                'instrucoes' => "Instruções do agente {$i}",
                'numero_cnj' => $processo->numero_cnj
            ]);
        }

        // Executar todas as requisições simultaneamente
        $responses = $this->getMultiple($promises);

        // Verificar se todas as respostas foram bem sucedidas
        foreach ($responses as $response) {
            $response->assertStatus(200)
                    ->assertHeader('Content-Type', 'text/event-stream')
                    ->assertHeader('Cache-Control', 'no-cache')
                    ->assertHeader('X-Accel-Buffering', 'no')
                    ->assertHeader('Connection', 'keep-alive');
        }
    }

    public function test_conteudo_do_stream()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andReturn([
                (object) ['choices' => [(object) ['delta' => (object) ['content' => 'Resposta do GPT']]]]
            ]);

        // Fazer a requisição
        $response = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar o status e headers
        $response->assertStatus(200)
                ->assertHeader('Content-Type', 'text/event-stream')
                ->assertHeader('Cache-Control', 'no-cache')
                ->assertHeader('X-Accel-Buffering', 'no')
                ->assertHeader('Connection', 'keep-alive');

        // Obter o conteúdo da resposta
        $content = $response->getContent();
        
        // Verificar se o conteúdo contém os dados esperados
        $this->assertStringContainsString('data: {"text":', $content);
        $this->assertStringContainsString('data: {"done":true}', $content);
    }

    public function test_erro_ao_buscar_processo_inexistente()
    {
        // Mock do retorno do EmbeddingService (não deve ser chamado neste caso)
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->never();

        // Fazer a requisição com um número CNJ inexistente
        $response = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => '0000000-00.0000.0.00.0000'
        ]);

        // Verificar se a resposta contém o erro esperado
        $response->assertStatus(500)
                ->assertJson([
                    'error' => 'No query results for model [App\\Models\\Processo] 0000000-00.0000.0.00.0000'
                ]);
    }

    public function test_erro_ao_faltar_parametros_obrigatorios()
    {
        // Testar sem o parâmetro 'instrucoes'
        $response = $this->get('/api/gpt/stream-partes', [
            'numero_cnj' => '0001234-12.2024.8.26.0000'
        ]);

        $response->assertStatus(500)
                ->assertJson([
                    'error' => 'The instrucoes field is required.'
                ]);

        // Testar sem o parâmetro 'numero_cnj'
        $response = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste'
        ]);

        $response->assertStatus(500)
                ->assertJson([
                    'error' => 'The numero cnj field is required.'
                ]);
    }

    public function test_erro_ao_embedding_service_lancar_excecao()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do EmbeddingService para lançar uma exceção
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->andThrow(new \Exception('Erro ao buscar embeddings'));

        // Fazer a requisição
        $response = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar se a resposta contém o erro esperado
        $response->assertStatus(500)
                ->assertJson([
                    'error' => 'Erro ao buscar embeddings'
                ]);
    }

    public function test_erro_ao_openai_lancar_excecao()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client para lançar uma exceção
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andThrow(new \Exception('Erro ao gerar resposta do GPT'));

        // Fazer a requisição
        $response = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar se a resposta contém o erro esperado
        $response->assertStatus(500)
                ->assertJson([
                    'error' => 'Erro ao gerar resposta do GPT'
                ]);
    }

    public function test_cache_do_processo()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->once() // Deve ser chamado apenas uma vez
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andReturn([
                (object) ['choices' => [(object) ['delta' => (object) ['content' => 'Resposta do GPT']]]]
            ]);

        // Fazer a primeira requisição
        $response1 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Fazer a segunda requisição (deve usar o cache)
        $response2 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar se ambas as respostas foram bem sucedidas
        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verificar se o cache foi usado
        $this->assertTrue(Cache::has("processo_{$processo->numero_cnj}"));
    }

    public function test_cache_dos_embeddings()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->once() // Deve ser chamado apenas uma vez
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andReturn([
                (object) ['choices' => [(object) ['delta' => (object) ['content' => 'Resposta do GPT']]]]
            ]);

        // Fazer a primeira requisição
        $response1 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Fazer a segunda requisição (deve usar o cache dos embeddings)
        $response2 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar se ambas as respostas foram bem sucedidas
        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verificar se o cache dos embeddings foi usado
        $this->assertTrue(Cache::has("processo_embeddings_{$processo->id}"));
    }

    public function test_cache_dos_embeddings_expira()
    {
        // Criar um processo fake
        $processo = Processo::factory()->create([
            'numero_cnj' => '0001234-12.2024.8.26.0000',
            'titulo' => 'Processo de Teste'
        ]);

        // Mock do retorno do EmbeddingService
        $this->mockEmbeddingService
            ->shouldReceive('buscarSimilares')
            ->twice() // Deve ser chamado duas vezes
            ->andReturn([
                (object) ['texto' => 'Texto similar 1'],
                (object) ['texto' => 'Texto similar 2'],
                (object) ['texto' => 'Texto similar 3']
            ]);

        // Mock do OpenAI Client
        $this->mockOpenAIClient
            ->shouldReceive('chat->createStreamed')
            ->andReturn([
                (object) ['choices' => [(object) ['delta' => (object) ['content' => 'Resposta do GPT']]]]
            ]);

        // Fazer a primeira requisição
        $response1 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Expirar o cache dos embeddings
        Cache::forget("processo_embeddings_{$processo->id}");

        // Fazer a segunda requisição (deve buscar os embeddings novamente)
        $response2 = $this->get('/api/gpt/stream-partes', [
            'instrucoes' => 'Instruções de teste',
            'numero_cnj' => $processo->numero_cnj
        ]);

        // Verificar se ambas as respostas foram bem sucedidas
        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verificar se o cache dos embeddings foi recriado
        $this->assertTrue(Cache::has("processo_embeddings_{$processo->id}"));
    }

    protected function getMultiple($promises)
    {
        $responses = [];
        foreach ($promises as $promise) {
            $responses[] = $promise;
        }
        return $responses;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 