<?php

namespace App\Http\Controllers;

use App\Models\ComplaintStatus;
use Illuminate\Http\Request;

class ComplaintStatusController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        $complaintStatus = ComplaintStatus::all();

        return view('complaint_status.index', compact('complaintStatus'));
    }

    /**
     * Create form for a complaint
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('complaint_status.create');
    }

    /**
     * Save a complaint
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:complaint_statuses',
        ]);

        $complaint = new ComplaintStatus();
        $complaint->name = $request['name'];
        $complaint->save();

        return redirect(route('complaintStatus.list'))->with('success', trans('general.pages.complaint_status.success'));
    }

    public function edit(Complaintstatus $status)
    {
        return view('complaint_status.edit', compact('status'));
    }

    public function update(Complaintstatus $status, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:complaint_statuses,name,' . $status->id,
        ]);

        $status->name = $request['name'];
        $status->update();

        return redirect(route('complaintStatus.list'))->with('success', trans('general.pages.complaint_status.success'));
    }
}
