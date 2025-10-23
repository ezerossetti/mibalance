<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Categoria;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transaccion';
    protected $primaryKey = 'idtransaccion';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategoria');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'idforma_pago');
    }

}
