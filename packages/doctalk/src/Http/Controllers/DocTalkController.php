<?php

namespace Package\DocTalk\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Controller;

class DocTalkController extends Controller
{
    public function __construct()
    {
        if (config('doctalk.middleware')) {
            $this->middleware(config('doctalk.middleware'));
        }
    }

    public function index(): View|Application|Factory
    {
        return view('doctalk::index');
    }
}
