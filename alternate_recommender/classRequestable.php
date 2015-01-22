<?php

class requestable 
{
    /*ToDo: These all need to be protected*/
    public $title = "";
    public $author = "";
    public $format = "";
    public $oclcn = "";
    public $language = "";
    public $edition = "";
    public $lenders = array();
    public $timeToFill = ""; /*This is definitely the wrong way to get/set*/
    public function getTimeToFill() 
    {
        return 4;
    }
}

?>