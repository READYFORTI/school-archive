<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $user = Auth::user();

        if($user->role->role_name == 'Internal Lead Auditor') {
            $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
            $audit_plans = AuditPlan::latest()->get();
        }else{
            $auditors = [];
            $audit_plans = AuditPlan::whereHas('users', function($q) { $q->where('user_id', Auth::user()->id); })->latest()->get();
        }
        
        return view('audits.index', compact('audit_plans', 'auditors'));
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
        return view('audits.create', compact('tree_areas', 'auditors','main'));
    }

    public function getProcess() {
        $areas = Area::query()->with('parent')->get();
        $main = $areas->whereNull('type')->toArray();
        $process = $areas->where('type','process')->groupBy('area_name')->toArray();
        $data = [
            
        ];
        foreach ($main as $key => $value) {
            $value['selectable'] = false;
            $value['text'] = $value['area_name'];
            $data[] = $value;
        }
        foreach ($process as $key => $value) {
            foreach ($value as $k => $v) {
                $root = $this->getRootOfProcess($v['parent']);
                foreach ($data as $key2 => $value2) {
                    if (!isset($data[$key2]['nodes'])) {
                        $data[$key2]['nodes'] = []; 
                    }
                    if ($root == $value2['id']) {
                        $v['nodes'] = $this->getDirectoryTree($v);
                        $v['parent']['text'] = $v['parent']['area_name'];
                        if (!in_array($v['area_name'],array_column($data[$key2]['nodes'],'area_name'))) {
                            $data[$key2]['nodes'][] = [
                                'area_name'=>$v['area_name'],
                                'text'=>$v['area_name'],
                                'selectable'=>false,
                                'nodes'=>[
                                    $v['parent'],
                                ],
                            ];
                        }
                        else{
                            $array_key = array_search($v['area_name'],$data[$key2]['nodes']);
                            $data[$key2]['nodes'][$array_key]['nodes'][] = $v['parent'];
                            $data[$key2]['nodes'][$array_key]['selectable'] = false;
                            $data[$key2]['nodes'][$array_key]['text'] = $data[$key2]['nodes'][$array_key]['area_name'];
                        }
                        break;
                    }
                }
            }
        }

        // foreach ($data as $key => $value) {
        //     $data[$key]['selectable'] = $value['type'] == 'office' || $value['type'] == 'program';
        //     $data[$key]['text'] = $value['area_name'];
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
        $auditors = User::whereHas('role', function($q) { $q->where('role_name', 'Internal Auditor'); })->get();
        $batches = AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->get();
        $tree_areas = $this->dr->getAreaFamilyTree(null, 'process');
        $selected_users = $audit_plan->users->pluck('user_id')->toArray();
        return view('audits.previous', compact('tree_areas', 'auditors', 'audit_plan', 'batches'));
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
                ->exists() ?? null;
            $batch->cars = Car::whereHas('audit_report', function($q) use($audit_plan, $batch) {
                    $q->where('audit_plan_id', $audit_plan->id)
                    ->where('audit_plan_batch_id', $batch->id);
                })->exists() ?? null;
        }
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
            AuditPlanArea::where('audit_plan_id', $audit_plan->id)->delete();
            AuditPlanAreaUser::where('audit_plan_id', $audit_plan->id)->delete();
            AuditPlanBatch::where('audit_plan_id', $audit_plan->id)->delete();

            foreach($request->area_names as $key => $area_name) {
                $batch = AuditPlanBatch::create([
                    'name' => $area_name,
                    'audit_plan_id' => $audit_plan->id,
                    'date_scheduled'=> $request->date_selected[$key],
                    'from_time'=> $request->from_time[$key],
                    'to_time'=> $request->to_time[$key],
                ]);

                $areas = explode(',', $request->process[$key]);
                
                foreach($areas as $process_area) {
                    $area = Area::findOrFail($process_area);
                    $audit_plan_area = AuditPlanArea::firstOrcreate([
                        'area_id' => $area->id,
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

        return redirect()->route('lead-auditor.audit.index')->withMessage('Audit plan saved successfully');
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
