<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Rating extends Model
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
        'score', 'score_max', 'used_tips', 'required_time'
    ];

    /**
     * Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Calculate a score from given parameters
     */
    public function calculateScore($required_time) {

        // TODO: implement correct way for calculating score

        $this->score = 3;
        $this->score_max = 3;
        $this->used_tips = 0;
        $this->required_time = $required_time;
    }
}
