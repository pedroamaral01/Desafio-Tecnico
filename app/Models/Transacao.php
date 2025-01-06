<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacoes';

    protected $primaryKey = 'id';

    protected $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'contas_id',
        'valor',
        'tipo_transacao',
        'moeda',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class, 'contas_id', 'id');
    }
}
