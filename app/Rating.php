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
        'score', 'score_max', 'used_tips', 'required_time', 'solve_attempts', 'solution_data'
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
     * @param $solve_attempts
     */
    public function calculateScore($required_time, $used_tips, $tips_max, $solve_attempts)
    {
        // 1/3 of total score, because we rate in 3 sections
        $fullPointsPerSection = env('RATING_SCORE_MAX', 3) / 3;


        // initial point for solving the task
        $score = $fullPointsPerSection;


        // Calculate points for used tips
        if ($used_tips == 0) { // no tip used = full point
            $tips_points = $fullPointsPerSection;
        } else {
            $tips_points = $fullPointsPerSection - ($used_tips * 0.25);

            if (($tips_points < 0) || ($used_tips == $tips_max)) {
                $tips_points = 0;
            }
        }
        $score += $tips_points;


        // Calculate points for solve attempts
        if ($solve_attempts == 1) { // first try = full point
            $attempts_points = $fullPointsPerSection;
        } else {
            $attempts_points = $fullPointsPerSection - ($solve_attempts * 0.25);

            if (($attempts_points < 0)) {
                $attempts_points = 0;
            }
        }
        $score += $attempts_points;


        /*
        // UPDATE: Time is no score rating anymore, keep it here for later

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
        */

        $this->score = $score;
        $this->score_max = env('RATING_SCORE_MAX', 3);
        $this->used_tips = $used_tips;
        $this->required_time = $required_time;
        $this->solve_attempts = $solve_attempts;
    }
}
