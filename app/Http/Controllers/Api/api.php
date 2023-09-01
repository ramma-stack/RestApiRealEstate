<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use Illuminate\Http\Request;

class api extends Controller
{
    public function index()
    {
        return "Hello World!";
    }

    public function contact(Request $request)
    {
        $validator = validator($request->all(), [
            'full_name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'phone' => 'required|min:10|max:11',
            'message' => 'required|min:10|max:400'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 401);
        } else {

            $contact = Contacts::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ]);

            if (!$contact) {
                return response()->json(['errors' => 'Message sent faild!'], 401);
            } else {
                return response()->json(['success' => 'Message sent successfully'], 200);
            }
        }
    }
}
