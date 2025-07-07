<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stride - Modern PHP Framework</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#1e293b'
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-gray-50 text-gray-800">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="bg-indigo-600 text-white font-bold text-xl px-3 py-2 rounded-lg">S</div>
                        <span class="ml-3 text-xl font-bold text-gray-900">Stride</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Features</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Documentation</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Examples</a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Community</a>
                </div>
                <div class="flex items-center">
                    <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 md:pt-32 md:pb-24 bg-gradient-to-r from-indigo-50 to-purple-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900">
                    <span class="block">Stride: Your Next PHP Project</span>
                    <span class="block text-indigo-600 mt-2">Starts Here</span>
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600">
                    A lightweight, powerful, and intuitive PHP framework designed for speed and simplicity. 
                    Build modern web applications with confidence and ease.
                </p>
                <div class="mt-10 flex justify-center">
                    <a href="#" class="px-8 py-4 bg-indigo-600 text-white text-lg font-medium rounded-lg shadow-lg hover:bg-indigo-700 transition-colors duration-300">
                        Get Started
                    </a>
                    <a href="#" class="ml-4 px-8 py-4 bg-white text-indigo-600 text-lg font-medium rounded-lg shadow-lg hover:bg-gray-50 transition-colors duration-300">
                        View on GitHub
                    </a>
                </div>
            </div>
            <div class="mt-16 mx-auto max-w-5xl">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                    <div class="bg-gray-900 p-4 flex items-center">
                        <div class="flex space-x-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="ml-4 text-sm text-gray-400">routes/web.php</div>
                    </div>
                    <div class="p-6 font-mono">
                        <div class="text-indigo-600">Route::get('/', function () {</div>
                        <div class="ml-4 text-purple-600">&nbsp;&nbsp;return view('welcome', [</div>
                        <div class="ml-8 text-blue-600">&nbsp;&nbsp;&nbsp;&nbsp;'title' => 'Welcome to Stride',</div>
                        <div class="ml-8 text-blue-600">&nbsp;&nbsp;&nbsp;&nbsp;'message' => 'Build amazing apps with speed!'</div>
                        <div class="ml-4 text-purple-600">&nbsp;&nbsp;]);</div>
                        <div class="text-indigo-600">});</div>
                        <div class="mt-4 text-indigo-600">Route::resource('posts', PostController::class);</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Powerful Features, Simplified
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                    Everything you need to build modern PHP applications with elegance and efficiency.
                </p>
            </div>
            
            <div class="mt-16 grid gap-12 md:grid-cols-2 lg:grid-cols-4">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 text-indigo-600">
                        <i class="fas fa-sitemap text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-900">Intuitive MVC Architecture</h3>
                    <p class="mt-2 text-gray-600">
                        Organize code with clear separation of concerns for structured, scalable applications.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-route text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-900">Expressive Routing</h3>
                    <p class="mt-2 text-gray-600">
                        Define clean routes for web and API endpoints with a flexible routing system.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-cube text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-900">Dependency Injection</h3>
                    <p class="mt-2 text-gray-600">
                        Easily manage dependencies for modular, testable, and cleaner code.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-database text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-bold text-gray-900">Integrated ORM</h3>
                    <p class="mt-2 text-gray-600">
                        Simplify data operations with an elegant Object-Relational Mapping system.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Stride? -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Why Choose Stride?
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                    The framework that balances power with simplicity for PHP developers.
                </p>
            </div>
            
            <div class="mt-16">
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Reason 1 -->
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <div class="text-indigo-600 text-5xl font-bold">01</div>
                        <h3 class="mt-4 text-xl font-bold text-gray-900">Lightweight & Performant</h3>
                        <p class="mt-4 text-gray-600">
                            Built for speed and efficiency, Stride ensures your applications run fast with minimal resource consumption. 
                            No unnecessary bloat - just pure performance.
                        </p>
                    </div>
                    
                    <!-- Reason 2 -->
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <div class="text-indigo-600 text-5xl font-bold">02</div>
                        <h3 class="mt-4 text-xl font-bold text-gray-900">Intuitive Development</h3>
                        <p class="mt-4 text-gray-600">
                            With expressive syntax and well-designed architecture, Stride makes development enjoyable and productive. 
                            Focus on features, not boilerplate.
                        </p>
                    </div>
                    
                    <!-- Reason 3 -->
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <div class="text-indigo-600 text-5xl font-bold">03</div>
                        <h3 class="mt-4 text-xl font-bold text-gray-900">Built-in Security</h3>
                        <p class="mt-4 text-gray-600">
                            Essential security measures like CSRF protection keep your applications safe from common web vulnerabilities. 
                            Security features are baked right in.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Features -->
    <section class="py-16 bg-indigo-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold sm:text-4xl">
                    And So Much More
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-indigo-200">
                    Stride comes packed with features that modern PHP developers need.
                </p>
            </div>
            
            <div class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Robust Event Handling</h4>
                        <p class="mt-2 text-indigo-200">Implement decoupled components using built-in event dispatcher.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Efficient Templating</h4>
                        <p class="mt-2 text-indigo-200">Create dynamic views with a simple yet powerful templating system.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Middleware Support</h4>
                        <p class="mt-2 text-indigo-200">Add layers like authentication or logging to HTTP requests.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Job Queues</h4>
                        <p class="mt-2 text-indigo-200">Handle long-running tasks in the background for better UX.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Smart Caching</h4>
                        <p class="mt-2 text-indigo-200">Optimize performance by caching frequently accessed data.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-indigo-500">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium">Error Handling</h4>
                        <p class="mt-2 text-indigo-200">Clear, configurable error reporting for smoother debugging.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold sm:text-4xl">
                Ready to Stride into Modern PHP?
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-xl">
                Join thousands of developers building amazing applications with Stride.
            </p>
            <div class="mt-8">
                <a href="#" class="px-8 py-4 bg-white text-indigo-600 text-lg font-bold rounded-lg shadow-lg hover:bg-gray-100 transition-colors duration-300">
                    Get Started in Minutes
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-indigo-600 text-white font-bold text-xl px-3 py-2 rounded-lg">S</div>
                    <span class="ml-3 text-xl font-bold">Stride</span>
                </div>
                <div class="mt-8 md:mt-0">
                    <p class="text-gray-400">
                        © 2025 Stride Framework. All rights reserved.
                    </p>
                </div>
                <div class="mt-8 md:mt-0 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-discord text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-sm text-gray-400">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-white font-medium">Documentation</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="hover:text-white">Getting Started</a></li>
                            <li><a href="#" class="hover:text-white">Tutorials</a></li>
                            <li><a href="#" class="hover:text-white">API Reference</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-medium">Resources</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="hover:text-white">Examples</a></li>
                            <li><a href="#" class="hover:text-white">Packages</a></li>
                            <li><a href="#" class="hover:text-white">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-medium">Community</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="hover:text-white">Forums</a></li>
                            <li><a href="#" class="hover:text-white">GitHub</a></li>
                            <li><a href="#" class="hover:text-white">Discord</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-white font-medium">Company</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="hover:text-white">About</a></li>
                            <li><a href="#" class="hover:text-white">Contribute</a></li>
                            <li><a href="#" class="hover:text-white">Sponsor</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>