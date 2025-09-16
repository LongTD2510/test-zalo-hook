<?php

namespace App\Services;

use App\Models\ExaminationSchoolYear;
use App\Models\Examination;
use App\Models\SchoolYear;
use App\Models\StudentInformation;
use App\Models\Teacher;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\StudentInfoSearchTypeEnum;

class TeacherService
{
    public static function getTeachers($request): array
    {
        $limit = $request->query('limit', null);
        if (isset($limit)) {
            $teachers = Teacher::limit($limit)->get();
        } else {
            $teachers = Teacher::get();
        }
        return $teachers->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'age' => $teacher->age,
                'bullet_point' => $teacher->bullet_point,
                'motto' => $teacher->motto,
                'quote' => $teacher->quote,
                'viewpoint' => $teacher->viewpoint,
                'file_url' => $teacher->file_url,
                'short_description' => $teacher->short_description
            ];
        })->toArray();
    }

    public static function getTeacherById($id): array
    {
        $teacher = Teacher::query()->find($id);
        return [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'age' => $teacher->age,
            'bullet_point' => $teacher->bullet_point,
            'motto' => $teacher->motto,
            'quote' => $teacher->quote,
            'viewpoint' => $teacher->viewpoint,
            'file_url' => $teacher->file_url,
            'short_description' => $teacher->short_description,
            'description' => $teacher->description
        ];
    }
}
