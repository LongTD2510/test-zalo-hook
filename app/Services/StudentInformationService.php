<?php

namespace App\Services;

use App\Models\ExaminationSchoolYear;
use App\Models\Examination;
use App\Models\SchoolYear;
use App\Models\StudentInformation;
use App\Models\NotifyMonthQuarter;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Enums\StudentInfoSearchTypeEnum;

class StudentInformationService
{
    use ApiResponse;

    protected const PER_PAGE = 5;
    public function getListStudentInformation($request): JsonResponse
    {
        $search = $request->query('q');
        $year = $request->query('school_year');
        $examType = $request->query('exam_type');
        $sort = $request->query('sort', 'updated_at');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', self::PER_PAGE);
        $page = $request->query('page', 1);
        try {
            $query = StudentInformation::query();
            $examSchoolYearIds = self::mapSchoolYearAndExamType($year, $examType);
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('full_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('student_id', $search)
                        ->orWhere('contact', $search);
                });
            }
            $query->when(!empty($examSchoolYearIds), function ($q) use ($examSchoolYearIds) {
                $q->whereIn('exam_school_year_id', $examSchoolYearIds);
            });
            $query->with(['examinationSchoolYear.examinations', 'examinationSchoolYear.schoolYears']);
            $query->selectRaw('*, (COALESCE(english, 0) + COALESCE(literature, 0) + COALESCE(math, 0)) as total');
            // $query->when($sort == 'school_year', function ($q) use ($direction) {
            //     $q->when($direction == 'desc', function ($query) {
            //         $query->sortByDesc('examinationSchoolYear.schoolYears.year');
            //     });
            //     $q->when($direction == 'asc', function ($query) {
            //         $query->sortByAsc('examinationSchoolYear.schoolYears.year');
            //     });
            // });
            // $query->when($sort != 'school_year', function ($q) use ($sort, $direction) {
            //     $q->orderBy($sort, $direction);
            // });
            $query->when($sort == 'external_id', function ($q) {
                $q->orderByRaw('CAST(external_id AS UNSIGNED)');
            });
            $query->when($sort != 'external_id', function ($q) use ($sort, $direction) {
                $q->orderBy($sort, $direction);
            });
            $data = $query->paginate($perPage);
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

