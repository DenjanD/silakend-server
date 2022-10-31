<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

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
        'nip',
        'password',
        'name',
        'address',
        'phone',
        'email',
        'role_id',
        'unit_id'
    ];

    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userRole() {
        return $this->hasMany(UserRole::class, 'user_id', 'user_id');
    }

    public function role() {
        return $this->hasManyThrough(
            Role::class, //deployment class
            UserRole::class, //env class
            'user_id', // FK on env
            'role_id', // FK on deployment
            'user_id', // Local Key on Projects class (in this class)
            'role_id'); // Local Key on env class
    }

    public function jobUnit() {
        return $this->hasOne(JobUnit::class, 'unit_id', 'unit_id')
                    ->select(['unit_id','name']);
    }
}
