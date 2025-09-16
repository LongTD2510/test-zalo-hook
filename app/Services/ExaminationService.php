<?php

namespace App\Services;

use App\Http\Requests\CreateExaminationRequest;
use App\Models\Examination;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExaminationService
{
    use ApiResponse;
    public function __construct()
    {
        //
    }

    public function getExaminations($request): JsonResponse
    {
        try {
            $q = $request->query('q');
            $sort = $request->query('sort', 'created_at');
            $direction = $request->query('direction', 'desc');

            $data = Examination::query()
                ->where('exam', 'like', "%$q%")
                ->orderBy($sort, $direction)
                ->get();

            return $this->successResponse([
                'status' => 'success',
                'data' => $data
            ], 200);

        }catch (\Exception $e) {
            return $this->errorResponse(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500
            );
        }
    }

    public function createExamination($request): JsonResponse
    {
        try {
            $exam = $request->input('exam');
            $examination = Examination::query()->firstOrCreate([
                'exam' => $exam
            ]);

            if ($examination->wasRecentlyCreated) {
                return $this->successResponse([
                    'status' => 'success',
                ]);
            } else {
                return $this->errorResponse([
                    'message' => 'Examination already exists',
                    'status' => 409
                ]);
            }
        }catch (\Exception $e) {
            return $this->errorResponse(
                [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500
            );
        }
    }

    public static function insertExaminations($examinations): bool
    {
        if (empty($examinations)) {
            return false;
        }

        try {
            foreach ($examinations as &$exam) {
                $exam['created_at'] = now();
                $exam['updated_at'] = now();
            }

            DB::beginTransaction();
            $result = Examination::query()->insert($examinations);
            if (!$result) {
                DB::rollBack();
                return false;
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function updateExamination($request): JsonResponse
    {
        try{
            $id = $request->input('id');
            $exam = $request->input('exam');
            $examination = Examination::query()
                ->where('id', $id)
                ->firstOrFail();
            $examination->update([
                'exam' => $exam
            ]);
            return $this->successResponse([
                'status' => 'success',
                'message' => 'Examination updated'
            ], 200);
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function deleteExamination($request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $examination = Examination::query()
                ->where('id', $id)
                ->firstOrFail();
            $examination->delete();
            return $this->successResponse([
                'status' => 'success',
                'message' => 'Examination deleted'
            ], 200);
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
