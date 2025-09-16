<?php

namespace App\Http\Controllers;

use App\Models\Schoolyear;
use App\Services\ExaminationService;
use App\Services\SchoolYearService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\JsonResponse;

class SchoolYearController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

    }

    public function getList(Request $request)
    {
        return (new SchoolYearService())->getSchoolYears($request);
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
            'year' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors()->toJson(), 400);
        }

        return (new SchoolYearService())->createSchoolYear($request);
    }

    public function storeSchoolYears(Request $request) :JsonResponse
    {
        $schoolYears = $request->get('school_years', []);
        $result = SchoolYearService::insertSchoolYears($schoolYears);

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
    public function show(Schoolyear $schoolyear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schoolyear $schoolyear)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'year' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors()->toJson(), 400);
        }

        return (new SchoolYearService())->updateSchoolYear($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if($validator->fails()){
            return $this->errorResponse($validator->errors()->toJson(), 400);
        }

        return (new SchoolYearService())->deleteSchoolYear($request);
    }
}
