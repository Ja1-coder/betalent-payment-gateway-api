<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 * @property int $priority Lower values represent higher priority
 */
class Gateway extends Model
{
    protected $fillable = ['name', 'is_active', 'priority'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}