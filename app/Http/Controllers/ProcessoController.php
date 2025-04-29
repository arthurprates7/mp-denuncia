<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Services\ProcessadorPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProcessoController extends Controller
{
    private $processadorPdf;

    public function __construct(ProcessadorPdf $processadorPdf)
    {
        $this->processadorPdf = $processadorPdf;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $processos = Processo::when($search, function($query) use ($search) {
            $query->where('numero_cnj', 'like', "%{$search}%")
                  ->orWhere('titulo', 'like', "%{$search}%");
        })->paginate(10);

        return view('processos.index', compact('processos', 'search'));
    }

    public function create()
    {
        return view('processos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:pdf|max:10240',
            'numero_cnj' => 'required|string|max:255',
            'titulo' => 'required|string|max:255'
        ]);

        try {
            $arquivo = $request->file('arquivo');
            
            Log::info('Tentando fazer upload do arquivo', [
                'nome_original' => $arquivo->getClientOriginalName(),
                'tamanho' => $arquivo->getSize(),
                'mime_type' => $arquivo->getMimeType()
            ]);

            // Gerar um nome único para o arquivo
            $nomeArquivo = uniqid() . '_' . $arquivo->getClientOriginalName();
            $caminho = 'processos/' . $nomeArquivo;

            // Salvar o arquivo usando o método mais simples
            $arquivo->storeAs('processos', $nomeArquivo, 'private');
            
            Log::info('Arquivo salvo com sucesso', [
                'caminho' => $caminho
            ]);

            $processo = Processo::create([
                'numero_cnj' => $request->numero_cnj,
                'titulo' => $request->titulo,
                'caminho_arquivo' => $caminho,
                'metadata' => [
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'tamanho' => $arquivo->getSize(),
                    'mime_type' => $arquivo->getMimeType()
                ]
            ]);

            Log::info('Processo criado com sucesso', [
                'processo_id' => $processo->id
            ]);

            // Processar o PDF
            $this->processadorPdf->processar($processo);

            return redirect()->route('processos.show', $processo)
                ->with('success', 'Processo cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar processo', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Erro ao salvar o processo: ' . $e->getMessage());
        }
    }

    public function show(Processo $processo)
    {
        return view('processos.show', compact('processo'));
    }

    public function download(Processo $processo): BinaryFileResponse
    {
        $caminho = Storage::disk('private')->path($processo->caminho_arquivo);
        return response()->download($caminho, $processo->metadata['nome_original']);
    }

    public function buscarPorCnj(Request $request)
    {
        $numero_cnj = $request->query('numero_cnj');
        
        $processo = Processo::where('numero_cnj', $numero_cnj)->first();
        
        if (!$processo) {
            return redirect()->route('home')
                ->with('error', 'Processo não encontrado com o número CNJ informado.');
        }
        
        return redirect()->route('home', ['numero_cnj' => $numero_cnj]);
    }
} 