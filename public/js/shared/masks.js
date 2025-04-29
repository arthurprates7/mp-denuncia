$(document).ready(function() {
    // Máscara CPF/CNPJ (para clientes)
    var cpfCnpjOptions = {
        onKeyPress: function (cpf, ev, el, op) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            $('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
        }
    }
    if($('#cpf_cnpj').length) {
        $('#cpf_cnpj').length > 11 ? $('#cpf_cnpj').mask('00.000.000/0000-00', cpfCnpjOptions) : $('#cpf_cnpj').mask('000.000.000-00#', cpfCnpjOptions);
    }

    // Máscara CPF (para motoristas)
    if($('#cpf').length) {
        $('#cpf').mask('000.000.000-00');
    }

    // Máscara CNPJ (para oficinas)
    if($('#cnpj').length) {
        $('#cnpj').mask('00.000.000/0000-00');
    }

    // Máscara Agência (para contas bancárias)
    if($('#agencia').length) {
        $('#agencia').mask('0000-0');
    }

    // Máscara Conta (para contas bancárias)
    if($('#conta').length) {
        $('#conta').mask('00000000-0');
    }

    // Máscara Telefone
    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('#telefone').mask(SPMaskBehavior, spOptions);

    // Máscara CEP
    $('#cep').mask('00000-000');

    // Busca CEP
    function limpa_formulário_cep() {
        $("#rua").val("");
        $("#bairro").val("");
        $("#cidade").val("");
    }

    $("#cep").blur(function() {
        var cep = $(this).val().replace(/\D/g, '');

        if (cep != "") {
            var validacep = /^[0-9]{8}$/;

            if(validacep.test(cep)) {
                $("#rua").val("...");
                $("#bairro").val("...");
                $("#cidade").val("...");

                $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                    if (!("erro" in dados)) {
                        $("#rua").val(dados.logradouro);
                        $("#bairro").val(dados.bairro);
                        $("#cidade").val(dados.localidade);
                    } else {
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } else {
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } else {
            limpa_formulário_cep();
        }
    });

    // Máscara KM (para abastecimentos)
    if($('#km_atual').length) {
        $('#km_atual').mask('000000.000', {reverse: true});
    }

    // Máscara Valor (para abastecimentos)
    if($('#valor').length) {
        $('#valor').mask('000000.00', {reverse: true});
    }

    // Máscara Litros (para abastecimentos)
    if($('#litros').length) {
        $('#litros').mask('000000.00', {reverse: true});
    }
}); 