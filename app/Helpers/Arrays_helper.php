<?php

function getRandom($arr)
{
    shuffle($arr);
    return end($arr);
}

?>