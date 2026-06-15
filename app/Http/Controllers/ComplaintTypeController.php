<?php

namespace App\Http\Controllers;

use App\Models\ComplaintType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintTypeController extends Controller
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
        $complaintTypes = ComplaintType::all();

        return view('complaint_type.index', compact('complaintTypes'));
    }

    /**
     * Create form for a complaint
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $users = User::doesnthave('codes')->get();

        return view('complaint_type.create', compact('users'));
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
            'name' => 'required|max:255|unique:complaint_types',
            'user_id' => 'required'
        ]);

        $complaint = new ComplaintType();
        $complaint->name = $request['name'];
        $complaint->save();

        foreach ($request->get('user_id') as $id) {
            DB::insert('insert into complaint_type_user (complaint_type_id, user_id) values (?, ?)', [$complaint->id, $id]);
        }

        return redirect(route('complaintType.list'))->with('success', trans('general.pages.complaint_type.success'));
    }

    public function edit(ComplaintType $type)
    {
        $users = User::doesnthave('codes')->get();

        $complaintUsers = $type->users->keyBy('user_id')->toArray();

        return view('complaint_type.edit', compact('type', 'users', 'complaintUsers'));
    }

    public function update(ComplaintType $type, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:complaint_types,name,' . $type->id,
            'user_id' => 'required'
        ]);

        $type->name = $request['name'];
        $type->update();

        foreach ($request->get('user_id') as $id) {
            DB::insert('insert into complaint_type_user (complaint_type_id, user_id) values (?, ?)', [$type->id, $id]);
        }

        return redirect(route('complaintType.list'))->with('success', trans('general.pages.complaint_type.success'));
    }
}
