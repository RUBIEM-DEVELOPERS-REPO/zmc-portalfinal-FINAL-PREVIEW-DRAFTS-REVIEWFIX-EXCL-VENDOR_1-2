<?php
 
namespace App\Support;
 
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
 
class LoginHistory
{
    public static function record($user, Request $request, bool $successful = true, string $failureReason = null): void
    {
        try {
            $userAgent = $request->userAgent();
            $ip = $request->ip();
 
            // Basic parsing for OS and Browser
            $os = self::getOS($userAgent);
            $browser = self::getBrowser($userAgent);
            $machineName = gethostbyaddr($ip) ?: 'Unknown';
 
            LoginActivity::create([
                'user_id'           => $user?->id,
                'account_name'      => $user?->email,
                'ip_address'        => $ip,
                'user_agent'        => substr($userAgent, 0, 500),
                'device_identifier' => $machineName,
                'operating_system'  => $os,
                'browser_name'      => $browser['name'],
                'browser_version'   => $browser['version'],
                'login_at'          => now(),
                'login_successful'  => $successful,
                'failure_reason'    => $failureReason,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to record login history: ' . $e->getMessage());
        }
    }
 
    private static function getOS(string $userAgent): string
    {
        $osPlatform = "Unknown OS";
        $osArray = [
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        ];
 
        foreach ($osArray as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                $osPlatform = $value;
            }
        }
 
        return $osPlatform;
    }
 
    private static function getBrowser(string $userAgent): array
    {
        $browserName = "Unknown Browser";
        $version = "Unknown";
 
        if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
            $browserName = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browserName = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browserName = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browserName = 'Safari';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browserName = 'Opera';
        } elseif (preg_match('/Netscape/i', $userAgent)) {
            $browserName = 'Netscape';
        }
 
        $known = ['Version', $browserName, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (preg_match_all($pattern, $userAgent, $matches)) {
            $i = count($matches['browser']);
            if ($i != 1) {
                if (strripos($userAgent, "Version") < strripos($userAgent, $browserName)) {
                    $version = $matches['version'][0];
                } else {
                    $version = $matches['version'][1];
                }
            } else {
                $version = $matches['version'][0];
            }
        }
 
        return ['name' => $browserName, 'version' => $version];
    }
}
