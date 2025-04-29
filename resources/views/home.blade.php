@extends('template.navbar')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Informações do inquérito</span>
                    </h6>
                    <form method="GET" action="{{ route('processos.buscar-cnj') }}">
                        @csrf
                        <div class="form-group px-3">
                            <label for="numero_cnj">Coloque o número do inquérito para iniciar a pesquisa</label>
                            <input type="text" id="numero_cnj" name="numero_cnj" class="form-control"
                                placeholder="Número CNJ" value="{{ request()->get('numero_cnj') }}">
                        </div>
                        <div class="px-3 mb-3">
                            <button type="submit" class="btn btn-primary btn-block">Buscar Processo</button>
                        </div>
                    </form>
                </div>
            </nav>

            <!-- Conteúdo principal -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="row">
                    <div class="col-md-6">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if(request()->has('numero_cnj') && request()->get('numero_cnj') !== '')
                            <!-- Card de Quota da Denúncia -->
                            <div id="quota-denuncia" class="card mb-4">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <span>Quota da denúncia</span>
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Escreva instruções ao GPT para alterar o texto abaixo:</label>
                                        <textarea id="instrucoes-gpt" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center my-2">
                                        <button id="gerar-trecho" class="btn btn-secondary btn-block" disabled>Gerar novo trecho</button>
                                    </div>
                                    <div class="form-group">
                                        <label>Texto gerado</label>
                                        <textarea id="texto-gerado-quota" class="form-control" rows="3" readonly>Trecho gerado pela LLM</textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Texto Final</label>
                                        <textarea id="texto-editado-quota" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button id="aprovar-quota" class="btn btn-success btn-block">
                                            <i class="fas fa-check"></i> Aprovar texto
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card de Qualificação das Partes -->
                            <div id="qualificacao-partes" class="card mb-4">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <span>Qualificação das partes</span>
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Escreva instruções ao GPT para alterar o texto abaixo:</label>
                                        <textarea id="instrucoes-gpt-partes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center my-2">
                                        <button id="gerar-trecho-partes" class="btn btn-secondary btn-block" disabled>Gerar novo trecho</button>
                                    </div>
                                    <div class="form-group">
                                        <label>Texto gerado</label>
                                        <textarea id="texto-gerado-partes" class="form-control" rows="3" readonly>Trecho gerado pela LLM</textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Texto Final</label>
                                        <textarea id="texto-editado-partes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button id="aprovar-partes" class="btn btn-success btn-block">
                                            <i class="fas fa-check"></i> Aprovar texto
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card de Dos Fatos -->
                            <div id="dos-fatos" class="card mb-4">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <span>Dos fatos</span>
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Escreva instruções ao GPT para alterar o texto abaixo:</label>
                                        <textarea id="instrucoes-gpt-fatos" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center my-2">
                                        <button id="gerar-trecho-fatos" class="btn btn-secondary btn-block" disabled>Gerar novo trecho</button>
                                    </div>
                                    <div class="form-group">
                                        <label>Texto gerado</label>
                                        <textarea id="texto-gerado-fatos" class="form-control" rows="3" readonly>Trecho gerado pela LLM</textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label>Texto Final</label>
                                        <textarea id="texto-editado-fatos" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button id="aprovar-fatos" class="btn btn-success btn-block">
                                            <i class="fas fa-check"></i> Aprovar texto
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(request()->has('numero_cnj') && request()->get('numero_cnj') !== '')
                        <!-- Visualizador de PDF -->
                        <div class="col-md-6">
                            <div id="pdf-container">
                                <iframe id="pdf-viewer" src="{{ route('gpt.getPdf') }}" width="100%" height="800px" frameborder="0"></iframe>
                            </div>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
    
@endsection

@section('scripts')
<script>
    const routes = {
        streamQuota: '{{ route("gpt.streamQuota") }}',
        streamPartes: '{{ route("gpt.streamPartes") }}',
        streamFatos: '{{ route("gpt.streamFatos") }}',
        getPdf: '{{ route("gpt.getPdf") }}'
    };
</script>
<script src="{{ asset('js/denuncia.js') }}"></script>
@endsection
