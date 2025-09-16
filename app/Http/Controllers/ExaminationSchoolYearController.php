<?php

namespace App\Http\Controllers;

use App\Models\ExaminationSchoolyear;
use App\Services\ExaminationSchoolYearService;
use App\Services\ExaminationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\JsonResponse;

class ExaminationSchoolYearController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return (new ExaminationSchoolYearService())->getExaminationSchoolYears($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_year_id' => 'required | integer | exists:school_years,id',
            'examination_id' => 'required | integer | exists:examinations,id',
        ]);

        if($validator->fails()){
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new ExaminationSchoolYearService())->createExaminationSchoolYear($request);
    }

    public function storeExaminationSchoolYears(Request $request): JsonResponse
    {
        //validate request
        $validator = Validator::make($request->all(), [
            'examination_school_years' => 'required|array',
            'examination_school_years.*.school_year_id' => 'required | integer | exists:school_years,id',
            'examination_school_years.*.examination_id' => 'required | integer | exists:examinations,id',
        ]);

        if($validator->fails()){
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $examinationSchoolYears = $request->get('examination_school_years', []);
        $result = ExaminationSchoolYearService::insertExaminationSchoolYears($examinationSchoolYears);

        if ($result) {
            return $this->successResponse([
                'message' => 'Insert success'
            ], 200);
        }

        return $this->errorResponse([
            'message' => 'Insert failed'
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExaminationSchoolyear $examinationSchoolyear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_year_id' => 'required | integer | exists:school_years,id',
            'examination_id' => 'required | integer | exists:examinations,id',
        ]);

        if($validator->fails()){
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new ExaminationSchoolYearService())->updateExaminationSchoolYear($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required | integer | exists:examination_school_year,id',
        ]);

        if($validator->fails()){
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new ExaminationSchoolYearService())->deleteExaminationSchoolYear($request);
    }
}
