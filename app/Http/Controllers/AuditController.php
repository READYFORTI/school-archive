<?php

namespace App\Http\Controllers;

use App\Models\AuditPlanFile;
use Illuminate\Http\Request;

use Ramsey\Uuid\Uuid;
use Storage;
use Carbon\Carbon;
use App\Models\Car;
use App\Models\User;
use App\Models\File;
use App\Models\Area;
use App\Models\Office;
use App\Models\AreaUser;
use App\Models\Evidence;
use App\Models\Directory;
use App\Models\AuditPlan;
use App\Models\AuditReport;
use App\Models\AuditPlanArea;
use App\Models\AuditPlanBatch;
use App\Models\AuditPlanAreaUser;
use App\Models\ConsolidatedAuditReport;

use Illuminate\Support\Facades\Auth;
use App\Repositories\DirectoryRepository;

class AuditController extends Controller
{
    private $parent = 'Evidences';
    private $dr;

    public function __construct() 
    {
        $this->dr = new DirectoryRepository;
    }

    public function auditEvaluation() {
        return view('PO.audit.evaluate');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if($user->role->role_name == 'Internal Lead Auditor') {
            $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
            $audit_plans = AuditPlan::query()
            ->with('audit_plan_file')
            ->latest()->get();
        }
        else if($user->role->role_name == 'Process Owner'){
            if (!$request->has('directory')) {
                $area = AreaUser::query()
                ->where('user_id',Auth::user()->id)
                ->get();
                $assigned = $area->pluck('area_id')->toArray();
                $audit_plans = AuditPlan::query()
                ->join('audit_plan_areas','audit_plan_areas.audit_plan_id','audit_plans.id')
                ->whereIn('audit_plan_areas.area_id',$assigned)
                ->groupBy('audit_plans.name')
                ->get();
            }
            else{
                $report_list = AuditReport::query()
                ->where('audit_plan_id',$request->directory)
                ->get();
                // dd($report_list->toArray());
                // dd($reports->toArray());
                return view('PO.audit.list',compact('report_list'));
            }
            // dd($audit_plans->toArray());
            return view('PO.audit.index',compact('audit_plans'));
        }
        else{
            $auditors = [];
            $audit_plans = AuditPlan::whereHas('users', function($q) { $q->where('user_id', Auth::user()->id); })->latest()->get();
        }
        return view('audits.index', compact('audit_plans', 'auditors'));
    }

