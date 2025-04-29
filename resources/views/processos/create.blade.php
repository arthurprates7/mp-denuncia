@extends('template.navbar')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Processos</h1>
        <p class="mb-4">Cadastrar Novo Processo</p>

        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Novo Processo</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('processos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="numero_cnj" class="form-label">Número CNJ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero_cnj') is-invalid @enderror" 
                                   id="numero_cnj" name="numero_cnj" value="{{ old('numero_cnj') }}" required>
                            @error('numero_cnj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="titulo" class="form-label">Título<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="arquivo" class="form-label">Arquivo PDF<span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('arquivo') is-invalid @enderror" 
                                   id="arquivo" name="arquivo" accept=".pdf" required>
                            @error('arquivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Tamanho máximo: 10MB</small>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('processos.index') }}" class="btn btn-secondary btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span class="text">CANCELAR</span>
                            </a>
                            <button type="submit" class="btn btn-success btn-icon-split ml-2">
                                <span class="icon text-white-50">
                                    <i class="fas fa-save"></i>
                                </span>
                                <span class="text">SALVAR</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 