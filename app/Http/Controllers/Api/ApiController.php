<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    public function response($data, $status, $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        return response()->json($data, $status, $headers, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Response success
     *
     * @param  array  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function responseSuccess($data = [], $message = '', $code = Response::HTTP_OK)
    {
        $res = [
            'status' => trans('messages.success'),
            'data' => $data,
            'message' => $message,
        ];

        return $this->response($res, $code);
    }

    /**
     * Response fail
     *
     * @param  array  $data
     * @param  string  $message
     * @return JsonResponse
     */
    public function responseFail($data = [], $message = '', $code = Response::HTTP_NOT_FOUND)
    {
        $res = [
            'status' => trans('messages.fail'),
            'data' => $data,
            'message' => $message,
        ];

        return $this->response($res, $code);
    }

    /**
     * Response error
     *
     * @param  string  $message
     * @return JsonResponse
     */
    public function responseError($message = '', $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $res = [
            'status' => trans('messages.error'),
            'message' => $message,
        ];

        return $this->response($res, $code);
    }
}
