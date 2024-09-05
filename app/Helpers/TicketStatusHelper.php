<?php

namespace App\Helpers;

class TicketStatusHelper {
    
    public static function status_name($status)
    {
        $name = "";
        switch ($status) {
            case 'new' :
                $name = "New";
                break;
            case 'rejected' :
                $name = "Rejected";
                break;
            case 'approved' :
                $name = "Approved";
                break;
            case 'pending' :
                $name = "Pending";
                break;
        }

        return $name;
    }

    public static function status_round_color($status)
    {
        $name = "";
        switch ($status) {
            case 'new' :
                $name = "info";
                break;
            case 'rejected' :
                $name = "danger";
                break;
            case 'approved' :
                $name = "success";
                break;
            case 'pending' :
                $name = "info";
                break;
        }

        return $name;
    }
}
