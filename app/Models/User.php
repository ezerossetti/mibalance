<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Transaccion;
use App\Models\Alerta;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
 * The table associated with the model.
 *
 * @var string
 */
protected $table = 'usuario';

/**
 * The primary key associated with the table.
 *
 * @var string
 */
protected $primaryKey = 'idUsuario';

/**
 * Define the custom timestamp columns.
 */
const CREATED_AT = 'fecha_registro';
const UPDATED_AT = null; // Indícaselo si no tienes una columna updated_at

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transaccions()
    {
    return $this->hasMany(Transaccion::class, 'idUsuario');
    }

    public function categorias()
    {
    return $this->hasMany(Categoria::class, 'idUsuario', 'idUsuario');
    }

    public function alertas()
{
    // Un usuario puede tener muchas alertas
    return $this->hasMany(Alerta::class, 'idUsuario', 'idUsuario');
}
}
