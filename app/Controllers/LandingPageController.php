<?php

namespace App\Controllers;

use Framework\Core\Controller;
use Framework\Template\TemplateEngine;

class LandingPageController extends Controller
{
    protected $templateEngine;

    public function __construct(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function index()
    {
        return $this->templateEngine->render('landingpage.index');
    }

    public function documentation()
    {
        return $this->templateEngine->render('landingpage.docs');
    }
}
