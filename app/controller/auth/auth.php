<?php

class Auth extends Controller
{

    public function index(): void{

        $this->render('auth', [

            'pageTitle' => 'Login Area | E-Musabaqah (Nama Daerah)',
            'appName'   => 'E-Musabaqah LPTQ Provinsi Sulawesi Tengah'

        ]);

    }

}

?>