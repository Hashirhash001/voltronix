<?php
if (!function_exists('get_json_input')) {
    function get_json_input() {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
}
