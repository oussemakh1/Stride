<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My App - <?php $this->yield('title'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo Framework\Security\Csrf::getToken(); ?>">
</head>
<body>
    <header>
        <nav class="bg-blue-500 p-4 text-white">
            <div class="container mx-auto flex justify-between items-center">
                <a href="/" class="text-lg font-bold">My App</a>
                <div>
                    <a href="/appels" class="px-3 py-2 hover:bg-blue-700 rounded">Appels</a>
                    <?php if (Framework\Support\Auth::check()): ?>
                        <a href="/logout" class="px-3 py-2 hover:bg-blue-700 rounded">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="px-3 py-2 hover:bg-blue-700 rounded">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto mt-8 p-4">
        <?php $this->yield('content'); ?>
    </main>

    <footer>
        <div class="container mx-auto p-4 text-center text-gray-600">
            &copy; <?php echo date('Y'); ?> My App. All rights reserved.
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Automatically add CSRF token to all POST forms
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[method="post"]');
            forms.forEach(form => {
                if (!form.querySelector('input[name="_token"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(input);
                }
            });
        });
    </script>
</body>
</html>
