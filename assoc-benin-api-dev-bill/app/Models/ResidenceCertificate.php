<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidenceCertificate extends Model
{
    use HasFactory;

    protected $table = "residence_certificates";

    protected $fillable = ['certificat',
                        'r_role_du_membre',
                        'code_requete'];

    // protected function certificate(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => json_decode($value, true),
    //         set: fn ($value) => json_encode($value),
    //     );
    // } 

}
