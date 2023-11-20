<?php

use App\Http\Controllers\Administrator\AnnouncementController;
use App\Http\Controllers\Administrator\AreaController;
use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\InstituteController;
use App\Http\Controllers\Administrator\OfficeController;
use App\Http\Controllers\Administrator\ProcessController;
use App\Http\Controllers\Administrator\ProgramController;
use App\Http\Controllers\Administrator\RoleController;
use App\Http\Controllers\Administrator\SurveyController as AdminSurveyController;
use App\Http\Controllers\Administrator\UserController as AdminUserController;
use App\Http\Controllers\Administrator\ReportsController as AdminReportsController;


use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\DCCController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\OfficeUserController;
use App\Http\Controllers\PO\PODashboardController;
use App\Http\Controllers\PO\POEvidenceController;
use App\Http\Controllers\ProcessUserController;
use App\Http\Controllers\ProgramUserController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\StaffTemplateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\MessageController;


use App\Http\Controllers\TemplateController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\CMTController;


use App\Http\Controllers\HR\HRDashboardController;
use App\Http\Controllers\HR\SurveyReportController;
use App\Http\Controllers\HR\SurveyController as HRSurveyController;
use App\Http\Controllers\HR\OfficeController as HROfficeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function(){
    Route::get('/',[AuthController::class,'loginPage'])->name('login-page');
    Route::post('login',[AuthController::class,'login'])->name('login');
    Route::prefix('surveys')->group(function(){
        Route::get('/',[SurveyController::class,'create'])->name('surveys.create');
        Route::post('/',[SurveyController::class,'store'])->name('surveys.store');
        Route::get('/success',[SurveyController::class,'success'])->name('surveys.success');
    });
});

