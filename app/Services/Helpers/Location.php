<?php

namespace App\Services\Helpers;

class Location
{

    /**
     * Get the client ip address
     *
     * @return mixed string|null
     */
    public static function getClientIp() : string|null
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }

        //
        return null;
    }

    /**
     * Convert IPv4 unsigned integer - INET_ATON()
     *
     * @param string $ipv4
     * @return int
     */
    public static function ipv4ToLong($ipv4) : int
    {
        return (int) sprintf('%u',ip2long($ipv4));
    }

    /**
     * Convert unsigned integer compatible IPv4 - INET_NTOA()
     *
     * @param int $long
     * @return string
     */
    public static function longToIpv4($long) : string
    {
        return long2ip(sprintf("%d", $long));
    }

}
