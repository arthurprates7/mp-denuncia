@extends('template.navbar')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Processos</h1>
        <p class="mb-4">Processos Cadastrados</p>

        <div class="row mb-4">
            <div class="col-md-6">
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search"
                    method="get" action="{{ route('processos.index') }}">
                    @csrf
                    <div class="input-group">
                        <input type="text" class="form-control border-5 small" style="width: 400px"
                            placeholder="Digite o número CNJ ou título do processo" name="search" aria-label="Search"
                            aria-describedby="basic-addon2" value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('processos.create') }}" class="btn btn-success btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span class="text">NOVO PROCESSO</span>
                </a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Processos</h6>
                </div>
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    @if ($processos->isEmpty())
                        <div class="alert alert-warning">
                            Não encontramos nenhum processo cadastrado!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr class="text-center">
                                        <th>NÚMERO CNJ</th>
                                        <th>TÍTULO</th>
                                        <th>DATA DE UPLOAD</th>
                                        <th>AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($processos as $processo)
                                        <tr class="text-center">
                                            <td>{{ $processo->numero_cnj }}</td>
                                            <td>{{ $processo->titulo }}</td>
                                            <td>{{ $processo->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('processos.show', $processo) }}"
                                                    class="btn btn-info btn-icon-split">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                    <span class="text">VISUALIZAR</span>
                                                </a>
                                                <a href="{{ route('processos.download', $processo) }}"
                                                    class="btn btn-primary btn-icon-split ml-2">
                                                    <span class="icon text-white-50">
                                                        <i class="fas fa-download"></i>
                                                    </span>
                                                    <span class="text">DOWNLOAD</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $processos->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 