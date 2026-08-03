<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'type',
        'status',
        'size_bytes',
        'generated_by',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'size_bytes' => 'integer',
        ];
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'completed' => 'success',
            'queued' => 'secondary',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    public function humanSize(): string
    {
        $bytes = $this->size_bytes;

        return match (true) {
            $bytes >= 1_048_576 => round($bytes / 1_048_576, 2).' MB',
            $bytes >= 1_024 => round($bytes / 1_024, 2).' KB',
            default => $bytes.' B',
        };
    }
}
