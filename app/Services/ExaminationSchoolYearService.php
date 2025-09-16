<?php

namespace App\Services;

use App\Models\ExaminationSchoolYear;
use App\Models\StudentInformation;
use Illuminate\Support\Facades\DB;

class ExaminationSchoolYearService
{
    public function getExaminationSchoolYears($request)
    {
        $searchId = $request->input('id');
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');

        try {
            $query = ExaminationSchoolYear::query();

            if ($searchId) {
                $query->where('id', $searchId);
            }
            $query->orderBy($sort, $direction);
            $data = $query->get();
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createExaminationSchoolYear($request)
    {
        $schoolYearId = $request->input('school_year_id');
        $examinationId = $request->input('examination_id');

        try {
            $examinationSchoolYear = new ExaminationSchoolYear();
            $examinationSchoolYear->school_year_id = $schoolYearId;
            $examinationSchoolYear->examination_id = $examinationId;
            $examinationSchoolYear->save();

            return response()->json([
                'status' => 'success',
                'data' => $examinationSchoolYear
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public static function insertExaminationSchoolYears($examinationSchoolYears)
    {
        if (empty($examinationSchoolYears)) {
            return false;
        }

        try {
            DB::beginTransaction();
            foreach ($examinationSchoolYears as &$examinationSchoolYear) {
                $examinationSchoolYear['created_at'] = now();
                $examinationSchoolYear['updated_at'] = now();
            }

            // không cần check, db đã có khoá unique cặp school_year_id và examination_id
            $result = ExaminationSchoolYear::query()->insert($examinationSchoolYears);

            if ($result) {
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function updateExaminationSchoolYear($request)
    {
        $id = $request->input('id');
        $schoolYearId = $request->input('school_year_id');
        $examinationId = $request->input('examination_id');

        try {
            $examinationSchoolYear = ExaminationSchoolYear::query()
                ->where('id', $id)
                ->firstOrFail();

            $examinationSchoolYear->school_year_id = $schoolYearId;
            $examinationSchoolYear->examination_id = $examinationId;
            $examinationSchoolYear->save();

            return response()->json([
                'status' => 'success',
                'data' => $examinationSchoolYear
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteExaminationSchoolYear($request)
    {
        $id = $request->input('id');

        try {
            $examinationSchoolYear = ExaminationSchoolYear::query()
                ->where('id', $id)
                ->firstOrFail();

            $examinationSchoolYear->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
