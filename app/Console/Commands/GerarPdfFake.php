<?php

namespace App\Console\Commands;

use App\Services\GeradorPdfFake;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GerarPdfFake extends Command
{
    protected $signature = 'processo:gerar-pdf-fake
        {--numero_processo=0001234-12.2024.8.26.0000 : Número do processo}
        {--data_distribuicao=15/03/2024 : Data de distribuição}
        {--foro=Foro Central da Capital : Foro}
        {--comarca=São Paulo : Comarca}
        {--juiz=Dr. João Silva : Juiz}
        {--boletim_ocorrencia= : Conteúdo do boletim de ocorrência}
        {--interrogatorio= : Conteúdo do interrogatório}
        {--documentos_diversos= : Lista de documentos diversos}';

    protected $description = 'Gera um PDF fake de processo para teste';

    public function handle(GeradorPdfFake $gerador)
    {
        $dados = [
            'numero_processo' => $this->option('numero_processo'),
            'data_distribuicao' => $this->option('data_distribuicao'),
            'foro' => $this->option('foro'),
            'comarca' => $this->option('comarca'),
            'juiz' => $this->option('juiz'),
            'boletim_ocorrencia' => $this->option('boletim_ocorrencia'),
            'interrogatorio' => $this->option('interrogatorio'),
            'documentos_diversos' => $this->option('documentos_diversos')
        ];

        $pdfContent = $gerador->gerar($dados);
        
        $caminho = 'processos/processo_fake.pdf';
        Storage::disk('public')->put($caminho, $pdfContent);
        
        $this->info('PDF fake gerado com sucesso em: ' . Storage::disk('public')->path($caminho));
    }
} 