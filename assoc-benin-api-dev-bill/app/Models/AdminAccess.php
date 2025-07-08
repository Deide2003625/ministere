<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminAccess extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = "admin_accesses";
    // Non utilisée
    protected $fillable = [
                    'matricule', 'prenom','nom',
                    'telephone', 'email', 'password',
                    'role','statut',
                    'created_at', 'updated_at'
    ];
}
