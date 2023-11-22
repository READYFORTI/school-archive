<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index(){
        $questionnaire = Questionnaire::query()
        ->get();
        return view("staff.questionnaire", ["questionnaire"=> $questionnaire]);
    }

    public function create(Request $request) {
        Questionnaire::query()
        ->insert([
            'question'=>$request->input('question')
        ]);

        return view("", ["message"=> "Question has been added!"]);
    }

    public function update(Request $request) {
        Questionnaire::query()
        ->where('questionnaire_id',$request->input('questionnaire_id'))
        ->update([
            'question'=>$request->input('question')
        ]);
    }

    public function destroy(Request $request) {
        Questionnaire::query()
        ->where('questionnaire_id',$request->input('questionnaire_id'))
        ->delete();
    }
}