    public static function getStudent($request): JsonResponse
    {
        $candidateNumber = $request->input('candidateNumber');
        $year = $request->input('year');
        $exam = $request->input('exam');
        $searchType = $request->input('type');
        $examSchoolYearId = self::mapExactSchoolYearAndExamType($year, $exam);
        if (!isset($examSchoolYearId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Year or exam could not be accepted'
            ], 500);
        }
        try {
            if (isset($candidateNumber) && strval($candidateNumber)[0] == '0') {
                $candidateNumber = substr(strval($candidateNumber), 1);
            }
            if ($searchType == StudentInfoSearchTypeEnum::EXAM_RESULT) {
                $data = StudentInformation::query()
                    ->where(function ($q) use ($candidateNumber) {
                        $q->where('contact', $candidateNumber)
                            ->orWhere('contact2', $candidateNumber)
                            ->orWhere('student_id', $candidateNumber);
                    })
                    ->where('exam_school_year_id', $examSchoolYearId)
                    ->first();
            } else if ($searchType == StudentInfoSearchTypeEnum::STUDENT_ID) {
                $data = StudentInformation::query()
                    ->where('contact', $candidateNumber)
                    ->where('exam_school_year_id', $examSchoolYearId)
                    ->first();
            }

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

    public static function insertStudentInformation($students): bool
    {
        if (empty($students)) {
            return false;
        }

        try {
            DB::beginTransaction();
            foreach ($students as &$student) {
                $student['student_id'] = md5(Str::uuid() . Str::random(4));
                $student['created_at'] = now();
                $student['updated_at'] = now();
            }
            //insert db
            $result = StudentInformation::query()->insert($students);
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


    public function createStudentInformation($request): JsonResponse
    {
        $message = [];
        $infos = $request->input('info');
        $year = $request->input('year');
        $exam = $request->input('exam');
        $examSchoolYearId = self::mapExactSchoolYearAndExamType($year, $exam);
        if (!isset($examSchoolYearId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Year or exam could not be accepted'
            ], 500);
        }


        foreach ($infos as $info) {
            try {
                $fullName = strval($info['full_name']);
                $studentId = strval($info['student_id']);
                $room = isset($info['room']) ? $info['room'] : null;
                $location = isset($info['location']) ? $info['location'] : null;
                $math = isset($info['math_score']) ? $info['math_score'] :  null;
                $english = isset($info['english_score']) ? $info['english_score'] : null;
                $literature = isset($info['literature_score']) ? $info['literature_score'] : null;
                $birthDate = isset($info['birth_date']) ? Carbon::parse($info['birth_date'])->toDateTimeString() : null;
                $linkExam = isset($info['link_exam']) ? $info['link_exam'] : null;
                $contact = isset($info['contact']) ? strval($info['contact']) : null;
                $externalId = isset($info['external_id']) ? $info['external_id'] : null;
                $time = isset($info['time']) ? $info['time'] : null;
                $contact2 = isset($info['contact2']) ? strval($info['contact2']) : null;
                $subject = isset($info['subject']) ? $info['subject'] : null;
                if (isset($contact) && ($contact)[0] == '0') {
                    $contact = substr($contact, 1);
                }
                if (isset($contact2) && ($contact2)[0] == '0') {
                    $contact2 = substr($contact2, 1);
                }
                if (Carbon::parse($birthDate) < Carbon::parse('1970-12-30 00:00:00')) {
                    $birthDate = null;
                }
                $dataExisted = StudentInformation::query()
                    // ->where('full_name', $fullName)
                    ->where('student_id', $studentId)
                    ->where('exam_school_year_id', $examSchoolYearId)
                    ->first();

                if (isset($dataExisted)) {
                    $infoId = $dataExisted->id;
                    if (isset($fullName) && isset($dataExisted->full_name) && $fullName != $dataExisted->full_name) {
                        $message['infos'][$infoId]['data']['full_name']['old'] = $dataExisted->full_name;
                        $message['infos'][$infoId]['data']['full_name']['new'] = $fullName;
                    }

                    if (isset($room) && isset($dataExisted->room) && $room != $dataExisted->room && !self::checkEmptyCell($room)) {
                        $message['infos'][$infoId]['data']['room']['old'] = $dataExisted->room;
                        $message['infos'][$infoId]['data']['room']['new'] = $room;
                    }

                    if (isset($math) && isset($dataExisted->math) && $math != $dataExisted->math && !self::checkEmptyCell($math)) {
                        $message['infos'][$infoId]['data']['math']['old'] = $dataExisted->math;
                        $message['infos'][$infoId]['data']['math']['new'] = $math;
                    }

                    if (isset($english) && isset($dataExisted->english) && $english != $dataExisted->english && !self::checkEmptyCell($english)) {
                        $message['infos'][$infoId]['data']['english']['old'] = $dataExisted->english;
                        $message['infos'][$infoId]['data']['english']['new'] = $english;
                    }

                    if (isset($literature) && isset($dataExisted->literature) && $literature != $dataExisted->literature && !self::checkEmptyCell($literature)) {
                        $message['infos'][$infoId]['data']['literature']['old'] = $dataExisted->literature;
                        $message['infos'][$infoId]['data']['literature']['new'] = $literature;
                    }

                    if (isset($birthDate) && isset($dataExisted->birth_date) && $birthDate != $dataExisted->birth_date && !self::checkEmptyCell($birthDate)) {
                        $message['infos'][$infoId]['data']['birth_date']['old'] = $dataExisted->birth_date;
                        $message['infos'][$infoId]['data']['birth_date']['new'] = $birthDate;
                    }

                    if (isset($linkExam) && isset($dataExisted->link_exam) && $linkExam != $dataExisted->link_exam && !self::checkEmptyCell($linkExam)) {
                        $message['infos'][$infoId]['data']['link_exam']['old'] = $dataExisted->link_exam;
                        $message['infos'][$infoId]['data']['link_exam']['new'] = $linkExam;
                    }

                    if (isset($contact) && isset($dataExisted->contact) && $contact != $dataExisted->contact  && !self::checkEmptyCell($contact)) {
                        $message['infos'][$infoId]['data']['contact']['old'] = $dataExisted->contact;
                        $message['infos'][$infoId]['data']['contact']['new'] = $contact;
                    }

                    if (isset($externalId) && isset($dataExisted->external_id) && $externalId != $dataExisted->external_id  && !self::checkEmptyCell($externalId)) {
                        $message['infos'][$infoId]['data']['external_id']['old'] = $dataExisted->external_id;
                        $message['infos'][$infoId]['data']['external_id']['new'] = $externalId;
                    }

                    if (isset($location) && isset($dataExisted->location) && $location != $dataExisted->location  && !self::checkEmptyCell($location)) {
                        $message['infos'][$infoId]['data']['location']['old'] = $dataExisted->location;
                        $message['infos'][$infoId]['data']['location']['new'] = $location;
                    }

                    if (isset($contact2) && isset($dataExisted->contact2) && $contact2 != $dataExisted->contact2  && !self::checkEmptyCell($contact2)) {
                        $message['infos'][$infoId]['data']['contact2']['old'] = $dataExisted->contact2;
                        $message['infos'][$infoId]['data']['contact2']['new'] = $contact2;
                    }

                    if (isset($time) && isset($dataExisted->time) && $time != $dataExisted->time  && !self::checkEmptyCell($time)) {
                        $message['infos'][$infoId]['data']['time']['old'] = $dataExisted->time;
                        $message['infos'][$infoId]['data']['time']['new'] = $time;
                    }

                    if (isset($subject) && isset($dataExisted->subject) && $subject != $dataExisted->subject  && !self::checkEmptyCell($subject)) {
                        $message['infos'][$infoId]['data']['subject']['old'] = $dataExisted->subject;
                        $message['infos'][$infoId]['data']['subject']['new'] = $subject;
                    }

                    if (!empty($message['infos'][$infoId])) {
                        // $message['infos'][$infoId]['full_name'] = $dataExisted->full_name;
                        $message['infos'][$infoId]['student_id'] = $studentId;
                        $message['infos'][$infoId]['id'] = $infoId;
                    } else {
                        if (isset($fullName)) {
                            $dataExisted->full_name = $fullName;
                        }

                        if (isset($room)) {
                            $dataExisted->room = self::handleEmptyCell($room);
                        }

                        if (isset($birthDate)) {
                            $dataExisted->birth_date = self::handleEmptyCell($birthDate);
                        }

                        if (isset($linkExam)) {
                            $dataExisted->link_exam = self::handleEmptyCell($linkExam);
                        }

                        if (isset($math)) {
                            $dataExisted->math = self::handleEmptyCell($math);
                        }

                        if (isset($english)) {
                            $dataExisted->english = self::handleEmptyCell($english);
                        }

                        if (isset($literature)) {
                            $dataExisted->literature = self::handleEmptyCell($literature);
                        }

                        if (isset($contact)) {
                            $dataExisted->contact = self::handleEmptyCell($contact);
                        }

                        if (isset($externalId)) {
                            $dataExisted->external_id = self::handleEmptyCell($externalId);
                        }

                        if (isset($location)) {
                            $dataExisted->location = self::handleEmptyCell($location);
                        }

                        if (isset($time)) {
                            $dataExisted->time = self::handleEmptyCell($time);
                        }

                        if (isset($contact2)) {
                            $dataExisted->contact2 = self::handleEmptyCell($contact2);
                        }

                        if (isset($subject)) {
                            $dataExisted->subject = self::handleEmptyCell($subject);
                        }

                        $dataExisted->save();
                    }
                } else {

                    $data = StudentInformation::query()->create([
                        'student_id' => $studentId,
                        'full_name' => $fullName,
                        'exam_school_year_id' => $examSchoolYearId,
                        'birth_date' => self::handleEmptyCell($birthDate),
                        'room' => self::handleEmptyCell($room),
                        'location' => self::handleEmptyCell($location),
                        'math' => self::handleEmptyCell($math),
                        'english' => self::handleEmptyCell($english),
                        'literature' => self::handleEmptyCell($literature),
                        'link_exam' => self::handleEmptyCell($linkExam),
                        'contact' => self::handleEmptyCell($contact),
                        'external_id' => self::handleEmptyCell($externalId),
                        'contact2' => self::handleEmptyCell($contact2),
                        'time' => self::handleEmptyCell($time),
                        'subject' => self::handleEmptyCell($subject)
                    ]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if (!empty($message)) {
            $message['year'] = $year;
            $message['exam'] = $exam;
        }
        return response()->json([
            'status' => 'success',
            'data' => $message
        ], 200);

        // try {


        // } catch (\Exception $e) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $e->getMessage()
        //     ], 500);
        // }
    }

    public function updateStudentInformation($request): JsonResponse
    {
        $id = $request->input('id');

        $studentInfo = StudentInformation::query()->where('id', $id)->first();
        if (!isset($studentInfo)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Info not found'
            ], 500);
        }
        $fullName = $request->input('full_name') ?? null;
        $room = $request->input('room') ?? null;
        $birthDate = $request->input('birth_date') ? Carbon::parse($request->input('birth_date'))->toDateTimeString() : null;
        $linkExam = $request->input('link_exam') ?? null;
        $math = $request->input('math') ?? null;
        $english = $request->input('english') ?? null;
        $literature = $request->input('literature') ?? null;
        $studentId = $request->input('student_id', null);
        $contact = $request->input('contact', null);
        $externalId = $request->input('external_id', null);
        $location = $request->input('location', null);
        $time = $request->input('time', null);
        $contact2 = $request->input('contact2', null);
        $subject = $request->input('subject', null);
        if (isset($contact) && strval($contact)[0] == '0') {
            $contact = substr(strval($contact), 1);
        }

        if (isset($contact2) && strval($contact2)[0] == '0') {
            $contact2 = substr(strval($contact2), 1);
        }
        $year = $request->input('year', null);
        $exam = $request->input('exam', null);
        $examSchoolYearId = self::mapExactSchoolYearAndExamType($year, $exam);
        try {
            if (Carbon::parse($birthDate) < Carbon::parse('1970-12-30 00:00:00')) {
                $birthDate = null;
            }

            if (isset($fullName)) {
                $studentInfo->full_name = $fullName;
            }

            if (isset($room)) {
                $studentInfo->room = $room;
            }

            if (isset($birthDate)) {
                $studentInfo->birth_date = $birthDate;
            }

            if (isset($linkExam)) {
                $studentInfo->link_exam = $linkExam;
            }

            if (isset($math)) {
                $studentInfo->math = $math;
            }

            if (isset($english)) {
                $studentInfo->english = $english;
            }

            if (isset($literature)) {
                $studentInfo->literature = $literature;
            }

            if (isset($studentId)) {
                $studentInfo->student_id = strval($studentId);
            }

            if (isset($examSchoolYearId)) {
                $studentInfo->exam_school_year_id = $examSchoolYearId;
            }

            if (isset($contact)) {
                $studentInfo->contact = $contact;
            }

            if (isset($externalId)) {
                $studentInfo->external_id = $externalId;
            }

            if (isset($location)) {
                $studentInfo->location = $location;
            }

            if (isset($time)) {
                $studentInfo->time = $time;
            }

            if (isset($contact2)) {
                $studentInfo->contact2 = $contact2;
            }

            if (isset($subject)) {
                $studentInfo->subject = $subject;
            }

            $studentInfo->save();
            // $data = StudentInformation::query()
            //     ->where('id', $id)
            //     ->update([
            //         'full_name' => $fullName,
            //         'exam_school_year_id' => $examSchoolYearId,
            //         'room' => $room,
            //         'location' => $location,
            //         'math' => $math,
            //         'english' => $english,
            //         'literature' => $literature
            //     ]);

            return response()->json([
                'status' => 'success',
                'data' => 'update success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteStudentInformation($request): JsonResponse
    {
        $id = $request->input('id');
        try {
            $data = StudentInformation::query()
                ->where('id', $id)
                ->delete();

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

    public static function mapSchoolYearAndExamType($schoolYear = null, $examType = null): array
    {
        if (!isset($schoolYear) && !isset($examType)) {
            return [];
        }

        $response =  ExaminationSchoolYear::query()
            ->select('id')
            ->when(isset($schoolYear) && $schoolYear != 'all', function ($q) use ($schoolYear) {
                $year = SchoolYear::query()->select('id')->where('year', $schoolYear)->first();
                if (isset($year)) {
                    $q->where('school_year_id', $year->id);
                }
            })
            ->when(isset($examType) && $examType != 'all', function ($q) use ($examType) {
                $q->where('examination_id', $examType);
            })
            ->get()
            ->pluck('id')
            ->toArray();

        return $response;
    }

    public static function mapExactSchoolYearAndExamType($schoolYear = null, $examType = null)
    {
        if (!isset($schoolYear) && !isset($examType)) {
            return null;
        }

        $year = SchoolYear::query()->select('id')->where('year', $schoolYear)->first();
        if (!isset($year)) {
            return null;
        }

        $response =  ExaminationSchoolYear::query()
            ->select('id')
            ->where('school_year_id', $year->id)
            ->where('examination_id', $examType)
            ->first();


        return $response ? $response->id : null;
    }

    public static function getExamsYearsFilterInfo()
    {
        $now = Carbon::now();
        $yearCondition = [
            $now->copy()->subYears(5)->year,
            $now->copy()->addYears(5)->year,
        ];
        $schoolYears = SchoolYear::query()
            ->select([
                'id',
                'year'
            ])
            ->whereBetween('year', $yearCondition)
            ->get();

        $examTypes = Examination::query()
            ->select([
                'id',
                'exam'
            ])
            ->get();

        return [
            'years' => $schoolYears,
            'exams' => $examTypes
        ];
    }

    public function bulkUpdateStudentInformation($request)
    {
        $message = [];
        $infos = $request->input('info');

        foreach ($infos as $info) {
            try {
                if (!isset($info['id'])) {
                    continue;
                }
                $id = $info['id'];
                $room = isset($info['room']) ? $info['room'] : null;
                $location = isset($info['location']) ? $info['location'] : null;
                $math = isset($info['math_score']) ? $info['math_score'] :  null;
                $english = isset($info['english_score']) ? $info['english_score'] : null;
                $literature = isset($info['literature_score']) ? $info['literature_score'] : null;
                $birthDate = isset($info['birth_date']) ? Carbon::parse($info['birth_date'])->toDateTimeString() : null;
                $linkExam = isset($info['link_exam']) ? $info['link_exam'] : null;
                $contact = isset($info['contact']) ? strval($info['contact']) : null;
                $externalId = isset($info['external_id']) ? $info['external_id'] : null;
                $time = isset($info['time']) ? $info['time'] : null;
                $contact2 = isset($info['contact2']) ? $info['contact2'] : null;
                $fullName = isset($info['full_name']) ? $info['full_name'] : null;
                $subject = isset($info['subject']) ? $info['subject'] : null;

                if (isset($contact) && ($contact)[0] == '0') {
                    $contact = substr($contact, 1);
                }
                if (isset($contact2) && ($contact2)[0] == '0') {
                    $contact2 = substr($contact2, 1);
                }
                if (Carbon::parse($birthDate) < Carbon::parse('1970-12-30 00:00:00')) {
                    $birthDate = null;
                }

                $dataExisted = StudentInformation::query()
                    ->where('id', $id)
                    ->first();

                if (isset($fullName)) {
                    $dataExisted->full_name = $fullName;
                }

                if (isset($room)) {
                    $dataExisted->room = $room;
                }

                if (isset($birthDate)) {
                    $dataExisted->birth_date = $birthDate;
                }

                if (isset($linkExam)) {
                    $dataExisted->link_exam = $linkExam;
                }

                if (isset($math)) {
                    $dataExisted->math = $math;
                }

                if (isset($english)) {
                    $dataExisted->english = $english;
                }

                if (isset($literature)) {
                    $dataExisted->literature = $literature;
                }

                if (isset($contact)) {
                    $dataExisted->contact = $contact;
                }

                if (isset($externalId)) {
                    $dataExisted->external_id = $externalId;
                }

                if (isset($location)) {
                    $dataExisted->location = $location;
                }

                if (isset($time)) {
                    $dataExisted->time = $time;
                }

                if (isset($contact2)) {
                    $dataExisted->contact2 = $contact2;
                }

                if (isset($subject)) {
                    $dataExisted->subject = $subject;
                }

                $dataExisted->save();
            } catch (\Exception $e) {
                $message[] = $e;
                continue;
            }
        }
        return $message;
    }

    public static function handleEmptyCell($cell)
    {
        if ($cell == 'x' || $cell == 'X') {
            $cell = null;
        }
        return $cell;
    }

    public static function checkEmptyCell($cell)
    {
        return $cell == 'x' || $cell == 'X';
    }

    public static function getMonthQuarter()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(2);
        $endDate = $now->copy()->addWeek(2);
        $startMonth = $startDate->month;
        $startYear = $startDate->year;
        $endMonth = $endDate->month;
        $endYear = $endDate->year;

        $query = NotifyMonthQuarter::query();
        if ($startYear !== $endYear) {
            $q1 = $query->clone()->where('year', $startYear)->where('month', '>=', $startMonth);
            $q2 = $query->clone()->where('year', $endYear)->where('month', '<=', $endMonth);
            $queryQuarter = $q1->unionAll($q2);
        } else {
            $queryQuarter = $query->where(function ($q) use ($startYear, $startMonth, $endMonth) {
                $q->where('year', $startYear)->whereBetween('month', [$startMonth, $endMonth]);
            });
        }

        $quarters = $queryQuarter->orderByDesc('id')->get();

        return [
            'quarters' => $quarters,
        ];
    }
}
