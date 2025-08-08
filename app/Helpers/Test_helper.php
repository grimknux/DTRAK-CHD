<?php

    function getRandomString($str)
    {
       //return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 10, 10);
       return str_shuffle($str);
    }
?> 