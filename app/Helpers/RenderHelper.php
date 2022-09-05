<?php

if (!function_exists('nl2br2')) {
    function nl2br2($string)
    {
        $string = str_replace(array("\r\n", "\r", "\n","\\r\\n", "\\r", "\\n"), "<br />", $string);
        return $string;
    }
}

if (!function_exists('html_order_status')) {
    function html_order_status($status_column_name, $status_name)
    {
        switch ($status_column_name) {
            case 'order':
                $string = '<span class="badge badge-rounded badge-outline-warning">'.$status_name.'</span>';
                break;
            
            case 'booked':
                $string = '<span class="badge badge-rounded badge-outline-info">'.$status_name.'</span>';
                break;
            
            case 'unpaid':
                $string = '<span class="badge badge-rounded badge-outline-danger">'.$status_name.'</span>';
                break;
            
            case 'paid':
                $string = '<span class="badge badge-rounded badge-outline-success">'.$status_name.'</span>';
                break;
            
            case 'cancel':
                $string = '<span class="badge badge-rounded badge-outline-dark">'.$status_name.'</span>';
                break;
                                        
            default:
                $string = '';
                break;
        }
        return $string;
    }
}