    public function getListofProcess(Request $request) {
        if ($request->has('area')) {
            $area = Area::query()->get();
            $data = $area->where('area_name',$request->area);
            $list = [];
            foreach ($data as $key => $value) {
                if (empty($value['parent']['parent']['parent'])) {
                    $temp = $value['parent'];
                    if (!isset($list[$temp['area_name']])) {
                        $temp['process_id'] = $value['id'];
                        $list[$temp['area_name']] = $temp;
                    }
                    else{
                        $list[$temp['area_name']]['process_id'] = $list[$temp['area_name']]['process_id'].','.$value['id'];
                    }
                }
                else{
                    $temp = $value['parent']['parent'];
                    if (!isset($list[$temp['area_name']])) {
                        $temp['process_id'] = $value['id'];
                        $list[$temp['area_name']] = $temp;
                    }
                    else{
                        $list[$temp['area_name']]['process_id'] = $list[$temp['area_name']]['process_id'].','.$value['id'];
                    }
                }
            }
            return response()->json([
                'data'=>$list
            ]);
        }
        return response()->json([
            'message'=>'Failed to get data!'
        ],500);
    }
    public function addAuditPlanFile(Request $request) {
        if ($request->hasFile('audit_plan_file')) {
            $uuid = Uuid::uuid4()->toString().'.'.$request->file('audit_plan_file')->getClientOriginalExtension();
            $link = $request->file('audit_plan_file')->storeAs('public/audit_plan',$uuid);
            $audit = new AuditPlanFile;
            $audit->audit_plan_id = $request->audit_plan_id;
            $audit->file_name = $request->filename . '.' . $request->file('audit_plan_file')->getClientOriginalExtension();
            $audit->link = $link;
            $audit->save();
            return back()->withMessage('
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    File uploaded successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            ');
        };
        return back()->withMessage('
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                Failed to upload file!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        ');
    }

    public function downloadPlan($id) {
        if ($id) {
            $file = AuditPlanFile::findOrFail($id);
            return Storage::download($file->link,$file->file_name);
        }
    }
    public function areas(Request $request, $id)
    {
        $user = Auth::user();
        $audit_plan = AuditPlan::whereHas('plan_users', function($q) {
                            $q->where('user_id', Auth::user()->id); 
                        })->where('id', $id)
                        ->firstOrFail();
        
        $areas = Area::whereHas('audit_plan_area', function($q) use($audit_plan){
            $q->where('audit_plan_id', $audit_plan->id)
                ->whereHas('area_users', function($q2) {
                    $q2->where('user_id', Auth::user()->id); 
                }); 
        })->get();

        foreach($areas as $area) {
            $area->directory = $this->dr->getDirectoryByAreaAndGrandParent($area->id, 'Evidences');
        }
        return view('audits.auditor-areas', compact('audit_plan', 'areas'));
    }

    public function createAuditPlan()
    {
        $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
        $tree_areas = $this->dr->getAreaFamilyTree(null, 'process');
        $main = $this->getProcess();
        $list = $this->getAdminProcess();
        return view('audits.create', compact('tree_areas', 'auditors','main','list'));
    }

    public function getAdminProcess() {
        $area = Area::query()->get();
        $process = $area->where('type','process');
        $admin = $area->whereNull('parent_area')->where('area_name','Administration');
        $process_list = [
            [
                'text'=>'Administration',
                'selectable'=>false,
                'nodes'=>[]
            ],
            [
                'text'=>'Academics',
                'selectable'=>false,
                'nodes'=>[]
            ]
        ];
        $list_of_acads = [];
        // dd($process->toArray());
        foreach ($process as $key => $value) {
            $res = $this->getRootOfProcessName($value);
            $data = $value->toArray();
            $data['text'] = $data['area_name'];
            if ($res == 'Administration') {
                $data['selectable'] = true;
                $process_list[0]['nodes'][] = $data;
            } else {
                $data['selectable'] = false;
                if (count($process_list[1]['nodes']) == 0) {
                    $process_list[1]['nodes'][] = $data;
                }
                else{
                    $check = false;
                    foreach ($process_list[1]['nodes'] as $key2 => $value2) {
                        if ($value2['area_name'] == $data['area_name']) {
                            $check = true;
                        }
                    }
                    if (!$check) {
                        $process_list[1]['nodes'][] = $data;
                    }
                }
                $list_of_acads[] = $data;
            }
        }
        // dd($list_of_acads);
        foreach ($process_list[1]['nodes'] as $key => $value) {
            foreach ($list_of_acads as $key2 => $value2) {
                if ($value['area_name'] == $value2['area_name']) {
                    if (!isset($process_list[1]['nodes'][$key]['nodes'])) {
                        $temp = $value2['parent']['parent'];
                        $temp['text'] = $temp['area_name'];
                        $temp['selectable'] = true;
                        $process_list[1]['nodes'][$key]['nodes'][] = $temp;
                    }
                    else{
                        $check2 = false;
                        foreach ($process_list[1]['nodes'][$key]['nodes'] as $key3 => $value3) {
                            if ($value3['area_name'] == $value2['parent']['parent']['area_name']) {
                                $check2 = true;
                            }
                        }
                        if (!$check2) {
                            $temp = $value2['parent']['parent'];
                            $temp['text'] = $temp['area_name'];
                            $temp['selectable'] = true;
                            $process_list[1]['nodes'][$key]['nodes'][] = $temp;
                        }
                    }
                }
            }
        }
        return $process_list;
    }

    public function getRootOfProcessName($area) {
        if (is_null($area['parent'])) {
            return $area['area_name'];
        }
        return $this->getRootOfProcessName($area['parent']);
    }

    public function getProcess() {
        $areas = Area::query()->with('parent')->get();
        $main = $areas->whereNull('type')->toArray();
        $process = $areas->where('type','process')->groupBy('area_name')->toArray();
        $data = [
            
        ];
        // foreach ($main as $key => $value) {
        //     array_push($data,array(
        //         'id'=>$value['id'],
        //         'text'=>$value['area_name'],
        //         'selectable'=>false,
        //         'nodes'=>array()
        //     ));
        // }
        
        foreach ($process as $key => $value) {
            array_push($data,array(
                'text'=>$key,
                'selectable'=>true,
            ));
        }
                // dd($data);

        // dd($data);

        
        // foreach ($process as $key => $value) {
        //     foreach ($value as $k => $v) {
        //         $root = $this->getRootOfProcess($v['parent']);
        //         foreach ($data as $key2 => $value2) {
        //             if (!isset($data[$key2]['nodes'])) {
        //                 $data[$key2]['nodes'] = []; 
        //             }
        //             if ($root == $value2['id']) {
        //                 $v['nodes'] = $this->getDirectoryTree($v);
        //                 $v['parent']['text'] = $v['parent']['area_name'];
        //                 if (!in_array($v['area_name'],array_column($data[$key2]['nodes'],'area_name'))) {
        //                     $data[$key2]['nodes'][] = [
        //                         'area_name'=>$v['area_name'],
        //                         'text'=>$v['area_name'],
        //                         'selectable'=>false,
        //                         'nodes'=>[
        //                             $v['parent'],
        //                         ],
        //                     ];
        //                 }
        //                 else{
        //                     $array_key = array_search($v['area_name'],$data[$key2]['nodes']);
        //                     $data[$key2]['nodes'][$array_key]['nodes'][] = $v['parent'];
        //                     $data[$key2]['nodes'][$array_key]['selectable'] = false;
        //                     $data[$key2]['nodes'][$array_key]['text'] = $data[$key2]['nodes'][$array_key]['area_name'];
        //                 }
        //                 break;
        //             }
        //         }
        //     }
        // }
        return $data;
    }

    public function getRootOfProcess($area) {
        if (is_null($area['parent'])) {
            return $area['id'];
        }
        return $this->getRootOfProcess($area['parent']);
    }

    public function getDirectoryTree($area, $list = []) {
        if (is_null($area['parent'])) {
            array_pop($list);
            return array_reverse($list);
        }
        $area['parent']['text'] = $area['parent']['area_name'];
        $list[] = $area['parent'];
        return $this->getDirectoryTree($area['parent'],$list);
    }

    public function getPrevious()
    {
        $audit_plan = AuditPlan::latest()->firstOrFail();
        // $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
        // $tree_areas = $this->dr->getAreaFamilyTree(null, 'process');
        $selected_users = $audit_plan->users->pluck('user_id')->toArray();
        // return view('audits.previous', compact('tree_areas', 'auditors', 'audit_plan', 'batches'));

        $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
        $batches = AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->with(['areaLead'=>function($query){
            $query->select('audit_plan_areas.*','users.firstname','users.surname');
            $query->join('users','users.id','audit_plan_areas.lead_user_id');
        }])
        ->get();
        $tree_areas = $this->dr->getAreaFamilyTree(null, 'process');
        $main = $this->getProcess();
        $list = $this->getAdminProcess();
        return view('audits.previous', compact('audit_plan','tree_areas', 'auditors','main','list','batches'));
    }

    public function editAuditPlan($id)
    {
        $audit_plan = AuditPlan::findOrFail($id);
        $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })
                        ->whereHas('audit_plan_area_user', function($q) use($audit_plan){
                            $q->where('audit_plan_id', $audit_plan->id);
                        })->with('audit_plan_area_user')->get();
        
        $batches = AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->get();

        foreach($batches as $batch) {
            $batch->audit_report = AuditReport::where('audit_plan_id', $audit_plan->id)
                ->where('audit_plan_batch_id', $batch->id)
                ->value('date') ?? null;
            $batch->cars = Car::whereHas('audit_report', function($q) use($audit_plan, $batch) {
                    $q->where('audit_plan_id', $audit_plan->id)
                    ->where('audit_plan_batch_id', $batch->id);
                })->exists() ?? null;
            $batch->lead = User::query()
            ->select('users.firstname','users.surname')
            ->join('audit_plan_areas','audit_plan_areas.lead_user_id','users.id')
            ->join('audit_plan_batches','audit_plan_batches.audit_plan_id','audit_plan_areas.audit_plan_id')
            ->where('audit_plan_batches.id',$batch->id)
            ->where('audit_plan_areas.audit_plan_id',$batch->audit_plan_id)
            ->get();
        }
        // dd($batch->toArray());
        return view('audits.edit', compact('auditors', 'audit_plan', 'batches'));
    }

