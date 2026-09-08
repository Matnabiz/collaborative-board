<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'content',
        'color',
        'pos_x',
        'pos_y',
        'rotation',
        'board_date',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'pos_x' => 'float',
            'pos_y' => 'float',
            'rotation' => 'float',
            'board_date' => 'date',
            'items' => 'array',
        ];
    }

    /**
     * The note belongs to a single author.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
