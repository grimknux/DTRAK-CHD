<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;


class Welcome extends BaseController
{
    public function index()
    {
        echo "Welcome!";
    }

    public function test($name)
    {
        echo "Welcome to ".$name."!";
    }

    public function address($city, $country)
    {
        echo "I am from ".$city.", ".$country."!";
    }

    public function _remap($method, $param1 = null, $param2 = null)
    {
        if(method_exists($this, $method))
        {
            return $this->$method($param1, $param2);
        }
        else
        {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}

