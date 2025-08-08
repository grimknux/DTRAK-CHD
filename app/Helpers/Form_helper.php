<?php

function display_error($validation, $field){
    
    if(isset($validation))
    {
        if($validation->hasError($field))
        {
            return $validation->getError($field);
            //return '<div class="error-box-form error-form">'.$validation->getError($field).'</div>';
        }
        else
        {
            return false;
        }
    } 
}


function error_form($validation, $field){
    if(isset($validation))
    {
        
        return $validation;
        
    }else
    {
        return false;
    }
    
}
    

?>