    public function saveAuditPlan(Request $request, $id = null)
    {
        $request = (object) $request->all();
        \DB::transaction(function () use (
            $id,
            $request
        ) {
            $audit_plan = AuditPlan::find($id);
           
            if(empty($audit_plan)) {
                $audit_plan = AuditPlan::create(['name' => $request->name]);
            }

            if(empty($audit_plan->directory_id)) {
                $parent_directory = $this->dr->getDirectory('Audit Reports');
                $dir = $this->dr->getDirectory($request->name, $parent_directory->id);
                $audit_plan->directory_id = $dir->id;
            }
            
            $audit_plan->name = $request->name . ' ' .now()->year;
            $audit_plan->description = $request->description;
            $audit_plan->date = now();
            $audit_plan->save();
            
            Directory::where('id', $audit_plan->directory_id)->update(['name' => $audit_plan->name]);
            AuditPlanAreaUser::where('audit_plan_id', $audit_plan->id)->delete();
            AuditPlanArea::where('audit_plan_id', $audit_plan->id)->delete();
            AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->delete();

            foreach($request->area_names as $key => $area_name) {
                $batch = AuditPlanBatch::create([
                    'name' => $area_name,
                    'audit_plan_id' => $audit_plan->id,
                    'date_scheduled'=> $request->date_selected[$key],
                    'from_time'=> $request->from_time[$key],
                    'to_time'=> $request->to_time[$key],
                ]);

                // $areas = Area::query()
                // ->where('area_name',$request->area_names[$key])
                // ->where('type','process')
                // ->pluck('id')
                // ->toArray();

                $areas = explode(',',$request->area_names[$key]);
                
                // dd($areas);
                
                foreach($areas as $process_area) {
                    $area = Area::findOrFail($process_area);
                    $audit_plan_area = AuditPlanArea::firstOrcreate([
                        'area_id' => $area->id,
                        'lead_user_id'=>$request->lead[$key],
                        'audit_plan_batch_id' => $batch->id,
                        'audit_plan_id' => $audit_plan->id,
                    ]);

                    $auditors = explode(',',$request->auditors[$key]);
                    foreach($auditors as $auditor) {
                        AuditPlanAreaUser::firstOrcreate([
                            'user_id' => $auditor,
                            'audit_plan_id' => $audit_plan->id,
                            'audit_plan_batch_id' => $batch->id,
                            'audit_plan_area_id' => $audit_plan_area->id
                        ]);

                        AreaUser::firstOrcreate([
                            'area_id' => $area->id,
                            'user_id' => $auditor,
                        ]);

                        $user = User::find($auditor);
                        \Notification::notify($user, 'Assigned you to audit plan '.$request->name, route('user.dashboard'));
                    }
                }
            }
            
            
        });

        return redirect()->route('lead-auditor.audit.index')->withMessage('
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                Audit plan saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        ');
    }

    public function deleteAuditPlan($id)
    {
        $audit_plan = AuditPlan::findOrFail($id);

        $audit_area_users = AuditPlanAreaUser::where('audit_plan_id', $id)->get();
        foreach($audit_area_users as $audit_area_user) {
            $area_user = AreaUser::where('area_id', $audit_area_user->audit_plan_area->area_id)
                ->where('user_id', $audit_area_user->user_id)->first();
            if(!empty($area_user)) {
                $area_user->delete();
            }
            $audit_area_user->delete();
        }
        Car::whereHas('audit_report', function($q) use($id) {
            $q->where('audit_plan_id', $id);
        })->delete();
        AuditReport::where('audit_plan_id', $id)->delete();
        AuditPlanArea::where('audit_plan_id', $id)->delete();
        AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->delete();


        
        // Delete Folder and Files
        $directory = Directory::where('id', $audit_plan->directory_id)->first();
        $child_directories = $this->dr->getChildDirectories($directory);
        $directories = array_merge([$directory], $child_directories);

        foreach($directories as $directory) {
            File::where('directory_id', $directory->id)->delete();
            $directory->delete();
        }

        $audit_plan->delete();
        
        return redirect()->route('lead-auditor.audit.index')->withMessage('Audit plan deleted successfully');
    }

    public function auditReports(Request $request, $directory_name = '')
    {
        $parent = 'Audit Reports';
        $data = $this->dr->getDirectoriesAndFiles($parent, $request->directory ?? null);
        $data['route'] = \Str::slug($parent);
        $data['page_title'] = $parent;

        return view('archives.index', $data);
    }

    public function checklist() {
        return view('audit-reports.checklist');
    }

    public function createAuditReport()
    {
        $audit_plans = AuditPlan::whereHas('users', function($q) {
                    $q->where('user_id', Auth::user()->id); 
                })->with('batches', function($q) {
                    $q->whereHas('batch_users', function($q2) {
                        $q2->where('user_id', Auth::user()->id); 
                    });
                })->get();
                        
        return view('audit-reports.create', compact('audit_plans'));
    }

    public function storeAuditReport(Request $request)
    {
        $user = Auth::user();
        
        $audit_plan = AuditPlan::findOrFail($request->audit_plan);
        $dir = Directory::findOrFail($audit_plan->directory_id);
        $process = AuditPlanBatch::findOrFail($request->process);

        $directory = $this->dr->getDirectory($process->area_names(), $dir->id);
        $year = Carbon::parse($request->date)->format('Y');
        $directory = $this->dr->getDirectory($year, $directory->id);
        $exists = AuditReport::query()
        ->where('audit_plan_id',$audit_plan->id)
        ->where('audit_plan_batch_id',$request->process)
        ->where('directory_id',$directory->id)
        ->count();
        if ($exists) {
            return back()->with('error','Audit report has already been submitted!');
        }

        $file_id = null;
        if ($request->hasFile('file_attachments')) {
            $file = $this->dr->storeFile(
                        $request->name, 
                        $request->description, 
                        $request->file('file_attachments'), 
                        $directory->id, 
                        'audit_reports'
            );
            $file_id = $file->id;
        }

        $audit_report = AuditReport::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
            'audit_plan_id' => $audit_plan->id,
            'audit_plan_batch_id' => $request->process,
            'directory_id' => $directory->id,
            'date' => $request->date,
            'file_id' => $file_id
        ]);

