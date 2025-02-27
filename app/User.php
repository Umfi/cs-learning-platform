<?php

namespace App;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * USER ROLES
     */
    const ROLE_ADMIN = 'ADMIN';
    const ROLE_STUDENT = 'STUDENT';
    const ROLE_TEACHER = 'TEACHER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'active', 'language'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Courses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'user_ids');
    }

    /**
     * Ratings
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|\Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'student_id');
    }


    /**
     * Check if current User has Role ADMIN
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    /**
     * Check if current User has Role TEACHER
     *
     * @return bool
     */
    public function isTeacher()
    {
        return $this->role == self::ROLE_TEACHER;
    }

    /**
     * Check if current User has Role STUDENT
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->role == self::ROLE_STUDENT;
    }
}
