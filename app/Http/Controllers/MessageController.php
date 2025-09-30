<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Stevebauman\Purify\Facades\Purify;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     * (Auth user only)
     */
    public function index()
    {
        $messages = Message::all();

        if ($messages->isEmpty()) {
            return response()->json([
                'message' => 'No messages found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * (Public, no auth required)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        $portfolio = Portfolio::firstOrFail();
        $msg = new Message();
        $msg->name = empty($cleaned_name = Purify::clean($request->name))? $msg->name :$cleaned_name  ;
        $msg->email = $request->email;
        $msg->message = empty($cleaned_message = Purify::clean($request->message))? $msg->message :$cleaned_message;
        $msg->portfolio_id = $portfolio->id;
        $msg->save();

        return response()->json([
            'success' => true,
            'data' => $msg,
            'message' => 'Message sent successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     * (Auth user only)
     */
    public function show(string $message_id)
    {
        $msg = Message::where('id', $message_id)->first();

        if (!$msg) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        if (!$msg->read) {
            $msg->read = true;
            $msg->save();
        }

        return response()->json([
            'success' => true,
            'data' => $msg
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * (Auth user only)
     */
    public function destroy(string $message_id)
    {
        $msg = Message::where('id', $message_id)->first();

        if (!$msg) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        $msg->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ], 200);
    }
}