Route::middleware(['auth'])->group(function(){
    Route::get('/unassigned',[AuthController::class,'unassigned'])->name('unassigned');

    

    
    Route::get('/dashboard',[UserController::class,'dashboard'])->name('user.dashboard');
    Route::get('/profile',[UserController::class, 'profile'])->name('user.profile');

    Route::get('/directories/{name}',[ArchiveController::class,'index'])->name('directories');
    Route::get('/{parent}/search',[ArchiveController::class, 'search'])->name('search');
    Route::prefix('archives')->middleware('area_assigned')->group(function() {
        Route::get('/',[ArchiveController::class,'index'])->name('archives-page');
        Route::post('/directory',[ArchiveController::class,'storeDirectory'])->name('archives-store-directory');
        Route::post('/directory/{id}/update',[ArchiveController::class,'updateDirectory'])->name('archives-update-directory');
        Route::delete('/directory/{id}/delete',[ArchiveController::class,'deleteDirectory'])->name('archives-delete-directory');
        
        
        Route::post('/file',[ArchiveController::class,'storeFile'])->name('archives-store-file');
        Route::get('/shared-with-me',[ArchiveController::class,'sharedWithMe'])->name('archives-shared');
        
        Route::get('/file/{id}',[ArchiveController::class,'showFile'])->name('archives-show-file');
        Route::get('/file/{id}/download',[ArchiveController::class,'downloadFile'])->name('archives-download-file');
        Route::post('/file/{id}',[ArchiveController::class,'updateFile'])->name('archives-update-file');
        Route::delete('/file/{id}',[ArchiveController::class,'deleteFile'])->name('archives-delete-file');

        Route::get('/file-history/{id}',[ArchiveController::class,'downloadFileHistory'])->name('archives-download-file-history');

        Route::post('/file/{id}/share',[ArchiveController::class,'shareFile'])->name('archives-share-file');
        Route::post('/file/{id}/unshare',[ArchiveController::class,'unshareFile'])->name('archives-unshare-file');
        
    });


    // Admin routes
    Route::prefix('administrator')->middleware('admin')->group(function(){
        Route::get('/',function(){
            return redirect()->route('admin-dashboard-page');
        });
        Route::get('/dashboard',[DashboardController::class,'adminDashboardPage'])->name('admin-dashboard-page');
       
        Route::prefix('area')->group(function(){
            Route::get('/',[AreaController::class, 'index'])->name('admin-area-page');
            Route::post('/',[AreaController::class, 'store'])->name('admin-area-store');
            Route::post('/edit',[AreaController::class, 'update'])->name('admin-area-update');
        });

        Route::get('/pending-users',[AdminUserController::class,'pending'])->name('admin-pending-users-page');
        Route::get('/rejected-users',[AdminUserController::class,'rejected'])->name('admin-rejected-users-page');
        
        Route::put('/approve-user',[AdminUserController::class,'approve'])->name('admin-approve-user');
        
        Route::get('/assign_users',[AdminUserController::class, 'assignUserList'])->name('admin-assign-users');
        Route::post('/assign_users',[AdminUserController::class, 'assignUser'])->name('admin-assign-user');
        Route::post('/assign_po_users',[AdminUserController::class, 'assignPOUser'])->name('admin-assign-po-user');

        Route::get('/users',[AdminUserController::class,'index'])->name('admin-user-list');
        Route::delete('/users/{id}',[AdminUserController::class,'destroy'])->name('admin-user-destroy');
        Route::post('/users/{id}/enable',[AdminUserController::class,'enable'])->name('admin-user-enable');

        // Route::prefix('roles')->group(function(){
        //     Route::get('/',[RoleController::class,'index'])->name('admin-role-page');
        //     Route::get('{id}',[RoleController::class,'show'])->name('admin-user-list');
        // });
        
        Route::get('/surveys',[AdminSurveyController::class,'index'])->name('admin-surveys-list');

        Route::prefix('announcements')->group(function(){
            Route::get('/',[AnnouncementController::class, 'index'])->name('admin-announcement-page');
            Route::get('/create',[AnnouncementController::class, 'create'])->name('admin-announcement-create');
            Route::post('/',[AnnouncementController::class, 'store'])->name('admin-announcement-store');
            Route::get('/{id}',[AnnouncementController::class, 'edit'])->name('admin-announcement-edit');
            Route::patch('/{id}',[AnnouncementController::class, 'update'])->name('admin-announcement-update');
            Route::delete('/{id}',[AnnouncementController::class, 'delete'])->name('admin-announcement-delete');
        });

        Route::prefix('survey-reports')->group(function(){
            Route::get('/', [AdminReportsController::class, 'surveyReports'])->name('admin-survey-reports');
            Route::get('/rejected', [AdminReportsController::class, 'rejectedSurveyReports'])->name('admin-survey-reports.rejected');
            Route::post('/{id}/approve', [AdminReportsController::class, 'approveSurveyReport'])->name('admin-survey-reports.approve');
            Route::post('/{id}/reject', [AdminReportsController::class, 'rejectSurveyReport'])->name('admin-survey-reports.reject');
        });

        Route::prefix('consolidated-audit-reports')->group(function(){    
            Route::get('/', [AdminReportsController::class, 'consolidatedAuditReports'])->name('admin-consolidated-audit-reports');
            Route::get('/rejected', [AdminReportsController::class, 'rejectedConsolidatedAuditReports'])->name('admin-consolidated-audit-reports.rejected');
            Route::post('/{id}/approve', [AdminReportsController::class, 'approveConsolidatedAuditReport'])->name('admin-consolidated-audit-reports.approve');
            Route::post('/{id}/reject', [AdminReportsController::class, 'rejectConsolidatedAuditReport'])->name('admin-consolidated-audit-reports.reject');
        });
    });

    Route::prefix('dcc')->middleware('dcc')->group(function(){
        Route::get('/',function(){
            return redirect()->route('dcc-dashboard-page');
        });
        Route::get('/dashboard',[DCCController::class,'dashboard'])->name('dcc-dashboard-page');
        
        Route::middleware('area_assigned')->name('dcc.')->group(function(){
            Route::middleware('area_assigned')->group(function(){
                Route::get('evidence', [DCCController::class, 'evidences'])->name('evidence.index');
                Route::get('manual', [DCCController::class, 'manuals'])->name('manual.index');
            });
        });
    });

    Route::prefix('po')->middleware('po')->group(function(){
        Route::get('/',function(){
            return redirect()->route('po-dashboard-page');
        });
        Route::get('/dashboard',[PODashboardController::class,'dashboard'])->name('po-dashboard-page');
        
        Route::middleware('area_assigned')->name('po.')->group(function(){
            Route::prefix('evidence')->group(function(){
                Route::get('/', [EvidenceController::class, 'index'])->name('evidence.index');
                Route::get('/create', [EvidenceController::class, 'create'])->name('evidence.create');
                Route::post('/', [EvidenceController::class, 'store'])->name('evidence.store');
            });

            Route::prefix('manual')->group(function(){
                Route::get('/', [ManualController::class, 'index'])->name('manual.index');
                Route::get('/create', [ManualController::class, 'create'])->name('manual.create');
                Route::post('/', [ManualController::class, 'store'])->name('manual.store');
            });

            Route::prefix('archives')->group(function(){
                Route::get('/', [ManualController::class, 'index'])->name('archives');
            });
        });
    });

    Route::prefix('hr')->middleware('hr')->group(function(){
        Route::get('/',function(){
            return redirect()->route('hr-dashboard-page');
        });
        Route::get('/dashboard',[HRDashboardController::class,'dashboard'])->name('hr-dashboard-page');
        Route::get('/survey',[HRSurveyController::class,'index'])->name('hr-survey-page');
        Route::get('/survey/report',[HRSurveyController::class,'reports'])->name('hr-survey-report');
        Route::get('/survey/apriori',[HRSurveyController::class,'getApriori'])->name('hr-survey-apriori');
        

        Route::prefix('survey_reports')->group(function(){
            Route::get('/', [SurveyReportController::class, 'index'])->name('hr.survey_report.index');
            Route::get('/create', [SurveyReportController::class, 'create'])->name('hr.survey_report.create');
            Route::post('/', [SurveyReportController::class, 'store'])->name('hr.survey_report.store');
        });

        Route::prefix('offices')->group(function(){
            Route::get('/',[HROfficeController::class,'index'])->name('hr-offices-page');
            Route::post('/',[HROfficeController::class,'store'])->name('hr-offices-create');
            Route::post('/{id}/update',[HROfficeController::class,'update'])->name('hr-offices-update');
            Route::delete('/{id}',[HROfficeController::class,'delete'])->name('hr-offices-delete');
        });
    });

    Route::middleware('staff')->prefix('staff')->group(function () {
        // Sidebar
        Route::get('/dashboard', [StaffDashboardController::class, 'dashboard'])->name('staff.dashboard');
        
        Route::prefix('templates')->group(function(){
            Route::get('/', [TemplateController::class, 'index'])->name('staff.template.index');
            Route::get('/create', [TemplateController::class, 'create'])->name('staff.template.create');
            Route::post('/', [TemplateController::class, 'store'])->name('staff.template.store');
        });
        
        Route::get('/manuals', [ManualController::class, 'index'])->name('staff.manual.index');
    });

    Route::middleware(['auditor', 'area_assigned'])->prefix('auditor')->name('auditor.')->group(function () {
        Route::get('/templates', [TemplateController::class, 'index'])->name('template.index');
        Route::get('/audit-evidence', [AuditController::class, 'index'])->name('audit.evidence.index');
        Route::get('/audit-evidence/{id}', [AuditController::class, 'areas'])->name('audit.evidence.show');
        
        Route::prefix('audit-reports')->name('audit-reports.')->group(function () {
            Route::get('/', [AuditController::class, 'auditReports'])->name('index');
            Route::get('/create', [AuditController::class, 'createAuditReport'])->name('create');
            Route::post('/', [AuditController::class, 'storeAuditReport'])->name('store');
        });
        Route::post('/cars', [AuditController::class, 'storeCars'])->name('cars.store');
    });

    Route::middleware('lead-auditor')->prefix('lead-auditor')->name('lead-auditor.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::prefix('templates')->group(function(){
            Route::get('/', [TemplateController::class, 'index'])->name('template.index');
            Route::get('/create', [TemplateController::class, 'create'])->name('template.create');
            Route::post('/', [TemplateController::class, 'store'])->name('template.store');
        });
        
        Route::prefix('audit-plan')->group(function () {
            Route::get('/', [AuditController::class, 'index'])->name('audit.index');
            Route::get('/create', [AuditController::class, 'createAuditPlan'])->name('audit.create');
            Route::get('/previous', [AuditController::class, 'getPrevious'])->name('audit.previous');
            Route::get('/{id}', [AuditController::class, 'editAuditPlan'])->name('audit.edit');
            Route::post('/', [AuditController::class, 'saveAuditPlan'])->name('audit.save');
            Route::post('/{id}/update', [AuditController::class, 'saveAuditPlan'])->name('audit.update');
            Route::delete('/{id}', [AuditController::class, 'deleteAuditPlan'])->name('audit.delete');
        });
        Route::get('/', [AuditController::class, 'auditReports'])->name('audit-reports.index');
        Route::prefix('consolidated-audit-reports')->name('consolidated-audit-reports.')->group(function () {
            Route::get('/', [AuditController::class, 'consolidatedAuditReports'])->name('index');
            Route::get('/create', [AuditController::class, 'createConsolidatedAuditReport'])->name('create');
            Route::post('/', [AuditController::class, 'storeConsolidatedAuditReport'])->name('store');
        });
    });

    Route::middleware('cmt')->prefix('cmt')->name('cmt.')->group(function () {
        Route::get('/survey-reports', [CMTController::class, 'surveyReports'])->name('survey-reports');
        Route::post('/survey-reports/{id}/approve', [CMTController::class, 'approveSurveyReport'])->name('survey-reports.approve');
        Route::post('/survey-reports/{id}/reject', [CMTController::class, 'rejectSurveyReport'])->name('survey-reports.reject');
        Route::get('/consolidated-audit-reports', [CMTController::class, 'consolidatedAuditReports'])->name('consolidated-audit-reports');
        Route::post('/consolidated-audit-reports/{id}/approve', [CMTController::class, 'approveConsolidatedAuditReport'])->name('consolidated-audit-reports.approve');
        Route::post('/consolidated-audit-reports/{id}/reject', [CMTController::class, 'rejectConsolidatedAuditReport'])->name('consolidated-audit-reports.reject');
    });
    
    Route::post('save-remarks/{file_id}',[UserController::class,'saveRemarks'])->name('save-remarks');
    Route::get('logout',[AuthController::class,'lg'])->name('logout');
    Route::get('download-evidence/{id}',[DownloadController::class,'evidenceDownload'])->name('download-evidence');
    

    Route::prefix('templates')->group(function(){
        Route::get('/', [TemplateController::class, 'index'])->name('templates');
    });

    Route::prefix('evidences')->middleware('directory:Evidences')->group(function(){
        Route::get('/', [EvidenceController::class, 'index'])->name('evidences');
    });

    Route::prefix('manuals')->middleware('directory:Manuals')->group(function(){
        Route::get('/', [ManualController::class, 'index'])->name('manuals');
    });

    Route::prefix('audit-reports')->middleware('directory:Audit Reports')->group(function(){
        Route::get('/', [AuditController::class, 'auditReports'])->name('audit-reports');
    });

    Route::prefix('survey_reports')->middleware('directory:Survey Reports')->group(function(){
        Route::get('/', [SurveyReportController::class, 'index'])->name('survey-reports');
    });

    Route::prefix('notifications')->group(function(){
        Route::get('/', [UserController::class, 'notifications'])->name('notifications');
    });

    Route::prefix('messages')->middleware('staff_qad')->group(function(){
        Route::get('/', [MessageController::class, 'index'])->name('messages');
        Route::post('/send', [MessageController::class, 'store'])->name('messages.send');
    });

    Route::prefix('process-manuals')->name('process-manuals.')->middleware('manual_list')->group(function(){
        Route::get('/', [ManualController::class, 'allManuals'])->name('all');
        Route::get('/pending', [ManualController::class, 'pendingManuals'])->name('pending');
        Route::get('/pending-updates', [ManualController::class, 'pendingUpdateManuals'])->name('pending-updates');

        Route::patch('/{id}', [ManualController::class, 'approveManuals'])->name('approve');
        Route::delete('/{id}', [ManualController::class, 'rejectManuals'])->name('reject');
    });
});

Route::resources([
    'users'=>UserController::class
    
]);
Route::put('/users/{id}', 'UserController@update')->name('users.update');
Route::get('/users/{id}', 'UserController@show')->name('users.phow');



    


