# Stride PHP Framework Documentation

## 1. Introduction

Welcome to the Stride PHP Framework! Stride is a lightweight, modern, and flexible framework designed for building web applications of all sizes. It follows the Model-View-Controller (MVC) architectural pattern and provides a robust set of tools for routing, database management, authentication, and more.

### Key Features:

- **MVC Architecture**: Organize your code into Models, Views, and Controllers for a clean and maintainable structure.
- **Powerful Routing Engine**: Define clean, readable routes with support for parameters and middleware.
- **Eloquent-like ORM**: Interact with your database using an intuitive and powerful object-relational mapper.
- **Blade-like Templating Engine**: Create beautiful and dynamic views with a simple and expressive syntax.
- **Built-in Authentication**: Secure your application with a complete authentication system.
- **Command-Line Interface (CLI)**: Automate common tasks with custom console commands.
- **Dependency Injection**: Manage your application's dependencies with a powerful container.

## 2. Installation

To get started with Stride, follow these simple steps:

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/stride-framework.git
   ```

2. **Install Composer dependencies**:
   ```bash
   composer install
   ```

3. **Configure your environment**:
   - Rename the `.env.example` file to `.env`.
   - Update the `.env` file with your database credentials and other environment-specific settings.

4. **Run the database migrations**:
   ```bash
   php stride migrate
   ```

5. **Start the development server**:
   ```bash
   php -S localhost:8000 -t public
   ```

Your Stride application is now running at `http://localhost:8000`.

## 3. Directory Structure

The Stride framework follows a standard directory structure to keep your code organized and easy to navigate:

- `app/`: Contains your application's core code, including Models, Views, and Controllers.
- `config/`: Stores your application's configuration files.
- `database/`: Includes your database migrations and seeders.
- `public/`: The web server's document root, containing the `index.php` entry point and your assets (CSS, JS, images).
- `routes/`: Defines your application's routes.
- `storage/`: Stores your application's logs, cache, and other generated files.
- `tests/`: Contains your application's tests.
- `vendor/`: Manages your Composer dependencies.

## 4. Routing

Routes in Stride are defined in the `routes/web.php` file. The routing engine allows you to define clean, readable routes with support for parameters and middleware.

### Basic Routing

```php
use Framework\Http\RouteHandler as Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
```

### Route Parameters

```php
Route::get('/users/{id}', [UserController::class, 'show']);
```

### Route Middleware

```php
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth']);
```

## 5. Controllers

Controllers are responsible for handling incoming requests, processing data, and returning a response. Controllers are stored in the `app/Controllers` directory.

### Creating a Controller

```php
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
```

### Dependency Injection

Stride's container allows you to inject dependencies into your controllers:

```php
class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('users.index', ['users' => $users]);
    }
}
```

## 6. Models

Models in Stride provide a powerful and intuitive way to interact with your database. Models are stored in the `app/Models` directory.

### Creating a Model

```php
namespace App\Models;

use Framework\Core\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];
}
```

### Querying the Database

```php
// Get all users
$users = User::all();

// Get a user by ID
$user = User::find(1);

// Query the database
$users = User::where('status', 'active')->get();
```

## 7. Views

Views in Stride are responsible for rendering your application's UI. Views are stored in the `app/Views` directory and use a Blade-like templating engine.

### Creating a View

```blade
@extends('layouts.app')

@section('content')
    <h1>Users</h1>

    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>
@endsection
```

### Passing Data to Views

```php
return view('users.index', ['users' => $users]);
```

## 8. Authentication

Stride provides a complete authentication system to secure your application.

### Protecting Routes

```php
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth']);
```

### Accessing the Authenticated User

```php
$user = auth()->user();
```

## 9. Console Commands (CLI)

Stride's command-line interface (CLI) allows you to automate common tasks.

### Creating a Command

```php
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
```

### Running a Command

```bash
php stride my-command
```

## 10. Conclusion

This documentation provides a high-level overview of the Stride PHP Framework. For more detailed information, please refer to the source code and the examples provided in the repository.
