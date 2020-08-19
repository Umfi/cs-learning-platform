<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     * INTRO/EXTRO TYPES
     */
    CONST LOCAL = "LOCAL";
    const EXTERNAL = "EXTERNAL";
    const TEXT = "TEXT";
    const NONE = "NONE";

    const TYPE_NONE = "NONE";
    const TYPE_VIDEO = "VIDEO";
    const TYPE_IMAGE = "IMAGE";
    const TYPE_TEXT = "TEXT";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'module', 'description', 'intro', 'intro_type', 'extro', 'extro_type', 'difficulty', 'active',
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
     * Ratings
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Jenssegers\Mongodb\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get a user rating.
     *
     * @return string
     */
    public function getUserRatingAttribute()
    {
        return $this->ratings()->where('student_id', Auth::id())->first();
    }

    /**
     * Get type of intro.
     *
     * @return string
     */
    public function getIntroFiletypeAttribute()
    {
        return self::getUploadType($this->intro, $this->intro_type);
    }

    /**
     * Get type of extro.
     *
     * @return string
     */
    public function getExtroFiletypeAttribute()
    {
        return self::getUploadType($this->extro, $this->extro_type);
    }

    /**
     * Remove module specific configuration from model
     */
    public function removeModuleSpecificConfig()
    {
        $this->specification = "";
        $this->solution = "";
        $this->tips = array();
    }

    /**
     * Get the Type of a file
     *
     * @param $file
     * @param $type
     * @return string
     */
    private function getUploadType($file, $type)
    {
        if (empty($file)) {
            return self::TYPE_NONE;
        }

        if ($type == self::LOCAL) {
            if (Storage::disk('public')->exists($file)) {
                $mime = mime_content_type(Storage::disk('public')->path($file));
                if (strstr($mime, "video/")) {
                    return self::TYPE_VIDEO;
                } else if (strstr($mime, "image/")) {
                    return self::TYPE_IMAGE;
                }
            }
        } else if ($type == self::EXTERNAL) {

            $ch = curl_init($file);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            if (strstr($mime, "video/")) {
                return self::TYPE_VIDEO;
            } else if (strstr($mime, "image/")) {
                return self::TYPE_IMAGE;
            }
        } else if ($type == self::TEXT) {
            return self::TYPE_TEXT;
        }


        return self::TYPE_NONE;
    }

    /**
     * Store module specific data
     * Adopt this function if new modules are added!
     *
     * @param $request
     * @return bool
     */
    public function storeModuleConfig($request)
    {

        $data = json_decode($request->get('data'));

        if (isset($data->tips)) {
            $this->tips = $data->tips;
        } else {
            $this->tips = array();
        }

        switch ($request->get('module')) {
            case "MODULE_SPREADSHEET":
            {

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
                $solution->dataFormulaEvaluated = $data->solutionDataFormulaEvaluated;
                $solution->code = $data->solutionCode;

                $this->solution = $solution;

                return true;
            }
            default:
            {
                return false;
            }
        }
    }

    /**
     * Check if a given solution is correct
     * Adopt this function if new modules are added!
     *
     * @param $request
     * @return bool
     */
    public function checkSolution($request)
    {

        $data = json_decode($request->get('data'));

        switch ($request->get('module')) {
            case "MODULE_SPREADSHEET":
            {

                if ($data->programming) {
                    $result = json_encode($data->resultDataFormulaEvaluated);
                    $solution = json_encode($this->solution['dataFormulaEvaluated']);
                } else {
                    $result = json_encode($data->resultData);
                    $solution = json_encode($this->solution['data']);
                }

                $res = str_replace('"', "", $result);
                $res_clean = str_replace('null', "", $res);

                $sol = str_replace('"', "", $solution);
                $sol_clean = str_replace('null', "", $sol);

                if (strcasecmp($res_clean, $sol_clean) == 0) {
                    return true;
                }

                return false;
            }
            default:
            {
                return false;
            }
        }
    }

}
