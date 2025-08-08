<?php

namespace App\Controllers;
// use CodeIgniter\Exceptions\PageNotFoundException;


class Blog extends BaseController
{
        public $parser;
    public function __construct(){
        $this->parser = \Config\Services::parser();
    }

    public function index()
    {

        $data = [
            "page_title" => "Welcome to my CI4",
            "page_heading" => "Welcome to my CI4s",
            //"subject" => ["php","css","code","visual","ruby","java","yes"]
            "subject_list" => [
                ["subject" => "PHP", "abbr" => "PHP Hypertext Preprocessor"],
                ["subject" => "CSS", "abbr" => "Cascading Stylesheet"],
                ["subject" => "HTML", "abbr" => "Hypertext Markup Language"],
                ["subject" => "AJAX", "abbr" => "Asynchronous JavaScript and XML"],
            ],
            "status" => true,
        ];

        //$parser->setData($data);
        //return $parser->render("myview");
        return $this->parser->setData($data)->render("myview");
        //return view('myview', $data);
    }

    public function ViewFilter()
    {
        

        $data = [
            "page_title" => "Welcome to my CI4",
            "page_heading" => "Welcome to my CI4s blogee",
            //"subject" => ["php","css","code","visual","ruby","java","yes"]
            "date" => "06-09-1992",
            "price" => "500",
            "price_one" => "100.56",
            "mobile" => "8855336699",
            "subject_list" => [
                ["subject" => "PHP", "abbr" => "PHP Hypertext Preprocessor"],
                ["subject" => "CSS", "abbr" => "Cascading Stylesheet"],
                ["subject" => "HTML", "abbr" => "Hypertext Markup Language"],
                ["subject" => "AJAX", "abbr" => "Asynchronous JavaScript and XML"],
            ],
            "status" => true,
        ];

        //$parser->setData($data);
        //return $parser->render("myview");
        return $this->parser->setData($data)->render("viewfilter");
        //return view('myview', $data);
    }
}