<?php

use App\Constants\ErrorCodes;
use App\Constants\ErrorMessages;
use App\Constants\ValidationConstants;
use App\Http\Middleware\HorizonAuth;
use Console\SupervisorCommand;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Horizon\Console;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        $middleware->alias([
            'horizon.auth' => HorizonAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $exception, Request $request) {
            // Log the original error message
            Log::info('NotFoundHttpException: ' . $exception->getMessage());

            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'code'    => ErrorCodes::INVALID_REQUEST,
                    'message' => ErrorMessages::INVALID_REQUEST . '[API]',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'code'    => ErrorCodes::INVALID_REQUEST,
                    'message' => ErrorMessages::INVALID_REQUEST . '[WEB]',
                ], 200);
            }
        });

        $exceptions->renderable(function (AuthenticationException $exception, Request $request) {
            // Log the original error message

            Log::info('AuthenticationException: ' . $exception->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'code' => ErrorCodes::INVALID_SESSION, 'message' => ErrorMessages::SESSION_EXPIRED], 200);
            } else {
                return response()->json(['success' => false, 'code' => ErrorCodes::REQUEST_DENIED, 'message' => ErrorMessages::REQUEST_DENIED], 200);
            }
        });

        $exceptions->renderable(function (ValidationException $exception, Request $request) {
            // // Log the original error messages
            // Log::channel('special')->info('ValidationException: ' . json_encode($exception->errors()));

            $errors            = $exception->errors();
            $firstErrorMessage = collect($errors)->flatten()->first(); // Get the first error message
            $errors            = collect($errors)->map(function ($error) {
                return $error[0]; // Return the first error message for each field
            })->toArray();

            $combinedErrorMessage = implode(' and ', $errors); // Combine messages

            return response()->json([
                'status'  => false,
                'code'    => ValidationConstants::ERROR_CODE,
                'message' => $firstErrorMessage,
                'errors'  => $errors,
            ], 200);
        });

    }

    )

    ->withCommands([
        Console\HorizonCommand::class,
        Console\StatusCommand::class,
        Console\PauseCommand::class,
        Console\ContinueCommand::class,
        SupervisorCommand::class,
        Console\WorkCommand::class,
        Console\PublishCommand::class,
        Console\PurgeCommand::class,
        Console\TerminateCommand::class,
    ])
    ->withSchedule(function (Schedule $schedule) {
        // Add Horizon schedule
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // You can add other scheduled tasks here
    })
    ->create();
