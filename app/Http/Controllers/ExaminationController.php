<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Services\ExaminationService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class ExaminationController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       return (new ExaminationService)->getExaminations($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse([
                    'message' => $validator->errors()->toJson()
                ], 400
            );
        }

        return (new ExaminationService)->createExamination($request);
    }

    public function storeExaminations(Request $request): JsonResponse
    {
        $examinations = $request->get('examinations', []);
        $result = ExaminationService::insertExaminations($examinations);

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
    public function show(Examination $examination)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Examination $examination)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'exam' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400 );
        }

        return (new ExaminationService)->updateExamination($request);
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
            return $this->errorResponse([
                    'message' => $validator->errors()->toJson()
                ], 400);
        }

        return (new ExaminationService)->deleteExamination($request);
    }

}
