<?php
// We'll use this PHP block to define our code examples
$installation_code = <<<'CODE'
composer create-project stride/framework my-new-project
CODE;

$routing_code = <<<'CODE'
<?php

use Framework\Http\RouteHandler as Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
CODE;

$controller_code = <<<'CODE'
<?php

namespace App\Controllers;

use Framework\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', ['users' => $users]);
    }
}
CODE;

$model_code = <<<'CODE'
<?php

namespace App\Models;

use Framework\Core\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];
}
CODE;

$view_standalone_code = <<<'CODE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Standalone Page</title>
</head>
<body>
    <h1>Welcome to my standalone page!</h1>
    <p>This page does not use a layout.</p>
</body>
</html>
CODE;

$view_layout_code = <<<'CODE'
@extends('layouts.app')

@section('title')
Users List
@endsection

@section('content')
    <h1>Users</h1>

    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>
@endsection
CODE;

$database_code = <<<'CODE'
<?php
// config/database.php
return [
    'host' => 'localhost',
    'dbname' => 'your_db_name',
    'user' => 'root',
    'password' => ''
];
CODE;

$request_code = <<<'CODE'
use Framework\Http\Request;

public function store(Request $request)
{
    // Get a specific parameter
    $name = $request->get('name');
    
    // Get all parameters
    $allData = $request->all();
    
    // Get specific input with default
    $email = $request->input('email', 'default@example.com');
    
    // Check if parameter exists
    if ($request->has('subscribe')) {
        // Handle subscription
    }
}
CODE;

$cli_code = <<<'CODE'
<?php

namespace App\Console\Commands;

use Framework\Console\Command;

class MyCommand extends Command
{
    protected $signature = 'my-command';

    protected $description = 'My custom command';

    public function handle()
    {
        $this->info('My command executed successfully!');
    }
}
CODE;

