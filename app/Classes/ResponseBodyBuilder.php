<?php

namespace App\Classes;

use Mockery\Matcher\Any;

class ResponseBodyBuilder
{
    /**
     * Build a success response array.
     *
     * This static method constructs a success response array, which typically includes
     * a status of "success," an optional message, and an optional data payload.
     *
     * @param string|null $message Optional message to include in the response.
     * @param mixed $data Optional data to include in the response.
     *
     * @return array The success response array containing status, message, and data.
     */
    static public function buildSuccessResponse(string $message = null, $data = null): array
    {
        return [
            "status" => "success",
            "message" => $message,
            "data" => $data
        ];
    }

    /**
     * Build a failure response array.
     *
     * This static method constructs a failure response array, which typically includes
     * a status of "failure," an optional message, and an optional data payload.
     *
     * @param string|null $message Optional message to include in the response.
     * @param mixed $data Optional data to include in the response.
     *
     * @return array The failure response array containing status, message, and data.
     */
    static public function buildFailureResponse(string $message = null, $data = null): array
    {
        return [
            "status" => "failure",
            "message" => $message,
            "data" => $data
        ];
    }
}
