<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mode extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',          // Unique code for the mode
        'name',          // Name of the mode
        'type',          // Type of the mode
        'description',   // Optional description
        'created_at',    // Timestamps for creation
        'updated_at',    // Timestamps for updates
        'deleted_at',    // Timestamps for soft deletes
    ];

    /**
     * Define a one-to-many relationship with the Dispatch model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'mode_id');
    }
}
