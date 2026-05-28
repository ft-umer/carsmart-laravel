<?php

namespace App\Http\Controllers;

class QAController extends Controller
{
    public function index()
    {
        $items = [
            ['listing'=>'LST-1001','status'=>'Needs'],
            ['listing'=>'LST-1002','status'=>'Pass'],
        ];

        return view('qa.index', compact('items'));
    }
}