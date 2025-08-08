<?php

namespace App\Controllers;

class TestHelpers extends BaseController
{
    public function index()
    {
        
        helper(['form','html','cookie','array', 'test']);

        echo form_open();
        echo form_input('username', 'testvalue');
        /*
        echo base_url();
        echo current_url();
        */

        echo getRandom([10,20,30,40,50,60,70,80,90]);
        echo getRandomString('greg');
    }

}
 