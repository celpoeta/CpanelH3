<?php

namespace App\Http\Controllers;

use App\Models\CommentsReply;
use Illuminate\Http\Request;

class CommentsReplyController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'reply' => 'required',
        ]);
        CommentsReply::create([
            'name' => $request->name,
            'reply' => $request->reply,
            'poll_id' => $request->poll_id,
            'comment_id' => $request->comment_id,
        ]);
        return redirect()->back();
    }
}
