<?php

class Home extends Controller
{
    public function home(): void
    {
        $this->render('home', [
            'title' => 'Sandbox Application',
        ]);
    }
}

