<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            "user" => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role_id' => $this->role()->first()->id,
                'role_name' => $this->role()->first()->name,
                'permissions' => $this->permissions(),
            ]
        ];
    }

    /**
     * Define a relationship where the user belongs to a role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function permissions()
    {
        $permissions = optional($this->role)->permissions;

        // TODO: Add cache and clear if user has new session

        return $permissions->pluck('permission_code');
    }

    public function formattedPermissions() // jadi name dan code
    {
        return optional($this->role)->permissions->pluck('permission_code')->map(function ($permission) {
            return [
                'name' => ucfirst(str_replace('_', ' ', $permission)),
                'code' => $permission,
            ];
        });
    }

    public function hasPermission($permission)
    {
        if ($this->role?->name == 'Admin') {
            return true;
        }

        return optional($this->role)->permissions->contains('permission_code', $permission);
    }

    /**
     * Boot the model.
     *
     * This method is called when the model is booted and is used to set up any
     * bindings, observers, and other bootstrapping that needs to happen.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
