<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\NotificationResource;
use App\Http\Requests\SendNotificationRequest;

class NotificationController extends Controller
{
    public function store(Request $request){
        // #68
    }

    /**
     * Returns a user's notifications. Marks all unread as read
     */
    public function show(){
        $notifications = Auth::user()->notifications;
        $response = new JsonResponse(['data' => NotificationResource::collection($notifications)], Response::HTTP_OK); 
            //Creates the response before marking as read, so the notifications sent are still marked as unread.
        $this->markAsRead($notifications);
        return $response;
    }

    public function destroy(Notification $notification){
        if($notification->user_id == Auth::user()->id){
            $notification->delete();
            return new JsonResponse(['message' => ['success' => ['Notification deleted.']], 'data' => NotificationResource::collection(Auth::user()->notifications)], Response::HTTP_OK); 
        } else {
            return new JsonResponse(['errors' => ['error' => ['You are not authorized to do this.']]], Response::HTTP_FORBIDDEN); 
        }
    }

    /**
     * Sets all notifications as read if any of them are unread
     */
    private function markAsRead($notificationArray){
        foreach($notificationArray as $notification){
            if(!$notification->read){
                $notification->read = true;
                $notification->update();
            }
        }
    }

    /**
     * Admin function
     * Sends a notification to all users
     */
    public function sendNotificationToAll(SendNotificationRequest $request){
        $validated = $request->validated();
        foreach(User::lazy() as $user){
            Notification::create(['user_id' => $user->id,
            'title' => $validated['title'],
            'text' => $validated['text']]);
        }
        return new JsonResponse(['message' => ['success' => ['Notification sent.']]]);
    }
}
