@extends('template.navbar')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Processos</h1>
        <p class="mb-4">Detalhes do Processo</p>

        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $processo->titulo }}</h6>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="font-weight-bold text-gray-800">Informações do Processo</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número CNJ:</strong> {{ $processo->numero_cnj }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Data de Criação:</strong> {{ $processo->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold text-gray-800">Capa do Processo</h5>
                        @if($processo->capa)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Número do Processo:</strong> {{ $processo->capa->numero_processo }}</p>
                                    <p><strong>Data de Distribuição:</strong> {{ $processo->capa->data_distribuicao }}</p>
                                    <p><strong>Foro:</strong> {{ $processo->capa->foro }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Comarca:</strong> {{ $processo->capa->comarca }}</p>
                                    <p><strong>Juiz:</strong> {{ $processo->capa->juiz }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Capa não processada ainda.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold text-gray-800">Documentos</h5>
                        @if($processo->documentos->count() > 0)
                            <div class="list-group">
                                @foreach($processo->documentos as $documento)
                                    <div class="list-group-item">
                                        <h6 class="mb-1 font-weight-bold">{{ $documento->tipo }}</h6>
                                        <p class="mb-1">{{ Str::limit($documento->conteudo, 200) }}</p>
                                        <small class="text-muted">Página {{ $documento->pagina }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Nenhum documento processado ainda.</p>
                        @endif
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('processos.index') }}" class="btn btn-secondary btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                            <span class="text">VOLTAR</span>
                        </a>
                        <a href="{{ route('processos.download', $processo) }}" class="btn btn-primary btn-icon-split ml-2">
                            <span class="icon text-white-50">
                                <i class="fas fa-download"></i>
                            </span>
                            <span class="text">DOWNLOAD PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 