<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Send message to user
     *
     * @param Request $request
     */
    public function sendMessage(Request $request){
        $request->validate([
            'message'=>'required',
            'to'=>'required'
        ]);
        $message= new Message();

        $message->message= $request->message;
        $message->from= auth()->user()->id;
        $message->to= $request->to;
        $user= auth()->user();

        if($message->save()){
            $unread_message= Message::where('from',auth()->user()->id)->where('to',$request->to)->where('is_read',0)->count();

            broadcast(new MessageSent($user, $message,$unread_message))->toOthers();
            return response()->json(['status'=>true,'message'=>"Message sent successfully.",'data'=>$message]);
        }
        else{
            return response()->json(['status'=>false, 'error'=>'something went wrong']);
        }
    }

    /**
     * Get all Messages of user
     *
     * @param Request $request
     */
    public function getMessages(Request $request){
        $request->validate([
            'user'=>'required'
        ]);
        $user= $request->user;

        $messages= Message::where(function($q1 ) use($user){
            $q1->where('from',$user)->where('to',auth()->user()->id);
        })->orWhere(function($q2)use($user){
            $q2->where('from',auth()->user()->id)->where('to',$user);
        })->get();
        Message::where('from',$user)->where('to',auth()->user()->id)->update(['is_read'=>1]);
        $user=User::find($request->user);
        return response()->json(['messages'=>$messages,'user'=>$user]);
    }

    /**
     * Update message status
     *
     * @param Request $request
     */
    public function updateMessagesStatus(Request $request){
        $request->validate([
            'message_id'=>'required'
        ]);
        $messages= Message::where('id',$request->message_id)->update(['is_read'=>1]);
        return response()->json(['status'=>true,'message'=>'Message status updated successfully.']);
    }
}
