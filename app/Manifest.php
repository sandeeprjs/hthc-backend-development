<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Branch;
use App\Franchisee;
use App\Booking;

class Manifest extends Model
{
    use SoftDeletes;

    protected $appends = ['sender_branch', 'receiver_branch', 'sender_franchisee', 'receiver_franchisee'];

    protected $fillable = [
        'manifest_type',
        'manifest_number',
        'origin_branch_id',
        'origin_pincode_id',
        'dest_pincode_id',
        'dest_branch_id',
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'consg_number_id',
        'last_mile_delivery',
        'delivery_user_id',
        'status',
        'remarks',
        'user_id',
        'office_id',
        'office_type'
    ];

    // Change relationships to belongsTo
    public function sender_branch()
    {
        return $this->belongsTo(Branch::class, 'sender_id', 'id')
            ->when($this->sender_type === 'BR', function ($query) {
                return $query->where('branch_type', 'BR');
            });
    }

    public function receiver_branch()
    {
        return $this->belongsTo(Branch::class, 'receiver_id', 'id')
            ->when($this->receiver_type === 'BR', function ($query) {
                return $query->where('branch_type', 'BR');
            });
    }

    public function sender_franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'sender_id', 'id')
            ->when($this->sender_type === 'FR', function ($query) {
                return $query->where('franchisee_type', 'FR');
            });
    }

    public function receiver_franchisee()
    {
        return $this->belongsTo(Franchisee::class, 'receiver_id', 'id')
            ->when($this->receiver_type === 'FR', function ($query) {
                return $query->where('franchisee_type', 'FR');
            });
    }

    // Simplified branch method
    public function branch()
    {
        return $this->sender_type === 'BR'
            ? $this->sender_branch()
            : $this->sender_franchisee();
    }

    // Accessor methods adjusted to use relationships
    public function getSenderBranchAttribute()
    {
        return $this->sender_type === 'BR'
            ? $this->sender_branch()->select(['code', 'branch_name'])->first()
            : null;
    }

    public function getReceiverBranchAttribute()
    {
        return $this->receiver_type === 'BR'
            ? $this->receiver_branch()->select(['code', 'branch_name'])->first()
            : null;
    }

    public function getSenderFranchiseeAttribute()
    {
        return $this->sender_type === 'FR'
            ? $this->sender_franchisee()->select(['code', 'enterprise_name'])->first()
            : null;
    }

    public function getReceiverFranchiseeAttribute()
    {
        return $this->receiver_type === 'FR'
            ? $this->receiver_franchisee()->select(['code', 'enterprise_name'])->first()
            : null;
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'consg_number', 'manifest_number');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
