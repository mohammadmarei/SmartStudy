<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    //
    public function indexBySubject(Request $request)
    {
        $q=Quiz::query();
        if($request->input('subject_id')!==null){
            $q->where('subject_id',$request->subject_id);
        }
        return response()->json($q->get());
    }
}
