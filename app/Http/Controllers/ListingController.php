<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\News;
use App\Models\Video;
use App\Models\Grade;
use Exception;

class ListingController extends Controller
{
    use ApiResponse;
    public function getNewsList (Request $request) {
        $typeRequest = $request->query('type', null);
        $q = $request->query('q', null);

        if ($typeRequest == 'search_posts' && (!isset($q) || $q == '')) {
            return $this->successResponse([]);
        }
        $news = News::query()
        ->when($typeRequest == 'search_posts' && isset($q) && $q != '', function ($query) use ($q) {
            $query->where('listing_title', 'like', '%' . $q . '%');
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
        return $this->successResponse($news);
    }

    public function getPostPagination (Request $request, $postId) {
        $nextPost = News::query()
        ->where('id', '>', $postId)
        ->first();
        $previousPost = News::query()
        ->where('id', '<', $postId)
        ->first();

        $response = [
            'next' => $nextPost,
            'previous' => $previousPost
        ];
        return $this->successResponse($response);
    }

    public function getVideoList (Request $request) {
        $videos = Video::query()->orderBy('created_at', 'desc')->limit(15)        
        ->get();
        return $this->successResponse($videos);
    }

    public function getGradeDetail (Request $request, $gradeCode) {
        $grade = Grade::query()->where('class_code', $gradeCode)->first();
        return $this->successResponse($grade);
    }

    public function getGradesListRecommend (Request $request, $gradeCode) {
        $grades = Grade::query()->where('class_code', '!=',$gradeCode)->limit(5)->orderBy('class_code', 'asc')->get();
        foreach($grades as $grade) {
            switch ($grade->group_grade) {
                case
                ('tieu_hoc'):
                    $grade->listing_title = 'Khối tiểu học - ' . $grade->class_name;
                    $grade->url = '/schedule/tieu_hoc?class=' . $grade->class_code;
                    break;
                case ('thcs'):
                    $grade->listing_title = 'Khối trung học cơ sở - ' . $grade->class_name;
                    $grade->url = '/schedule/thcs?class=' . $grade->class_code;
                    break;

                case ('thpt'):
                    $grade->listing_title = 'Khối trung học phổ thông - ' . $grade->class_name;
                    $grade->url = '/schedule/thpt?class=' . $grade->class_code;
                    break;
            }
        }
        return $this->successResponse($grades);
    }

    public function getGrades (Request $request,) {
        $grades = Grade::query()->get();
        return $this->successResponse($grades);
    }

}
