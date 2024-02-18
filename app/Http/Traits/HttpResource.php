<?php
namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HttpResource {

    public function success($message, $data = null){
        return response()->json([
            'status' => true,
            'message' => $message,
            'data'  =>  $data
        ], 200);
    }
    
    public function objectCreated($message, $data = null){
        return response()->json([
            'status' => true,
            'message' => $message,
            'data'  =>  $data
        ], 201);
    }

    public function noContent($message, $data = null){
        return response()->json([
            'status' => true,
            'message' => $message,
            'data'  =>  $data
        ], 204);
    }

    public function validation($message, $data = null){
        return response()->json([
            'status' => false, 
            'message' => $message, 
            'data'  =>  $data 
        ], 400);
    }
    
    public function unauthorized($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 401);
    }

    public function forbidden($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 403);
    }
    
    public function notFound($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 404);
    }
    
    public function invalidMethod($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 405);
    }
    
    public function internalServer($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 500);
    }
    
    public function serviceUnavailable($message, $data = null){
        return response()->json([
            'status' => false,
            'message' => $message,
            'data'  =>  $data
        ], 503);
    }

}