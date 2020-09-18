<?php

    /**
     * @param string $message
     * @param array $param
     * @param string $stack
     */
    function wc1c_save_error_message($message, $param = array(), $stack = "")
    {
        $dir = WC1C_DATA_DIR . "log/";
        $path = WC1C_DATA_DIR . "log/error.txt";

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $dateNow = date('Y-m-d h:i:s');
        $log_text = "DATE $dateNow \r\n";
        file_put_contents($path, $log_text, FILE_APPEND);

        $_message = print_r($message, true);
        $log_text = "MESSAGE: $_message \r\n";
        file_put_contents($path, $log_text, FILE_APPEND);

        foreach ($param as $_key => $_value) {
            $_message = print_r($_value, true);
            $log_text = "$_key: $_message \r\n";
            file_put_contents($path, $log_text, FILE_APPEND);
        }
    }

