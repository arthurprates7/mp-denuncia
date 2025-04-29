class Mascara{

    constructor() {

        this.habilitarData();
        this.habilitarCPF();
        this.habilitarCNPJ();
        this.habilitarMoeda();
        this.habilitarTaxa();
        this.habilitarCEP();
        this.habilitarCelular();
        this.habilitarTelefone();
        this.desabilitarAutoComplete();
        this.habilitarMoney();

    }

    habilitarData(){

        $('body').on('focus','input[class="form-control date"]', function(){

            $(this).datepicker({

                format: "dd/mm/yyyy",
                language: "pt-BR",
                //startDate: '+0d',
                autoclose: true

            });

        });

        $(document).on("focus", ".form-control.date", function(){

            $(this).mask('00/00/0000');

        });

    }

    habilitarCPF(){

        $(document).on("focus", ".form-control.cpf", function(){

            $(this).mask('000.000.000-00');

        });

    }

    habilitarCNPJ(){

        $(document).on("focus", ".form-control.cnpj", function(){

            $(this).mask('00.000.000/0000-00');

        });

    }

    habilitarTaxa(){

        $(document).on("focus", ".form-control.taxa", function(){

            $(this).mask('000.000.000.000.000.00', {reverse: true});

        });

        $(document).on("keypress", ".form-control.taxa", function(){

            return(MascaraMoeda.formatar(this,'.','.',event));

        });

    }

    habilitarMoney(){

        $(document).on("focus", ".form-control.dinheiro", function(){

            $(this).mask('000000000000000.00', {reverse: true});

        });

        $(document).on("keypress", ".form-control.dinheiro", function(){

            return(MascaraMoeda.formatar(this,'','.',event));

        });
    }

    habilitarMoeda(){

        $(document).on("focus", ".form-control.money", function(){

            $(this).mask('000.000.000.000.000,00', {reverse: true});

        });

        $(document).on("keypress", ".form-control.money", function(){

            return(MascaraMoeda.formatar(this,'.',',',event));

        });

    }

    habilitarCEP(){

        $(document).on("focus", ".form-control.cep", function(){

            $(this).mask('00000-000');

        });

    }

    habilitarCelular(){

        $(document).on("focus", ".form-control.celular", function(){

            $(this).mask('(00) 0 0000-0000');

        });

    }

    habilitarTelefone(){

        $(document).on("focus", ".form-control.telefone", function(){

            $(this).mask('(00) 0000-0000');

        });

    }

    desabilitarAutoComplete(){

        $(document).on('click', ':input', function() {

            $(this).attr('autocomplete', false);

        });

    }


}

const mascara = new Mascara();
