<?php
    function logMessage($message)
    {
        $message = date('Y-m-d H:i:s') . "  " . $message . "\r\n";
        error_log($message,3,'appLog.txt');
    }
    function returnError($type,$message)
    {
        $msg = "Type: $type | Message: $message";
        logMessage($msg);
    }

?>