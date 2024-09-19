<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $arrData = (object)[
            'title' => 'Загрузка файлов'
        ];

        return view('main.views.index', compact('arrData'));
    }

    public function resource(){
        $arrData = (object)[
            'title' => 'Загрузка файлов'
        ];

        return view('main.views.index', compact('arrData'));
    }
}
