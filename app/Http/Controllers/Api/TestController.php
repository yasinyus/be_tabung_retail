<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Test endpoint working',
            'timestamp' => now()
        ]);
    }

    public function testAuth(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Authenticated endpoint working',
            'user' => $request->user(),
            'timestamp' => now()
        ]);
    }
}
