<?php

namespace App\Http\Controllers;

use App\Models\NotificationType;
use Illuminate\Http\Request;
use App\Models\Notification;


class NotificationTypeController extends Controller
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

        $notificationTypes = NotificationType::all();

        return view('notification_type.index', compact('notificationTypes'));
    }

    /**
     * Create form for a notification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('notification_type.create');
    }

    /**
     * Save a notification
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:notification_types',
        ]);

        $notification = new NotificationType();
        $notification->name = $request['name'];
        $notification->save();

        return redirect(route('notificationType.list'))->with('success', trans('general.pages.notification_type.success'));
    }

    public function edit(NotificationType $type)
    {
        return view('notification_type.edit', compact('type'));
    }

    public function update(NotificationType $type, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:notification_types,name,' . $type->id,
        ]);

        $type->name = $request['name'];
        $type->update();

        return redirect(route('notificationType.list'))->with('success', trans('general.pages.notification_type.success'));
    }
}
