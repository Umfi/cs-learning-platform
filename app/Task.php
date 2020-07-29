<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    /**
     * Task Modules
     * Add here new modules
     */
    const MODULES = array(
        "MODULE_SPREADSHEAT" => "Spreadsheat",
    );

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'module', 'description', 'intro', 'extro', 'difficulty', 'active',
        'specification', 'solution', 'tips'
    ];

    /**
     * Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\Jenssegers\Mongodb\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Remove module specific configuration from model
     */
    public function removeModuleSpecificConfig() {
        $this->specification = "";
        $this->solution = "";
        $this->tips = "";
    }

}
