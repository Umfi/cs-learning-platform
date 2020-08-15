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
     * Max value of score
     */
    const MAX_SCORE = 3;

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
     * - this could easily be adopted
     *
     * @param $required_time
     * @param $used_tips
     * @param $tips_max
     */
    public function calculateScore($required_time, $used_tips, $tips_max)
    {

        // 1 point initial for solving the task
        $score = 1;

        // Calculate points for used tips
        if ($used_tips == 0) { // no tip used = 1 point
            $tips_points = 1;
        } else {
            if ($used_tips == $tips_max) { // all tips used = 0 points
                $tips_points = 0;
            } else {
                $tips_points = 1 - ($used_tips * 0.25);

                if ($tips_points < 0) {
                    $tips_points = 0;
                }
            }

        }

        $score += $tips_points;

        // Calculate points for required time
        if ($required_time < (60 * 6)) { // less than 6 minutes
            $time_points = 1;
        } else if ($required_time < (60 * 10)) { // less than 10 minutes
            $time_points = 0.75;
        } else if ($required_time < (60 * 15)) { // less than 15 minutes
            $time_points = 0.5;
        } else if ($required_time < (60 * 20)) { // less than 20 minutes
            $time_points = 0.25;
        } else {
            $time_points = 0;
        }

        $score += $time_points;

        $this->score = $score;
        $this->score_max = self::MAX_SCORE;
        $this->used_tips = $used_tips;
        $this->required_time = $required_time;
    }
}
