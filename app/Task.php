<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use stdClass;

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
        "MODULE_SPREADSHEET" => "Spreadsheet",
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

    /**
     * @param $data
     */
    public function storeModuleConfig($request) {

        switch ($request->get('module')) {
            case "MODULE_SPREADSHEET": {

                $data = json_decode($request->get('data'));

                $this->tips = $data->tips;

                $specification = new stdClass();
                $specification->row = $data->row;
                $specification->col = $data->col;
                $specification->programming = $data->programming;
                $specification->dataVisualization = $data->dataVisualization;
                $specification->data = $data->specificationData;
                $specification->code = $data->specificationCode;

                $this->specification = $specification;

                $solution = new stdClass();
                $solution->row = $data->row;
                $solution->col = $data->col;
                $solution->programming = $data->programming;
                $solution->dataVisualization = $data->dataVisualization;
                $solution->data = $data->solutionData;
                $solution->code = $data->solutionCode;

                $this->solution = $solution;

                return true;
            }
            default: {
                return false;
            }
        }
    }

}
