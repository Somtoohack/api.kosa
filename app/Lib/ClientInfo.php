<?php

namespace App\Lib;

class ClientInfo
{
    /**
     * Get requestor IP information
     *
     * @return array
     */
    public static function ipInfo()
    {
        $ip = getRealIP();

        if ($ip == '127.0.0.1') {
            $ip = '102.88.84.85';
        }
        $jsonData = file_get_contents(
            'http://www.geoplugin.net/json.gp?ip=' . $ip
        );
        $data = json_decode($jsonData);
        $country = $data->geoplugin_countryName;
        $city = $data->geoplugin_city;
        $area = $data->geoplugin_areaCode;
        $code = $data->geoplugin_countryCode;
        $long = $data->geoplugin_longitude;
        $lat = $data->geoplugin_latitude;

        $info['country'] = $country ?? null;
        $info['city'] = $city ?? null;
        $info['area'] = $area ?? null;
        $info['code'] = $code ?? null;
        $info['long'] = $long ?? null;
        $info['lat'] = $lat ?? null;
        $info['ip'] = $ip;
        $info['time'] = date('Y-m-d h:i:s A');

        return $info;
    }

    /**
     * Get requestor operating system information
     *
     * @return array
     */
    public static function osBrowser()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $osPlatform = 'Unknown OS Platform';
        $osArray = [
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];
        foreach ($osArray as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $osPlatform = $value;
            }
        }
        $browser = 'Unknown Browser';
        $browserArray = [
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser',
        ];
        foreach ($browserArray as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $browser = $value;
            }
        }

        $data['os_platform'] = $osPlatform;
        $data['browser'] = $browser;

        return $data;
    }
}