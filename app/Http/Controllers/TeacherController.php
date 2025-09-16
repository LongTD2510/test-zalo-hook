<?php

namespace App\Http\Controllers;

use App\Services\TeacherService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    use ApiResponse;
    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}

    public function getTeachers(Request $request) {
        $teachers = TeacherService::getTeachers($request);
        return $this->successResponse([
            'status' => 'success',
            'data' => $teachers
        ], 200);
    }

    public function getTeacherById($id) {
        $teacher = TeacherService::getTeacherById($id);
        return $this->successResponse([
            'status' => 'success',
            'data' => $teacher
        ], 200);
    }

}
