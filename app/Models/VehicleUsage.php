<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleUsage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if ( ! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'user_id',
        'ucategory_id',
        'usage_description',
        'personel_count',
        'destination',
        'start_date',
        'end_date',
        'depart_date',
        'depart_time',
        'arrive_date',
        'arrive_time',
        'distance_count_out',
        'distance_count_in',
        'status',
        'status_description'
    ];

    protected $primaryKey = 'usage_id';

    public function vehicle() {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id')->select(['vehicle_id','name']);;
    }

    public function driver() {
        return $this->belongsTo(User::class, 'driver_id', 'user_id')->select(['user_id','name']);;
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id')->select(['user_id','name']);;
    }

    public function category() {
        return $this->belongsTo(UsageCategory::class, 'ucategory_id', 'ucategory_id')->select(['ucategory_id','name']);;
    }
}
