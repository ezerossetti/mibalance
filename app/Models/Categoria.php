<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaccion;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categoria';
    protected $primaryKey = 'idcategoria';
    public $timestamps = false;


    public function transaccions()
    {
        return $this.hasMany(Transaccion::class, 'idcategoria');
    }
}
