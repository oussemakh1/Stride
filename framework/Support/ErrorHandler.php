<?php

namespace Framework\Support;

class ErrorHandler
{
    protected $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        error_log("PHP Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}");

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
            http_response_code(500);
        }

        if ($this->debug) {
            echo $this->renderErrorPage(
                'PHP Error',
                self::getErrorType($errno),
                $errstr,
                $errfile,
                $errline,
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            );
        } else {
            echo $this->renderGenericError();
        }
        
        return true;
    }

    public function handleException(\Throwable $exception): void
    {
        error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n" . $exception->getTraceAsString());

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
            http_response_code(500);
        }

        if ($this->debug) {
            echo $this->renderErrorPage(
                'Uncaught Exception',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTrace()
            );
        } else {
            echo $this->renderGenericError();
        }
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private static function getErrorType(int $type): string
    {
        $types = [
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        ];

        return $types[$type] ?? 'Unknown error type';
    }

    private function renderErrorPage(string $title, string $type, string $message, string $file, int $line, array $trace): string
    {
        // Escape all output values
        $escTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $escType = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
        $escMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $escFile = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
        $escLine = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
        $escRequestMethod = htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? '', ENT_QUOTES, 'UTF-8');
        $escRequestUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES, 'UTF-8');
        $escPhpVersion = htmlspecialchars(phpversion(), ENT_QUOTES, 'UTF-8');
        
        $formattedTrace = $this->formatTrace($trace);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error: $escTitle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .trace-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .trace-item:hover {
            background-color: #fef2f2;
        }
        .trace-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .trace-expanded .trace-details {
            max-height: 500px;
        }
        pre {
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body class="h-full bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-5xl bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-danger-700 to-danger-800 p-6">
                <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    $escTitle
                </h1>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="bg-danger-50 border-l-4 border-danger-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            <svg class="h-5 w-5 text-danger-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-danger-800">$escType</h3>
                            <div class="mt-2 text-danger-700">
                                <p class="font-mono">$escMessage</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Location
                        </h3>
                        <p class="text-gray-600 font-mono truncate">$escFile</p>
                        <p class="text-gray-600 mt-1">Line: <span class="font-semibold">$escLine</span></p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Request
                        </h3>
                        <p class="text-gray-600">Method: <span class="font-semibold">$escRequestMethod</span></p>
                        <p class="text-gray-600 truncate">URI: <span class="font-semibold">$escRequestUri</span></p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        Stack Trace
                    </h3>
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        $formattedTrace
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-gray-500 text-sm">
                            Generated at: <span class="font-medium">@date('Y-m-d H:i:s')</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                            {$escPhpVersion}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                            Error Handler
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.trace-item').forEach(item => {
            item.addEventListener('click', () => {
                item.classList.toggle('trace-expanded');
            });
        });
    </script>
</body>
</html>
HTML;
    }

    private function formatTrace(array $trace): string
    {
        if (empty($trace)) {
            return '<div class="p-4 text-center text-gray-500">No stack trace available</div>';
        }

        $html = '';
        foreach ($trace as $i => $item) {
            $file = isset($item['file']) ? htmlspecialchars($item['file'], ENT_QUOTES, 'UTF-8') : '[internal]';
            $line = isset($item['line']) ? htmlspecialchars($item['line'], ENT_QUOTES, 'UTF-8') : '-';
            $class = isset($item['class']) ? htmlspecialchars($item['class'], ENT_QUOTES, 'UTF-8') : '';
            $type = isset($item['type']) ? htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') : '';
            $function = isset($item['function']) ? htmlspecialchars($item['function'], ENT_QUOTES, 'UTF-8') : '';
            
            // Format arguments safely
            $args = '';
            if (isset($item['args']) ){
                $argItems = [];
                foreach ($item['args'] as $arg) {
                    if (is_object($arg)) {
                        $argItems[] = '<span class="text-blue-600">object</span>(' . htmlspecialchars(get_class($arg), ENT_QUOTES, 'UTF-8') . ')';
                    } elseif (is_array($arg)) {
                        $argItems[] = '<span class="text-purple-600">array</span>(' . count($arg) . ')';
                    } elseif (is_string($arg)) {
                        $display = strlen($arg) > 50 ? substr($arg, 0, 50) . '...' : $arg;
                        $argItems[] = '<span class="text-green-600">"' . htmlspecialchars($display, ENT_QUOTES, 'UTF-8') . '"</span>';
                    } elseif (is_bool($arg)) {
                        $argItems[] = '<span class="text-yellow-600">' . ($arg ? 'true' : 'false') . '</span>';
                    } elseif (is_null($arg)) {
                        $argItems[] = '<span class="text-gray-500">null</span>';
                    } else {
                        $argItems[] = htmlspecialchars((string)$arg, ENT_QUOTES, 'UTF-8');
                    }
                }
                $args = implode(', ', $argItems);
            }
            
            $html .= <<<HTML
<div class="trace-item border-b border-gray-200 last:border-0">
    <div class="p-4 flex items-start">
        <span class="mr-3 inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-200 text-gray-700 font-mono text-xs">
            #$i
        </span>
        <div class="flex-1 min-w-0">
            <div class="font-medium text-gray-900 truncate">
                $class$type$function(<span class="text-gray-700">$args</span>)
            </div>
            <div class="text-sm text-gray-500 truncate mt-1">
                $file:$line
            </div>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </div>
</div>
HTML;
        }
        
        return $html;
    }

    private function renderGenericError(): string
    {
        $errorRef = 'ERR-' . substr(md5(uniqid()), 0, 8);
        $escErrorRef = htmlspecialchars($errorRef, ENT_QUOTES, 'UTF-8');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 text-center border border-gray-200">
            <div class="mx-auto bg-red-100 text-red-600 rounded-full p-4 w-16 h-16 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="mt-6 text-2xl font-bold text-gray-800">Something Went Wrong</h2>
            <p class="mt-3 text-gray-600">
                We're sorry, but an unexpected error occurred. Our team has been notified.
            </p>
            <div class="mt-8">
                <a href="/" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-md hover:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Return Home
                </a>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-200 text-sm text-gray-500">
                Error Reference: <span class="font-mono">$escErrorRef</span>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
}