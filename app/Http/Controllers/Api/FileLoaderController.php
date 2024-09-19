<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileLoaderController extends Controller
{
    public function resource(){
        $arrData = (object)[
            'title' => 'Загрузка файлов'
        ];

        return response()->json($arrData);
    }

    public function uploadFile(Request $request){
        $response = array('status' => false, 'message' => 'Не предвиденная остановка сервера', 'data' => array());
        try {
            $start = microtime(true);
            if (!$request->isMethod('POST') || !$request->expectsJson('data')){ throw new \Exception('Некорректное обращение к серверу'); }
            
            $token = $request->_token;
            $file = $request->file('file');
            if(!$file){ throw new \Exception('Не смогли получить файл'); }

            $filename = Str::slug($file->getClientOriginalName()) . '_s' . $file->getSize() . '_l' . date('YmdHis');
            $filename = $filename . '.' . $file->extension();

            $fileDirectory = config('app.directories.files');
            $fileDirectoryAbs = $_SERVER['DOCUMENT_ROOT'] . $fileDirectory;
            if(!file_exists($fileDirectoryAbs)) mkdir($fileDirectoryAbs, 0777, true);

            $fileAbs = $fileDirectoryAbs . $filename;
            
            move_uploaded_file( $file, $fileAbs );

            $time = round(microtime(true) - $start, 4);
            $response = array(
                'status' => true, 
                'message' => 'Файл загружен', 
                'data' => [
                    'file' => $filename,
                    'directory' => $fileDirectory,
                    'src' => $fileDirectory . $filename,
                    'time' => $time,
                ]
            );
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }   

}
