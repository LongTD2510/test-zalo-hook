<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentsRequest;
use App\Models\StudentInformation;
use App\Services\StudentInformationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\GetListStudentInfoRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Examination;
use App\Models\SchoolYear;
use App\Models\ExaminationSchoolYear;

class StudentInformationController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(GetListStudentInfoRequest $request) {}
    public function getList(GetListStudentInfoRequest $request)
    {
        return (new StudentInformationService)->getListStudentInformation($request);
    }

    public function getStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidateNumber' => 'required|string',
            'year' => 'required',
            'exam' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new StudentInformationService)->getStudent($request);
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
            'info' => 'required|array',
            'info.*.full_name' => 'required|string',
            'info.*.student_id' => 'required',
            'year' => 'required',
            'exam' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new StudentInformationService)->createStudentInformation($request);
    }

    public function storeStudents(StoreStudentsRequest $request): JsonResponse
    {
        $students = $request->get('students', []);
        $result = StudentInformationService::insertStudentInformation($students);

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
    public function show(StudentInformation $studentInformation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new StudentInformationService)->updateStudentInformation($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        return (new StudentInformationService)->deleteStudentInformation($request);
    }

    public function getExamsYearsFilter()
    {
        try {
            $data = StudentInformationService::getExamsYearsFilterInfo();
            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse([
                'message' => $e
            ], 500);
        }
    }

    public function mapSchoolYearsWithExam()
    {
        $exams = Examination::get();
        $years = SchoolYear::get();
        foreach ($exams as $exam) {
            foreach ($years as $year) {
                $examExisted = ExaminationSchoolYear::query()->where('examination_id', $exam->id)
                    ->where('school_year_id', $year->id)
                    ->exists();
                if (!$examExisted) {
                    ExaminationSchoolYear::insert([
                        'examination_id' => $exam->id,
                        'school_year_id' => $year->id
                    ]);
                }
            }
        }
    }

    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'info' => 'required|array',
            'info.*.id' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->errorResponse([
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $message = (new StudentInformationService)->bulkUpdateStudentInformation($request);
        return $this->successResponse([
            'data' => $message
        ]);
    }

    public function monthQuarter(Request $request)
    {
        try {
            $data = StudentInformationService::getMonthQuarter();
            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
