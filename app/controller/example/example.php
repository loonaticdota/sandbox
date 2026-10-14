<?php

class Example extends Controller
{
    public function index(): void
    {
        $this->render('example', [
            'pageTitle'   => 'Modern Landing Page Example',
            'appName'     => 'Sandbox MVC',
            'headline'    => 'Build Faster with Lightweight Modern PHP',
            'subheadline' => 'A clean MVC skeleton featuring zero-dependency JWT authentication, high-speed routing, and async task concurrency.',
            'features'    => [
                [
                    'icon'  => '⚡',
                    'title' => 'Minimalist MVC',
                    'desc'  => 'No bloated vendor lock-in. Clean routing, fast controllers, and intuitive directory structure.',
                ],
                [
                    'icon'  => '🔒',
                    'title' => 'Built-In JWT Security',
                    'desc'  => 'Zero-dependency token creation and verification for modern web pages and REST APIs.',
                ],
                [
                    'icon'  => '🧵',
                    'title' => 'Concurrency & Async',
                    'desc'  => 'Fire-and-forget background workers and simultaneous PHP 8.1 Fiber tasks out of the box.',
                ],
            ],
            'year'        => date('Y'),
        ]);
    }
}

