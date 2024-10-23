<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return redirect()->back()->with('success', 'Notification supprimée');
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        return redirect()->back()->with('success', 'Toutes les notifications ont été supprimées');
    }
}
