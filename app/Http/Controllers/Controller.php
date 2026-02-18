<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function json(?string $message = null, $data = [], $status = 200, array $headers = [])
    {
        $content = [];
        if ($message) {
            $content['message'] = $message;
        }

        if (! empty($data)) {
            $content['data'] = $data;
        }

        return response()->json($content, $status, $headers, JSON_PRESERVE_ZERO_FRACTION);
    }
}