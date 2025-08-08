<?php

namespace App\Controllers;

class Library extends BaseController
{
    public function viewlibrary($x,$y)
    {
        helper('inflector');
        
        echo "This is the ".$y.ordinal($y)." ".$x." library";
    }

    
}
 