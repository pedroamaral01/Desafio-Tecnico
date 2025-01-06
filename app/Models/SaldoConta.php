<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoConta extends Model
{
    protected $table = 'saldo_contas';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'contas_id',
        'moeda',
        'valor',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class, 'contas_id', 'id');
    }
}
