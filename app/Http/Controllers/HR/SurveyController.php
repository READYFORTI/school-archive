<?php

namespace App\Http\Controllers\HR;

use Carbon\Carbon;
use App\Models\Survey;
use App\Models\Facility;
use Phpml\Association\Apriori;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $date_from = $request->date_from ?? Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $date_to = $request->date_to ??  Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        $surveys = $this->getSurvey($keyword, $date_from, $date_to);
        
        return view('HR.surveys', compact('surveys', 'keyword', 'date_from', 'date_to'));
    }

    public function reports(Request $request)
    {
        $offices = Facility::get();
        $keyword = $request->keyword ?? '';
        $date_from = $request->date_from ?? Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        $date_to = $request->date_to ??  Carbon::now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
        $surveys = $this->getSurvey($keyword, $date_from, $date_to);
        $by_facilities = $surveys->groupBy('facility');
        $facilities = [];
        foreach($by_facilities as $key => $facility_surveys) {
            $facility = (object) [
                'name' => collect($facility_surveys)->first()->facility->name,
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'total' => count($facility_surveys),
            ];
            $facilities[] = $facility;
        }
        $facilities = collect($facilities);

        $by_type = $surveys->groupBy('type');
        $types = [];
        foreach($by_type as $key => $type_surveys) {
            $type = (object) [
                'name' => collect($type_surveys)->first()->type,
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                'total' => count($type_surveys),
            ];
            $types[] = $type;
        }
        $types = collect($types);
        
        return view('HR.report', compact('surveys', 'keyword', 'date_from', 'date_to', 'facilities', 'types', 'offices'));
    }

    private function getSurvey($keyword, $date_from, $date_to){
       return Survey::with(['facility'])
            ->whereHas('facility',function($q) use($keyword){
                $q->where('name', 'LIKE', "%$keyword%");
            })->where(function($q) use($date_from, $date_to){
                if(!empty($date_from)) {
                    $q->where('created_at', '>=', $date_from);
                }
                if(!empty($date_to)) {
                    $q->where('created_at', '<=', $date_to);
                }
        })->get();
    }

    public function getApriori(Request $request)
    {
        $facility = $request->facility;
        $surveys = Survey::select(['surveys.email',\DB::raw('GROUP_CONCAT(DISTINCT(f.name)) AS facilities')])
                    ->join('facilities as f', 'surveys.facility_id', 'f.id')
                    ->groupBy('surveys.email')
                    ->get();

        $data = [];
        foreach($surveys as $survey) {
            $facilities = explode(',', $survey->facilities);
            if(count($facilities) > 1) {
                $data[] = $facilities;
            }
        }
        // $data = [['alpha', 'beta', 'epsilon'], ['alpha', 'beta', 'theta'], ['alpha', 'beta', 'epsilon'], ['alpha', 'beta', 'theta']];

        $associator = new Apriori($support = 0.3, $confidence = 0.6);
        $associator->train($data, []);
        $associates = $associator->predict([$facility]);

        $results = ['total_survey' => Survey::count()];
        foreach($associates as $result) {
            $results['facilities'][] = $result; 
            $results['colors'][] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

            $instance = 0;
            foreach($data as $row) {
                if(in_array($result[0], $row)) {
                    $instance++;
                };
            }
            $results['total'][] = $instance;
        }
        return $results;
    }
}