// Now we'll output the HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stride - Custom PHP Framework Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#1e293b',
                        code: '#1a202c'
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: #c7d2fe #f1f5f9;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: #c7d2fe;
            border-radius: 6px;
        }
        
        pre {
            border-radius: 0.5rem;
            padding: 1.25rem;
            overflow-x: auto;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .code-block {
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: rgba(30, 41, 59, 0.8);
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .code-block:hover .copy-btn {
            opacity: 1;
        }
        
        .method-badge {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        /* Syntax highlighting */
        .php-comment { color: #999999; }
        .php-keyword { color: #cc7832; font-weight: bold; }
        .php-string { color: #6a8759; }
        .php-function { color: #ffc66d; }
        .php-variable { color: #9876aa; }
        .php-class { color: #4eade5; }
        .php-html { color: #e0e0e0; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="bg-indigo-600 text-white font-bold text-xl px-3 py-2 rounded-lg">S</div>
                        <span class="ml-3 text-xl font-bold text-gray-900">Stride PHP Framework</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">GitHub</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Examples</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Community</a>
                </div>
                <div class="flex items-center">
                    <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">v1.0.0</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-gray-100 border-r border-gray-200 overflow-y-auto hidden md:block">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Documentation</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="#introduction" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-info-circle mr-2"></i>Introduction
                        </a>
                    </li>
                    <li>
                        <a href="#installation" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-download mr-2"></i>Installation
                        </a>
                    </li>
                    <li>
                        <a href="#directory-structure" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-folder mr-2"></i>Directory Structure
                        </a>
                    </li>
                    <li>
                        <a href="#routing" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-route mr-2"></i>Routing
                        </a>
                    </li>
                    <li>
                        <a href="#controllers" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-cogs mr-2"></i>Controllers
                        </a>
                    </li>
                    <li>
                        <a href="#models" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-database mr-2"></i>Models
                        </a>
                    </li>
                    <li>
                        <a href="#views" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-eye mr-2"></i>Views
                        </a>
                    </li>
                    <li>
                        <a href="#database" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-server mr-2"></i>Database Configuration
                        </a>
                    </li>
                    <li>
                        <a href="#request" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-exchange-alt mr-2"></i>Request Handling
                        </a>
                    </li>
                    <li>
                        <a href="#cli" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-terminal mr-2"></i>Console Commands
                        </a>
                    </li>
                    <li>
                        <a href="#authentication" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-lock mr-2"></i>Authentication
                        </a>
                    </li>
                    <li>
                        <a href="#further-reading" class="block py-2 px-4 rounded-md hover:bg-indigo-50 text-gray-700 hover:text-indigo-600 font-medium">
                            <i class="fas fa-book-open mr-2"></i>Further Reading
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-8">
            <div class="max-w-4xl mx-auto">
                <!-- Title -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-extrabold text-gray-900">Stride PHP Framework</h1>
                    <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">
                        A lightweight, custom-built PHP framework designed for rapid web application development.
                    </p>
                </div>

                <!-- Mobile TOC -->
                <div class="md:hidden mb-8">
                    <details class="bg-white rounded-lg shadow p-4">
                        <summary class="font-bold text-lg cursor-pointer">Table of Contents</summary>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#introduction" class="text-indigo-600 hover:text-indigo-800">1. Introduction</a></li>
                            <li><a href="#installation" class="text-indigo-600 hover:text-indigo-800">2. Installation</a></li>
                            <li><a href="#directory-structure" class="text-indigo-600 hover:text-indigo-800">3. Directory Structure</a></li>
                            <li><a href="#routing" class="text-indigo-600 hover:text-indigo-800">4. Routing</a></li>
                            <li><a href="#controllers" class="text-indigo-600 hover:text-indigo-800">5. Controllers</a></li>
                            <li><a href="#models" class="text-indigo-600 hover:text-indigo-800">6. Models</a></li>
                            <li><a href="#views" class="text-indigo-600 hover:text-indigo-800">7. Views</a></li>
                            <li><a href="#database" class="text-indigo-600 hover:text-indigo-800">8. Database Configuration</a></li>
                            <li><a href="#request" class="text-indigo-600 hover:text-indigo-800">9. Request Handling</a></li>
                            <li><a href="#cli" class="text-indigo-600 hover:text-indigo-800">10. Console Commands</a></li>
                            <li><a href="#authentication" class="text-indigo-600 hover:text-indigo-800">11. Authentication</a></li>
                            <li><a href="#further-reading" class="text-indigo-600 hover:text-indigo-800">12. Further Reading</a></li>
                        </ul>
                    </details>
                </div>

                <!-- Documentation Content -->
                <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                    <section id="introduction" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">1</span>
                            Introduction
                        </h2>
                        <div class="mt-4 prose max-w-none">
                            <p>
                                Stride is a lightweight, modern, and flexible framework designed for building web applications of all sizes. It follows the Model-View-Controller (MVC) architectural pattern and provides a robust set of tools for routing, database management, authentication, and more.
                            </p>
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Key Features</h3>
                            <ul class="list-disc pl-5 mt-2 space-y-2">
                                <li><strong>MVC Architecture</strong>: Organize your code into Models, Views, and Controllers for a clean and maintainable structure.</li>
                                <li><strong>Powerful Routing Engine</strong>: Define clean, readable routes with support for parameters and middleware.</li>
                                <li><strong>Eloquent-like ORM</strong>: Interact with your database using an intuitive and powerful object-relational mapper.</li>
                                <li><strong>Blade-like Templating Engine</strong>: Create beautiful and dynamic views with a simple and expressive syntax.</li>
                                <li><strong>Built-in Authentication</strong>: Secure your application with a complete authentication system.</li>
                                <li><strong>Command-Line Interface (CLI)</strong>: Automate common tasks with custom console commands.</li>
                                <li><strong>Dependency Injection</strong>: Manage your application's dependencies with a powerful container.</li>
                            </ul>
                        </div>
                    </section>

                    <section id="installation" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">2</span>
                            Installation
                        </h2>
                        <div class="mt-4 prose max-w-none">
                            <p>To get started with Stride, follow these simple steps:</p>
                            <ol class="list-decimal pl-5 mt-2 space-y-2">
                                <li><strong>Create a new Stride project</strong>:
                                    <div class="code-block mt-2">
                                        <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">composer create-project stride/framework my-new-project</pre>
                                        <button class="copy-btn">Copy</button>
                                    </div>
                                </li>
                                <li><strong>Configure your environment</strong>:
                                    <ul class="list-disc pl-5 mt-2">
                                        <li>Rename the <code class="bg-gray-100 px-1.5 py-0.5 rounded">.env.example</code> file to <code class="bg-gray-100 px-1.5 py-0.5 rounded">.env</code>.</li>
                                        <li>Update the <code class="bg-gray-100 px-1.5 py-0.5 rounded">.env</code> file with your database credentials and other environment-specific settings.</li>
                                    </ul>
                                </li>
                                <li><strong>Run the database migrations</strong>:
                                    <div class="code-block mt-2">
                                        <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">php stride migrate</pre>
                                        <button class="copy-btn">Copy</button>
                                    </div>
                                </li>
                                <li><strong>Start the development server</strong>:
                                    <div class="code-block mt-2">
                                        <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">php -S localhost:8000 -t public</pre>
                                        <button class="copy-btn">Copy</button>
                                    </div>
                                </li>
                            </ol>
                            <p class="mt-4">
                                Your Stride application is now running at <a href="http://localhost:8000" class="text-indigo-600 hover:underline">http://localhost:8000</a>.
                            </p>
                        </div>
                    </section>

                    <section id="directory-structure" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">3</span>
                            Directory Structure
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">The Stride framework follows a standard directory structure to keep your code organized and easy to navigate:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-bold text-gray-700 mb-2">Core Directories</h3>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">app/</span>
                                                <p class="text-sm text-gray-600">Application-specific code</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">config/</span>
                                                <p class="text-sm text-gray-600">Configuration files</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">database/</span>
                                                <p class="text-sm text-gray-600">Database migrations and seeders</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">framework/</span>
                                                <p class="text-sm text-gray-600">Core framework components</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">public/</span>
                                                <p class="text-sm text-gray-600">Web server document root</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">routes/</span>
                                                <p class="text-sm text-gray-600">Defines application routes</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">storage/</span>
                                                <p class="text-sm text-gray-600">Logs, cache, and generated files</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">tests/</span>
                                                <p class="text-sm text-gray-600">Application tests</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder text-indigo-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono font-bold">vendor/</span>
                                                <p class="text-sm text-gray-600">Composer dependencies</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-bold text-gray-700 mb-2">App Subdirectories</h3>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <i class="fas fa-folder-open text-purple-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono">Controllers/</span>
                                                <p class="text-sm text-gray-600">Application controllers</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder-open text-purple-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono">Models/</span>
                                                <p class="text-sm text-gray-600">Data models</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder-open text-purple-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono">Views/</span>
                                                <p class="text-sm text-gray-600">View templates</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder-open text-purple-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono">Middleware/</span>
                                                <p class="text-sm text-gray-600">Request middleware</p>
                                            </div>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-folder-open text-purple-500 mt-1 mr-2"></i>
                                            <div>
                                                <span class="font-mono">Jobs/</span>
                                                <p class="text-sm text-gray-600">Queueable jobs</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="routing" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">4</span>
                            Routing
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Routes in Stride are defined in the <code class="bg-gray-100 px-1.5 py-0.5 rounded">routes/web.php</code> file. The routing engine allows you to define clean, readable routes with support for parameters and middleware.</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Basic Routing</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($routing_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Route Parameters</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">Route::get('/users/{id}', [UserController::class, 'show']);</pre>
                                <button class="copy-btn">Copy</button>
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Route Middleware</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth']);</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-bold text-blue-800 flex items-center">
                                    <i class="fas fa-info-circle mr-2"></i>Routing Method
                                </h4>
                                <p class="mt-2"><code class="bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">Route::get(string $url, array $handler)</code></p>
                                <ul class="mt-2 space-y-1">
                                    <li><strong>$url</strong>: The URL pattern. Use <code class="bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">{id}</code> for parameters.</li>
                                    <li><strong>$handler</strong>: An array <code class="bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">[ControllerClass::class, 'methodName']</code>.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section id="controllers" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">5</span>
                            Controllers
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Controllers are responsible for handling incoming requests, processing data, and returning a response. Controllers are stored in the <code class="bg-gray-100 px-1.5 py-0.5 rounded">app/Controllers</code> directory.</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Creating a Controller</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($controller_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Dependency Injection</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('users.index', ['users' => $users]);
    }
}</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-green-800 flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>Key Methods
                                    </h4>
                                    <ul class="mt-2 space-y-2">
                                        <li><code class="bg-green-100 px-1.5 py-0.5 rounded text-green-800">view($template, $data)</code>: Render a view template</li>
                                        <li><code class="bg-green-100 px-1.5 py-0.5 rounded text-green-800">redirect($url)</code>: Redirect to another URL</li>
                                    </ul>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg">
                                    <h4 class="font-bold text-purple-800 flex items-center">
                                        <i class="fas fa-lightbulb mr-2"></i>Best Practices
                                    </h4>
                                    <ul class="mt-2 space-y-1 text-sm">
                                        <li>Keep controllers focused on HTTP logic</li>
                                        <li>Delegate business logic to models</li>
                                        <li>Use dependency injection for services</li>
                                        <li>Validate all incoming requests</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="models" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">6</span>
                            Models
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Models in Stride provide a powerful and intuitive way to interact with your database. Models are stored in the <code class="bg-gray-100 px-1.5 py-0.5 rounded">app/Models</code> directory.</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Creating a Model</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($model_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Querying the Database</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">// Get all users
$users = User::all();

// Get a user by ID
$user = User::find(1);

// Query the database
$users = User::where('status', 'active')->get();</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <div class="mt-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Available Model Methods</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Example</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">all()</code></td>
                                                <td class="px-4 py-3">Retrieve all records</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::all()</code></td>
                                            </tr>
                                            <tr class="bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">find($id)</code></td>
                                                <td class="px-4 py-3">Find record by primary key</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::find(42)</code></td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">findBy($column, $value)</code></td>
                                                <td class="px-4 py-3">Find by specific column</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::findBy('email', 'test@example.com')</code></td>
                                            </tr>
                                            <tr class="bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">create($data)</code></td>
                                                <td class="px-4 py-3">Insert new record</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::create(['name' => 'New User'])</code></td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">update($id, $data)</code></td>
                                                <td class="px-4 py-3">Update existing record</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::update(42, ['name' => 'Updated Name'])</code></td>
                                            </tr>
                                            <tr class="bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap"><code class="bg-gray-100 px-1.5 py-0.5 rounded">delete($id)</code></td>
                                                <td class="px-4 py-3">Delete record</td>
                                                <td class="px-4 py-3"><code class="text-xs">User::delete(42)</code></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="views" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">7</span>
                            Views
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Views in Stride are responsible for rendering your application's UI. Views are stored in the <code class="bg-gray-100 px-1.5 py-0.5 rounded">app/Views</code> directory and use a Blade-like templating engine.</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Creating a View</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 mb-2">Standalone View</h4>
                                    <div class="code-block">
                                        <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($view_standalone_code) ?></pre>
                                        <button class="copy-btn">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 mb-2">View with Layout</h4>
                                    <div class="code-block">
                                        <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($view_layout_code) ?></pre>
                                        <button class="copy-btn">Copy</button>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Passing Data to Views</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">return view('users.index', ['users' => $users]);</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                        </div>
                    </section>

                    <section id="database" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">8</span>
                            Database Configuration
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Database connection settings are defined in <code class="bg-gray-100 px-1.5 py-0.5 rounded">config/database.php</code>:</p>
                            
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($database_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                                <h4 class="font-bold text-yellow-800 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Important Security Note
                                </h4>
                                <p class="mt-2">Never commit your actual database credentials to version control. Use environment variables or a <code class="bg-yellow-100 px-1.5 py-0.5 rounded text-yellow-800">.env</code> file for production credentials.</p>
                            </div>
                        </div>
                    </section>

                    <section id="request" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">9</span>
                            Request Handling
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">The <code class="bg-gray-100 px-1.5 py-0.5 rounded">Request</code> class provides a convenient way to access HTTP request data (GET, POST, etc.).</p>
                            
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($request_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                        </div>
                    </section>

                    <section id="cli" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">10</span>
                            Console Commands (CLI)
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Stride's command-line interface (CLI) allows you to automate common tasks. Commands are created in <code class="bg-gray-100 px-1.5 py-0.5 rounded">app/Console/Commands/</code>:</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Creating a Command</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg"><?= htmlspecialchars($cli_code) ?></pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Running a Command</h3>
                            <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-bold text-blue-800 flex items-center">
                                    <i class="fas fa-terminal mr-2"></i>Running Commands
                                </h4>
                                <p class="mt-2">To run a command, use the <code class="bg-blue-100 px-1.5 py-0.5 rounded text-blue-800">stride</code> executable:</p>
                                <div class="mt-2 code-block">
                                    <pre class="bg-blue-100 text-blue-800 p-4 rounded">php stride my-command</pre>
                                    <button class="copy-btn">Copy</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="authentication" class="p-6 border-b">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">11</span>
                            Authentication
                        </h2>
                        <div class="mt-4">
                            <p class="mb-4">Stride provides a complete authentication system to secure your application.</p>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Protecting Routes</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth']);</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-800 mt-6 mb-3">Accessing the Authenticated User</h3>
                            <div class="code-block">
                                <pre class="bg-gray-800 text-gray-200 p-4 rounded-lg">$user = auth()->user();</pre>
                                <button class="copy-btn">Copy</button>
                            </div>
                        </div>
                    </section>

                    <section id="further-reading" class="p-6">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 w-8 h-8 rounded-full flex items-center justify-center mr-3">12</span>
                            Further Reading
                        </h2>
                        <div class="mt-4 prose max-w-none">
                            <p>
                                For more detailed information about the framework, please see the <a href="/documentation.md" class="text-indigo-600 hover:underline">full documentation</a>.
                            </p>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-indigo-600 text-white font-bold text-xl px-3 py-2 rounded-lg">S</div>
                    <span class="ml-3 text-xl font-bold">Stride PHP Framework</span>
                </div>
                <div class="mt-4 md:mt-0">
                    <p class="text-gray-400">
                        © 2025 Stride Framework. All rights reserved.
                    </p>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-sm text-gray-400">
                <p>This documentation provides a starting point. As your framework evolves, you'll want to expand on these sections and add more details specific to your application's needs.</p>
            </div>
        </div>
    </footer>

    <script>
        // Copy button functionality
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const codeBlock = this.previousElementSibling;
                const text = codeBlock.textContent;
                
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = this.textContent;
                    this.textContent = 'Copied!';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>