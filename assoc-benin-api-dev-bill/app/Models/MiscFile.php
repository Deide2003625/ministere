<?php

namespace App\Models;

use App\Models\RecordingRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MiscFile extends Model
{
    use HasFactory;

    protected $table = "misc_files";

    protected $fillable = ['type',
                            'file',
                            'code_requete',
                            'recording_request_id'];
                            
    public function RecordingRequest(): BelongsTo
    {
        return $this->belongsTo(RecordingRequest::class);
    }

}
