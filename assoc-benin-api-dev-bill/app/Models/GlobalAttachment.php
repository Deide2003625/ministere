<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAttachment extends Model
{
    use HasFactory;

    protected $table = "global_attachments";

    protected $fillable = ['demande_enregistrement',
                            'proces_verbal',
                            'membres_ag',
                            'reglement_interieur',
                            'recepisse_de_versement',
                            'recepisse_admin',
                            'ancien_recepisse_admin',
                            'statuts',
                            'code_requete',
                            'recording_request_id',];

    public function RecordingRequest(): BelongsTo
    {
        return $this->belongsTo(RecordingRequest::class);
    }
}
