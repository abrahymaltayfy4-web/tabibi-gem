<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'uploader_id',
        'original_name',
        'stored_name',
        'storage_disk',
        'storage_path',
        'mime_type',
        'file_size_bytes',
        'checksum_sha256',
        'category',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(FileAccessLog::class);
    }
}
