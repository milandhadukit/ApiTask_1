<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendResponse($message, $result)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $result, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }

    public function wrongPass($error, $result, $code = 422)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }

    public function noAvailable($error, $result, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }
}
