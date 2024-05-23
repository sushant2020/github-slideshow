<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */
    public function sendErrorResponse($result, $message, $code)
    {
        $response = [
            'success' => false,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**

     * Return error response.

     *

     * @return \Illuminate\Http\Response

     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    /**
     * Converts validation array errors to string
     *
     * @param array $errors The array of error messages
     *
     * @return string The comma separated string of error messages
     */
    public function errorString($errors)
    {
        $errorString = !empty($errors) && is_array($errors) ? implode(",", $errors) : "";
        return $errorString;
    }

}
