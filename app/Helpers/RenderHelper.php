<?php

if (!function_exists('nl2br2')) {
    function nl2br2($string)
    {
        $string = str_replace(array("\r\n", "\r", "\n","\\r\\n", "\\r", "\\n"), "<br />", $string);
        return $string;
    }
}

if (!function_exists('html_order_status')) {
    function html_order_status($status_column_name, $status_name, $is_pay_from_wallet = false)
    {
        if ($is_pay_from_wallet) {
            $status_name .= '<br><i class="badge text-red badge-sm">(Trừ tiền từ Ví)</i>';
        }
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

if (!function_exists('html_poll_status')) {
    function html_poll_status($status_column_name, $status_name, $is_pay_from_wallet = false, $is_join = '')
    {
        $status_name = '';
        if ($is_pay_from_wallet) {
            $status_name = '<br><i class="badge text-red badge-sm">(Trừ tiền từ Ví)</i>';
        }
        if (isset($is_join) && $is_join === 0) {
            $status_name = '<br><i class="badge text-red badge-sm">(Vắng mặt)</i>';
        }
        switch ($status_column_name) {
            case 'order':
                $string = '<span class="badge badge-rounded badge-outline-warning">Điểm danh'.$status_name.'</span>';
                break;
            
            case 'booked':
                $string = '<span class="badge badge-rounded badge-outline-info">Điểm danh'.$status_name.'</span>';
                break;
            
            case 'unpaid':
                $string = '<span class="badge badge-rounded badge-outline-danger">Chưa thanh toán'.$status_name.'</span>';
                break;
            
            case 'paid':
                $string = '<span class="badge badge-rounded badge-outline-success">Đã thanh toán'.$status_name.'</span>';
                break;
            
            case 'cancel':
                $string = '<span class="badge badge-rounded badge-outline-dark">Cancel'.$status_name.'</span>';
                break;
                                        
            default:
                $string = '';
                break;
        }
        return $string;
    }
}

if (!function_exists('label_poll_status')) {
    function label_poll_status($status_column_name, $order_date)
    {
        switch ($status_column_name) {
            case 'order':
            case 'booked':
            case 'unpaid':
                $string = 'Open';
                if ($order_date < date("Y-m-d")) {
                    $string = 'End';
                }
                break;
            
            case 'paid':
                $string = 'End';
                break;
            
            case 'cancel':
                $string = 'Cancel';
                break;
                                        
            default:
                $string = '';
                break;
        }
        return $string;
    }
}