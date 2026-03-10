<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * * Represents a system user with specific access roles.
 * * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role
 * @property string $role_name Accessor to get the string representation of the role
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @var int Default user role with basic permissions */
    const ROLE_USER = 0;

    /** @var int Role for financial management and refunds */
    const ROLE_FINANCE = 1;

    /** @var int Role for product and user management */
    const ROLE_MANAGER = 2;

    /** @var int Administrator role with full system access */
    const ROLE_ADMIN = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    /**
     * Accessor to get the human-readable role name.
     * * @return string
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_FINANCE => 'FINANCE',
            self::ROLE_MANAGER => 'MANAGER',
            self::ROLE_ADMIN   => 'ADMIN',
            default            => 'USER',
        };
    }

    /**
     * Translates a string role name into its corresponding integer value.
     *
     * @param string $roleName
     * @return int
     */
    public static function getRoleValue(string $roleName): int
    {
        return match (strtoupper($roleName)) {
            'FINANCE' => self::ROLE_FINANCE,
            'MANAGER' => self::ROLE_MANAGER,
            'ADMIN'   => self::ROLE_ADMIN,
            default   => self::ROLE_USER,
        };
    }
}
