<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{

    // Otros atributos y métodos

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'nro_documento',
        'user_message',
        'assistant_response',
        'llamaia_response',
        'remaining_responses',
    ];

}
