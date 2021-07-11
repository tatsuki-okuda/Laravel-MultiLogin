<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompornentTestController extends Controller
{
    
    public function showComportnent1()
    {
        $message = 'メッセージ';
        return view('tests.compornent-test1', compact('message'));
    }
    public function showComportnent2()
    {
        return view('tests.compornent-test2');
    }
}
