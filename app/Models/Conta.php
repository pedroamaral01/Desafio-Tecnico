<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    protected $table = 'contas';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nome_titular',
        'cpf'
    ];

    // Relacionamento com a tabela 'saldo_contas'
    public function saldosContas()
    {
        return $this->hasMany(SaldoConta::class, 'contas_id', 'id');
    }

    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'contas_id', 'id');
    }
}
