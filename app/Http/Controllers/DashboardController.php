<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as CURL;
use Illuminate\Http\Request;
use App\Models\User;


class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws GuzzleException
     */
    public function index()
    {
        if (auth()->user()->hasRole("cititor")) {
            return redirect("/cititor");
        }
        if (auth()->user()->hasRole("supervisor_citiri")) {
            return redirect("/cititor/supervisor");
        }
        try {
            $sold = ApiController::getSold() ?: 0;
        } catch (\Exception $e) {
            $sold = 0;
        }
        return view('dashboard.index', compact('sold'));
    }
}


