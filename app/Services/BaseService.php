<?php

namespace App\Services;

class BaseService
{
    public function validate($validator)
    {
        if ($validator->fails()) {
            return [false, $validator->errors()];
        }

        return [true, $validator];
    }

    public function responseSuccess($data = [])
    {
        $status = 'success';
        return compact('status', 'data');
    }

    public function responseFail($data = [])
    {
        $status = 'fail';
        return compact('status', 'data');
    }

    public function responseError($data = [])
    {
        $status = 'error';
        return compact('status', 'data');
    }
}
