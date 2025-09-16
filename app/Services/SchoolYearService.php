<?php

namespace App\Services;

use App\Models\Examination;
use App\Models\SchoolYear;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SchoolYearService
{
    use ApiResponse;

    public function __construct()
    {
        //
    }

    public function getSchoolYears($request): JsonResponse
    {
        try {
            $q = $request->query('q', null);
            $sort = $request->query('sort', 'created_at');
            $direction = $request->query('direction', 'desc');

            $data = SchoolYear::query()
                ->when($q, function ($query, $q) {
                    return $query->where('year', 'like', "%$q%");
                })
                ->orderBy($sort, $direction)
                ->get();

            return $this->successResponse([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return $this->errorResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500
            );
        }
    }

    public function createSchoolYear($request): JsonResponse
    {
        try {
            $year = $request->input('year');
            $schoolYear = SchoolYear::query()->firstOrCreate([
                'year' => $year
            ]);

            if ($schoolYear->wasRecentlyCreated) {
                return $this->successResponse([
                    'status' => 'success',
                ], 200);
            } else {
                return $this->errorResponse([
                    'message' => 'School Year already exists',
                    'status' => 409
                ], 409);
            }
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public static function insertSchoolYears($schoolYears): bool
    {
        if (empty($schoolYears)) {
            return false;
        }

        try {
            DB::beginTransaction();
            $schoolYearsDb = SchoolYear::query()
                ->where('deleted_at', null)
                ->get()
                ->pluck('year')
                ->toArray();

            $schoolYears = array_map(function ($schoolYear) use ($schoolYearsDb) {
                $year = Arr::get($schoolYear, 'year');
                if (!in_array($year, $schoolYearsDb)) {
                    return [
                        'year' => $year,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }, $schoolYears);

            $schoolYears = array_filter($schoolYears);

            if (empty($schoolYears)) {
                return false;
            }

            // insert
            $result = SchoolYear::query()->insert($schoolYears);
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

    public function updateSchoolYear($request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $year = $request->input('year');
            $schoolYear = SchoolYear::query()
                ->where('id', $id)
                ->firstOrFail();
            $schoolYear->year = $year;
            $schoolYear->update([
                'year' => $year
            ]);
            return $this->successResponse([
                'status' => 'updated success',
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteSchoolYear($request): JsonResponse
    {
        try {
            $id = $request->input('id');
            $schoolYear = SchoolYear::query()
                ->where('id', $id)
                ->firstOrFail();
            $schoolYear->delete();
            return $this->successResponse([
                'status' => 'deleted success',
            ], 200);
        } catch (\Exception $e) {
            return $this->errorResponse([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
