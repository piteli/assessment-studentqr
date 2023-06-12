<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use App\Models\Students;
use Illuminate\Support\Facades\Log;

class StudentsController extends Controller
{
    public function fileUpload(Request $request) {

        if ($request->hasFile('file')) {
            $validatedData = $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048', // Example file validation rules
            ]);
            
            $file = $request->file('file');
            $filePath = $file->getRealPath();

            try {
                $openedFile = fopen($filePath, 'r');
                fgetcsv($openedFile);
                $students = new Students();
                $countRunning = 0;
                $countSuccessStore = 0;

                while (($data = fgetcsv($openedFile, 0, ',')) !== false) {

                    $dataName = isset($data[0]) ? $data[0] : '';
                    $dataClass = isset($data[1]) ? $data[1] : '';
                    $dataLevel = isset($data[2]) ? $data[2] : 0;
                    $dataParentContact = isset($data[3]) ? $data[3] : '';
                    
                    $data = $students->retrieveExistingRecord(false, $dataName, $dataClass, $dataLevel, $dataParentContact);
                    Log::info($data);
                    Log::info($dataName);

                    if(count($data) === 0) {
                        $students->create([
                            'name' => $dataName,
                            'class' => $dataClass,
                            'level' => $dataLevel,
                            'parent_contact' => $dataParentContact,
                        ]);
                        $countSuccessStore++;
                    }
                    $countRunning++;
                }

                if($countRunning === $countSuccessStore) {
                    return response()->json(['success' => true, 'message' => 'Successfuly uploaded file!']);
                } else if ($countSuccessStore > 0) {
                    return response()->json(['success' => true, 'message' => "Successfuly uploaded file! There's duplicated record found hence it will skip"]);
                } else {
                    return response()->json(['success' => true, 'message' => "No new record found"]);
                }

                

            } catch (QueryException $exception) {
                Log::error($exception->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);

            } catch(Exception $exception) {
                Log::error($exception->getMessage());
                return response()->json(['success' => false, 'message' => 'An error occurred'], 500);
            }
        } else {
            abort(400, ['message' => 'File not found!']);
        }
    }

    public function templateDownload() {
        $file = 'app/public/template.csv'; 
        return response()->download(storage_path($file));
    }

    public function fetchStudentsRecords(Request $request) {
        if($request->ajax()) {
            $page = $request->query('page');
            $students = new Students();
            $data = $students->retrieveExistingRecord($page);
            return response()->json($data);
        }
    }
}
