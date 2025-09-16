<?php

namespace App\Services;

use App\Http\Requests\CreateExaminationRequest;
use App\Models\Examination;
use App\Models\Grade;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\NotifyMonthQuarter;
use Illuminate\Support\Arr;
class NotifyService
{
    use ApiResponse;
    protected const PER_PAGE = 5;

    public function __construct()
    {
        //
    }

    public function getNotify($request)
    {
        $search = $request->query('q');
        $year = $request->query('year');
        $month = $request->query('month');
        $week = $request->query('week');
        $quarterSlug = $request->query('quarter');

        $sort = $request->query('sort', 'updated_at');
        $direction = $request->query('direction', 'desc');
        $perPage = $request->query('per_page', self::PER_PAGE);
        $page = $request->query('page', 1);

        $query = Notification::query()->with(['monthQuarter', 'grade']);

        $query->when($year, function ($q) use ($year) {
            $q->whereHas('monthQuarter', function ($qHas) use ($year) {
                $qHas->where('year', $year);
            });
        })
        ->when(isset($search), function ($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')
            ->orWhere('contact', 'like', '%'.ltrim($search, '0').'%')
            ->orWhere('contact_2', 'like', '%'.ltrim($search, '0').'%');
        })
        ->when($month, function ($q) use ($month) {
            $q->whereHas('monthQuarter', function ($qHas) use ($month) {
                $qHas->where('month', $month);
            });
        })
        ->when($quarterSlug, function ($q) use ($quarterSlug) {
            $q->whereHas('monthQuarter', function ($qHas) use ($quarterSlug) {
                $qHas->where('slug', $quarterSlug);
            });
        })->when(!empty($sort), function ($q) use ($sort, $direction) {
            $q->orderBy($sort, $direction);
        });

        $data = $query->paginate($perPage);

        return $data;
    }


    public function storeNewNotify ($request) {
        $monthQuarter = $request->post('month_quarter');
        $monthQuarterInfo = NotifyMonthQuarter::query()->where('slug', $monthQuarter)->first();
        if (empty($monthQuarterInfo)) {
            throw new \Exception('Not found month quarter');
        }
        $notifications = $request->post('notifications');
        $dataInsert = [];
        $notificationGrades = array_column($notifications, 'grade');
        $grades = array_unique($notificationGrades);
        $gradesInfo = Grade::query()->whereIn('class_code', $grades)->get()->keyBy('class_code')->toArray();
        $errorMessages = [];
        foreach ($notifications as $index => $notify) {
            $gradeInfo = Arr::get($gradesInfo, $notify['grade']);
            $name = Arr::get($notify, 'full_name');
            $contact = trim(Arr::get($notify, 'contact'));
            $contact2 = trim(Arr::get($notify, 'contact2') ?? '');
            $content = Arr::get($notify, 'notification');

            if (empty($name)) {
                $rowNumber = $index + 1;
                $errorMessages [] = "Học sinh không có tên ở dòng $rowNumber";
                continue;
            }
            if (empty($gradeInfo)) {
                $errorMessages [] = "Học sinh {$name} không có thông tin khối";
                continue;
            }

            if (empty($gradeInfo)) {
                $errorMessages [] = "Học sinh {$name} không có thông tin khối";
                continue;
            }

            if (!empty($contact) && ($contact)[0] == '0'){
                $contact = substr($contact, 1);
            }
            if (!empty($contact2) && ($contact2)[0] == '0'){
                $contact2 = substr($contact2, 1);
            }

            $dataInsert [] = [
                'month_quarter_id' => $monthQuarterInfo->id,
                'grade_id' => $gradeInfo['id'],
                'name' => $name,
                'contact' => $contact,
                'contact_2' => $contact2,
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Notification::insert($dataInsert);

        return $errorMessages;
    }

    public function publicGetNotify ($request) {
        $quarterSlug = $request->query('quarter');
        $grade = $request->query('grade');
        $contact = trim($request->query('contact'));
        
        if (empty($quarterSlug)) {
            throw new \Exception('Chưa cung cấp tuần / tháng');
        }
        if (empty($grade)) {
            throw new \Exception('Chưa cung cấp khối học');
        }
        if (empty($contact)) {
            throw new \Exception('Chưa cung cấp số điện thoại');
        }
        if (!empty($contact) && ($contact)[0] == '0'){
            $contact = substr($contact, 1);
        }
        $monthQuarterInfo = NotifyMonthQuarter::query()->where('slug', $quarterSlug)->first();
        if (empty($monthQuarterInfo)) {
            throw new \Exception('Not found month quarter');
        }

        $gradeInfo = Grade::query()->where('id', $grade)->first();
        if (empty($gradeInfo)) {
            throw new \Exception('Not found grade info');
        }


        $notifications = Notification::query()
        ->where('month_quarter_id', $monthQuarterInfo->id)
        ->where('grade_id', $gradeInfo->id)
        ->where(function ($q) use ($contact) {
            $q->where('contact', $contact)->orWhere('contact_2', $contact);
        })
        ->orderByDesc('created_at')
        ->get();

        $response = [];
        $name = [];
        foreach ($notifications as $notify) {
            if (!in_array($notify->name, $name)) {
                $response [] =  $notify;
                $name [] = $notify->name;
            }
        }

        return $response;
    }

}
