<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Consignment extends Model
{
    use SoftDeletes;

    // Add with property to eagerly load the office relationship
    protected $with = ['office'];

    protected $appends = ['UsedConsignment'];

    // Define fillable properties
    protected $fillable = [
        'consg_number',
        'batch_id',
        'office_type',
        'office_id',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the office that owns the consignment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        if ($this->office_type == 'FR') {
            return $this->belongsTo(Franchisee::class, 'office_id', 'id');
        } else {
            return $this->belongsTo(Branch::class, 'office_id', 'id');
        }
    }

    /**
     * Get the branch if office_type is not FR.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'office_id', 'id');
    }

    /**
     * Get the franchisee if office_type is FR.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'office_id', 'id');
    }

    /**
     * Define the relationship with bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'consg_number', 'consg_number');
    }

    /**
     * Get all consignments from the same batch.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batchConsignments()
    {
        return $this->hasMany(Consignment::class, 'batch_id', 'batch_id');
    }

    /**
     * Check if user has delete permission.
     *
     * @param int $moduleId
     * @return bool
     */
    public function hasDeletePermission($moduleId)
    {
        $user = Auth::user();

        // Admin always has permission
        if ($user->id == 1) {
            return true;
        }

        // Check if permissions relationship exists in the model
        if (method_exists($this, 'permissions')) {
            $permission = $this->permissions()->where('module_id', $moduleId)->first();
            return $permission ? $permission->delete : false;
        }

        return false;
    }

    /**
     * Define permissions relationship if it exists.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|null
     */
    public function permissions()
    {
        if (class_exists('App\Permission')) {
            return $this->belongsToMany(Permission::class);
        }

        return null;
    }

    /**
     * Get the count of used consignments.
     *
     * @return int
     */
    public function getUsedConsignmentAttribute()
    {
        return Booking::where('consg_number', $this->consg_number)->count();
    }

    /**
     * Scope a query to get unused consignments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnused($query)
    {
        return $query->whereNotExists(function ($query) {
            $query->select(\DB::raw(1))
                ->from('bookings')
                ->whereRaw('bookings.consg_number = consignments.consg_number');
        });
    }

    /**
     * Scope a query to get used consignments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsed($query)
    {
        return $query->whereExists(function ($query) {
            $query->select(\DB::raw(1))
                ->from('bookings')
                ->whereRaw('bookings.consg_number = consignments.consg_number');
        });
    }

    /**
     * Scope a query to filter by batch ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $batchId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope a query to filter by office type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOfficeType($query, $type)
    {
        return $query->where('office_type', $type);
    }
}
