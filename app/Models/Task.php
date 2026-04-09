<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'project_id',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

       public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

  
    public function scopeForProject($query, ?int $projectId)
    {
        return $query->when($projectId, fn ($q) => $q->where('project_id', $projectId));
    }

   
    public static function resequencePriorities(?int $projectId): void
    {
        $tasks = static::forProject($projectId)
            ->orderBy('priority')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->timestamps = false;
            $task->priority = $index + 1;
            $task->save();
        }
    }
}
