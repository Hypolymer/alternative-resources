<?php
    function logMessage($message)
    {
        $message = date('Y-m-d H:i:s') . "  " . $message . "\r\n";
        error_log($message,3,'appLog.txt');
    }
    function returnError($type,$message)
    {
        echo $type;
        echo "</br>";
        echo $message;
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    </head>
    <body>
        
    </body>
</html>
