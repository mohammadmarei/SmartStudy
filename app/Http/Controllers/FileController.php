<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Storage;

class FileController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:pdf,pptx,doc,docx|max:20000',
        ]);

        $uploadedFile = $request->file('file');


        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();


        $filePath = $uploadedFile->storeAs('uploads', $fileName, 'public');


        $file = File::create([
            'subject_id' => $request->subject_id,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $filePath,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => $file
        ], 201);
    }


    public function destroy(File $file)
    {
        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully'
        ], 200);
    }
}
