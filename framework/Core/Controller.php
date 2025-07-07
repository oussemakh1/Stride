<?php

namespace Framework\Core;

use Framework\Template\TemplateEngine;

class Controller
{
    protected $templateEngine;

    public function __construct(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Render a view with data.
     *
     * @param string $template The template file name (e.g., 'client.show')
     * @param array  $data     The data to pass to the view
     */
    protected function view(string $template, array $data = []): void
    {
        echo $this->templateEngine->render($template, $data);
    }
}
