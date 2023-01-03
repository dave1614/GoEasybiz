<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

// use \Response;

class TestController extends Controller
{
    

    public function index(Request $request): JsonResponse{
       

        return response()->json(['success' => true,'message' => 'Request Successful'], ($request->headers->has('X-Header') && Cookie::has('color')) ? 200 : 500);

    }

    public function testLogin(Request $request): JsonResponse{
        $user = auth()->user();
        

        return response()->json(['success' => true,'details' => ['user' => $user]],201);

    }
}
