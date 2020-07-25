<?php

namespace App;

use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'active', 'shared'
    ];

    /**
     * Teacher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Students
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Topics
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }


    /**
     * Set the course's code. Generates a unique code.
     *
     * @param  string  $value
     * @return void
     */
    public function setCodeAttribute($value)
    {
        while( $this->query()->where('code','=', $value)->first() ){
            $value = Str::random(6);
        }

        $this->attributes['code'] = strtoupper($value);
    }
}
