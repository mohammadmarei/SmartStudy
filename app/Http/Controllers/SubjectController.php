<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use File;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    public function index()
    {
        $id = auth()->id();
        $data = Subject::where('user_id', $id)->get();
        return response()->json($data, 200);
    }


    public function store(StoreSubjectRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $res = Subject::create($data);
        return response()->json($res, 201);
    }


    public function show($id_subject)
    {
        $id = auth()->id();
        $sub = Subject::find($id_subject);
        if (!$sub) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }
        if ($sub->user_id != $id) {
            return response()->json([
                'message' => 'Forbidden: you are not allowed to access this subject'
            ], 403);
        }
        return response()->json($sub->files, 200);
    }

    
    public function update(UpdateSubjectRequest $request, $id)
    {
        $sub = Subject::find($id);

        if (!$sub) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        if ($sub->user_id != auth()->id()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'color' => 'nullable|string'
        ]);

        $sub->update($data);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $sub
        ], 200);
    }


    public function destroy($id)
    {
        $sub = Subject::find($id);

        if (!$sub) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        if ($sub->user_id != auth()->id()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $sub->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}
