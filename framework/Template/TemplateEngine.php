<?php

namespace Framework\Template;

class TemplateEngine
{
    protected string $viewPath;
    protected string $cachePath;
    protected array $sections = [];
    protected array $sectionStack = [];
    protected ?string $extends = null;
    protected array $customFunctions = [];
    protected array $filters = [];
    protected bool $cacheEnabled = false;
    protected int $cacheLifetime = 3600;

    public function __construct(string $viewPath, string $cachePath)
    {
        $this->viewPath = rtrim($viewPath, '/') . '/';
        $this->cachePath = rtrim($cachePath, '/') . '/';
    }

    public function enableCache(int $lifetime = 3600): void
    {
        $this->cacheEnabled = true;
        $this->cacheLifetime = $lifetime;
    }

    public function disableCache(): void
    {
        $this->cacheEnabled = false;
    }

    public function registerFunction(string $name, callable $callback): void
    {
        $this->customFunctions[$name] = $callback;
    }

    public function registerFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    public function render(string $template, array $data = []): string
    {
        $templateFile = str_replace('.', '/', $template) . '.php';
        $templatePath = $this->viewPath . $templateFile;
        $compiledPath = $this->cachePath . md5($templatePath) . '.php';
        $cacheKey = md5($templatePath . serialize($data));
        $cachedFilePath = $this->cachePath . $cacheKey . '.html';

        if (!file_exists($templatePath)) {
            throw new \Exception("Template file not found: {$templatePath}");
        }

        if (!file_exists($compiledPath) || filemtime($templatePath) > filemtime($compiledPath)) {
            $this->compile($templatePath, $compiledPath);
        }

        if (
            $this->cacheEnabled &&
            file_exists($cachedFilePath) &&
            (filemtime($cachedFilePath) + $this->cacheLifetime) > time()
        ) {
            return file_get_contents($cachedFilePath);
        }

        extract($data);

        $this->sections = [];
        $this->sectionStack = [];
        $this->extends = null;

        ob_start();
        include $compiledPath;
        $output = ob_get_clean();

        if ($this->extends) {
            $parentTemplate = $this->extends;
            $this->extends = null;
            $finalContent = $this->render($parentTemplate, $data);
        } else {
            $finalContent = $output;
        }

        if ($this->cacheEnabled) {
            file_put_contents($cachedFilePath, $finalContent);
        }

        return $finalContent;
    }

    protected function compile(string $templatePath, string $compiledPath): void
    {
        $content = file_get_contents($templatePath);

        // Handle @extends, @section, @endsection, @yield
        $content = preg_replace("/@extends\s*\(\s*'([^']+)'\s*\)/", "<?php \$this->extends('$1'); ?>", $content);
        $content = preg_replace("/@section\s*\(\s*'([^']+)'\s*\)/", "<?php \$this->section('$1'); ?>", $content);
        $content = preg_replace("/@endsection/", "<?php \$this->endSection(); ?>", $content);
        $content = preg_replace("/@yield\s*\(\s*'([^']+)'\s*\)/", "<?php \$this->yield('$1'); ?>", $content);

        // Handle raw output: {!! ... !!}
        $content = preg_replace('/{!!\s*(.*?)\s*!!}/', '<?php echo $1; ?>', $content);

        // Handle unescaped output: {{{ ... }}}
        $content = preg_replace('/\{\{\{\s*(.*?)\s*\}\}\}/', '<?php echo $1; ?>', $content);

        // Handle filters: {{ $var | filterName }}
        $content = preg_replace_callback('/{{\s*(\$[a-zA-Z0-9_]+)\s*\|\s*([a-zA-Z0-9_]+)\s*}}/', function ($matches) {
            $var = $matches[1];
            $filter = $matches[2];
            return "<?php echo htmlspecialchars(\$this->filters['$filter']($var)); ?>";
        }, $content);

        // Handle custom functions: {{ functionName(...) }}
        $content = preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\((.*?)\)\s*}}/', function ($matches) {
            $func = $matches[1];
            $args = $matches[2];
            return "<?php echo htmlspecialchars(\$this->customFunctions['$func']($args)); ?>";
        }, $content);

        // Default escaped output: {{ ... }}
        $content = preg_replace('/{{\s*(.*?)\s*}}/', '<?php echo htmlspecialchars($1); ?>', $content);

        file_put_contents($compiledPath, $content);
    }

    protected function extends(string $template): void
    {
        $this->extends = $template;
    }

    protected function section(string $name): void
    {
        $this->sectionStack[] = $name;
        ob_start();
    }

    protected function endSection(): void
    {
        $name = array_pop($this->sectionStack);
        $this->sections[$name] = ob_get_clean();
    }

    protected function yield(string $name): void
    {
        echo $this->sections[$name] ?? '';
    }
}
