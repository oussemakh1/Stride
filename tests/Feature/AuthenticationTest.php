<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use Framework\Support\Auth;
use Framework\Support\Config;
use Framework\Http\RouteHandler;
use Framework\Container\Container;
use App\Middleware\AuthMiddleware;
use Framework\Http\Request;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Manually load .env file and populate $_ENV for tests
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        putenv('APP_ENV=testing');

        // Load application configuration
        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        $cacheConfig = require dirname(__DIR__, 2) . '/config/cache.php';
        $databaseConfig = require dirname(__DIR__, 2) . '/config/database.php';
        Config::load(array_merge(['app' => $appConfig], ['cache' => $cacheConfig, 'database' => $databaseConfig, 'sidebar' => require dirname(__DIR__, 2) . '/config/sidebar.php']));

        // Start session for tests
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Truncate the users table before each test
        User::truncateTable('users');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Destroy session after each test
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }

        // Reset PDO instance to ensure fresh connection for next test
        \Framework\Database\Connection::resetInstance();
    }

    public function testUserAuthentication(): void
    {
        // 1. Create a test user
        $password = password_hash('password', PASSWORD_DEFAULT);
        $creationSuccess = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
        ]);

        $this->assertTrue($creationSuccess, 'Failed to create test user.');

        $user = User::findBy('email', 'test@example.com');
        $this->assertNotNull($user, 'Test user not found after creation.');

        // 2. Attempt to log in
        $loggedIn = Auth::attempt('test@example.com', 'password');
        $this->assertTrue($loggedIn, 'Login failed.');

        // 3. Verify login status
        $this->assertTrue(Auth::check(), 'User is not logged in.');

        // 4. Verify user data
        $currentUser = Auth::user();
        $this->assertNotNull($currentUser, 'No user data found.');
        $this->assertEquals('Test User', $currentUser->name);
        $this->assertEquals('test@example.com', $currentUser->email);
        $this->assertEquals('commercial', $currentUser->role); // Assuming role is set during login

        // 5. Test roles and permissions
        $this->assertTrue(Auth::hasRole('commercial'), 'User does NOT have \'commercial\' role.');
        $this->assertTrue(Auth::hasPermission('manage_appels'), 'User does NOT have \'manage_appels\' permission.');
        $this->assertFalse(Auth::hasRole('admin'), 'User incorrectly has \'admin\' role.');

        // 6. Log out
        Auth::logout();
        $this->assertFalse(Auth::check(), 'User is still logged in after logout.');
    }

    public function testAuthMiddlewareRedirectsUnauthenticatedUser(): void
    {
        // Simulate unauthenticated request
        $_SERVER['REQUEST_URI'] = '/dashboard';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = new Container();
        $router = new RouteHandler($container);

        // Re-define the route with middleware for this test
        $router->get('/dashboard', [\App\Controllers\TestController::class, 'dashboard'])->middleware([\App\Middleware\AuthMiddleware::class]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Redirect to /login');

        $router->handleRequest();
    }

    public function testAuthMiddlewareAllowsAuthenticatedUser(): void
    {
        // Simulate authenticated request
        $password = password_hash('password', PASSWORD_DEFAULT);
        User::create([
            'name' => 'Test Middleware User',
            'email' => 'test_middleware@example.com',
            'password' => $password,
        ]);
        Auth::attempt('test_middleware@example.com', 'password');

        $_SERVER['REQUEST_URI'] = '/dashboard';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = new Container();
        $router = new RouteHandler($container);

        // Re-define the route with middleware for this test
        $router->get('/dashboard', [\App\Controllers\TestController::class, 'dashboard'])->middleware([\App\Middleware\AuthMiddleware::class]);

        ob_start();
        $router->handleRequest();
        $output = ob_get_clean();

        $this->assertStringContainsString('Welcome to the dashboard!', $output, 'Authenticated user should access the dashboard');

        Auth::logout();
    }
}