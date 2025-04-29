<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 2cm;
        }
        .titulo {
            text-align: center;
            margin-bottom: 2cm;
        }
        .data {
            text-align: right;
            margin-bottom: 1cm;
        }
        .conteudo {
            text-align: justify;
        }
        .quota, .partes {
            margin-bottom: 1cm;
        }
        .secao {
            font-weight: bold;
            margin-bottom: 0.5cm;
        }
    </style>
</head>
<body>
   

    <div class="titulo">
        <h1>{{ $titulo }}</h1>
    </div>

    <div class="conteudo">
        @if($conteudo['quota'])
            <div class="quota">
                <div class="secao">QUOTA DA DENÚNCIA</div>
                {!! nl2br(e($conteudo['quota'])) !!}
            </div>
        @endif

        @if($conteudo['partes'])
            <div class="partes">
                <div class="secao">QUALIFICAÇÃO DAS PARTES</div>
                {!! nl2br(e($conteudo['partes'])) !!}
            </div>
        @endif

        @if($conteudo['fatos'])
            <div class="fatos">
                <div class="secao">DOS FATOS</div>
                {!! nl2br(e($conteudo['fatos'])) !!}
            </div>
        @endif
    </div>
</body>
</html>
