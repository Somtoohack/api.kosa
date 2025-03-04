<?php
namespace App\Traits;

use App\Constants\ErrorMessages;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait HttpResponses
{

    // Success codes
    const SUCCESS_CODE = 2000;

    // Error codes
    const ERROR_CODE_BAD_REQUEST           = 4000;
    const ERROR_CODE_UNAUTHORIZED          = 4001;
    const ERROR_CODE_FORBIDDEN             = 4003;
    const ERROR_CODE_NOT_FOUND             = 4004;
    const ERROR_CODE_METHOD_NOT_ALLOWED    = 4005;
    const ERROR_CODE_INTERNAL_SERVER_ERROR = 5000;

    /**
     * Success response method.
     *
     * @param mixed $result
     * @param string $message
     * @return JsonResponse
     */
    public function sendResponse($result, $message = 'Successful'): JsonResponse
    {
        $response = [
            'state'   => true,
            'code'    => self::SUCCESS_CODE,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Error response method.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $errorCode
     * @param int $statusCode
     * @return JsonResponse
     */
    public function sendError(
        $error,
        $reason = [],
        $errorCode = self::ERROR_CODE_NOT_FOUND,
        $statusCode = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'state'   => false,
            'code'    => $errorCode,
            'message' => $error,
        ];

        if (! empty($reason)) {
            $response['reason'] = $reason;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Simple message response method.
     *
     * @param string $message
     * @param int $statusCode
     * @param int $code
     * @return JsonResponse
     */
    public function sendMessage($message, $code = self::SUCCESS_CODE, $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'state'   => true,
            'code'    => $code,
            'message' => $message,
        ];
        return response()->json($response, $statusCode);
    }
}