        if($request->has('with_cars')) {
            $file_id = null;
            if ($request->hasFile('cars_file_attachments')) {
                $file = $this->dr->storeFile(
                            $request->cars_name, 
                            $request->cars_description, 
                            $request->file('cars_file_attachments'), 
                            null, // No directory for CARS
                            'cars'
                );
                $file_id = $file->id;
            }
    
            Car::create([
                'name' => $request->cars_name,
                'audit_report_id' => $audit_report->id,
                'description' => $request->cars_description,
                'user_id' => $user->id,
                'date' => $request->cars_date,
                'file_id' => $file_id
            ]);
        }
        

        $users = User::whereHas('role', function($q){ $q->whereIn('role_name', \FileRoles::AUDIT_REPORTS); })->get();
        \Notification::notify($users, 'Submitted Audit Report', route('archives-show-file', $file_id));

        
        return back()->withMessage('Audit report created successfully');
    }

    public function storeCars(Request $request)
    {
        $user = Auth::user();
        $file_id = null;
        if ($request->hasFile('file_attachments')) {
            $file = $this->dr->storeFile(
                        $request->name, 
                        $request->description, 
                        $request->file('file_attachments'), 
                        null, // No directory for CARS
                        'cars'
            );
            $file_id = $file->id;
        }

        Car::create([
            'name' => $request->name,
            'audit_report_id' => $request->audit_report_id,
            'description' => $request->description,
            'user_id' => $user->id,
            'date' => $request->date,
            'file_id' => $file_id
        ]);

        $users = User::whereHas('role', function($q){ $q->where('role_name', 'Internal Lead Auditor'); })->get();
        \Notification::notify($users, 'Submitted CARS', route('archives-show-file', $file_id));
        
        return back()->withMessage('CARS created successfully');
    }


    public function consolidatedAuditReports(Request $request, $directory_name = '')
    {
        $parent = 'Consolidated Audit Reports';
        $data = $this->dr->getDirectoriesAndFiles($parent, $request->directory ?? null);
        
        $data['route'] = 'lead-auditor.consolidated-audit-reports.index';
        $data['page_title'] = $parent;

        return view('archives.index', $data);
    }

    public function createConsolidatedAuditReport()
    {
        $audit_plans = $audit_plans = AuditPlan::get();
        return view('consolidated-audit-reports.create', compact('audit_plans'));
    }

    public function storeConsolidatedAuditReport(Request $request)
    {
        $user = Auth::user();

        $files = $request->file('file_attachments');
        $audit_plan = AuditPlan::findOrFail($request->audit_plan);
        $parent_directory = Directory::where('name', 'Consolidated Audit Reports')->whereNull('parent_id')->firstOrFail();
        $directory = $this->dr->getDirectory($audit_plan->name, $parent_directory->id);

        $year = Carbon::parse($request->date)->format('Y');
        $directory = $this->dr->getDirectory($year, $directory->id);
        
        $file_id = null;
        if ($request->hasFile('file_attachments')) {
            $file = $this->dr->storeFile(
                        $request->name, 
                        $request->description, 
                        $request->file('file_attachments'), 
                        $directory->id, 
                        'consolidated_audit_reports'
            );
            $file_id = $file->id;
        }

        ConsolidatedAuditReport::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $user->id,
            'audit_plan_id' => $audit_plan->id,
            'directory_id' => $directory->id,
            'date' => $request->date,
            'file_id' => $file_id
        ]);

        $users = User::whereHas('role', function($q){ $q->where('role_name', \Roles::QUALITY_ASSURANCE_DIRECTOR); })->get();
        \Notification::notify($users, 'Submitted Consolidated Audit Report', route('admin-consolidated-audit-reports'));

        
        return back()->withMessage('Consolidated audit report created successfully');
    }
}
