<?php

namespace App\Traits;

use App\Models\Caixa;

trait GeraMovimentacaoCaixa
{
    public function movimentacaoCaixa()
    {
        return $this->morphOne(Caixa::class, 'fonte');
    }

    protected static function bootGeraMovimentacaoCaixa()
    {
        static::created(function ($model) {
            $tipo = $model->getTipoMovimentacao();
            $valor = $model->getValorMovimentacao();
            $descricao = $model->getDescricaoMovimentacao();

            if ($tipo && $valor) {
                Caixa::criarMovimentacao($model, $tipo, $valor, $descricao);
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status') || $model->isDirty('valor_total_geral')) {
                $tipo = $model->getTipoMovimentacao();
                $valor = $model->getValorMovimentacao();
                $descricao = $model->getDescricaoMovimentacao();

                if ($tipo && $valor) {
                    // Remove movimentação antiga se existir
                    $model->movimentacaoCaixa()->delete();
                    // Cria nova movimentação
                    Caixa::criarMovimentacao($model, $tipo, $valor, $descricao);
                }
            }
        });

        static::deleted(function ($model) {
            $model->movimentacaoCaixa()->delete();
        });
    }

    // Métodos que devem ser implementados nos modelos
    abstract public function getTipoMovimentacao();
    abstract public function getValorMovimentacao();
    abstract public function getDescricaoMovimentacao();
} 