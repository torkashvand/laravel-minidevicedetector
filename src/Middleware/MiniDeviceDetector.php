<?php

namespace Torkashvand\MiniDeviceDetector\Middleware;

use Closure;

class MiniDeviceDetector
{
    public function loadFromSearchStringsFile()
    {
        $f = null;
        $lines = [];
        try {
            $file_path = dirname(__FILE__) . '/../search_string.txt';
            $handle = fopen($file_path, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    if ($line[0] != '#') {
                        array_push($lines, $line);
                    }
                }

                fclose($handle);
            }
        } catch (\Exception $e) {

        }

        return $lines;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//         defaults (we assume this is a desktop)
        $request->isSimpleDevice = false;
        $request->isTouchDevice = false;
        $request->isWideDevice = true;
        $request->mobile = false;
        $request->isWebkit = false;
        $request->isIOSDevice = false;
        $request->isAndroidDevice = false;
        $request->isWebOSDevice = false;
        $request->isWindowsPhoneDevice = false;

        if ($request->header('X-OPERAMINI-FEATURES') != null) {
            $request->isSimpleDevice = true;
            $request->mobile = true;

            return $next($request);
        }

        if ($request->header('ACCEPT') != null) {
            $s = strtolower($request->header('ACCEPT'));
            if (strpos($s, 'application/vnd.wap.xhtml+xml') !== false) {
                # Then it's a wap browser
                $request->isSimpleDevice = true;
                $request->mobile = true;

                return $next($request);
            }
        }

        if ($request->header('USER-AGENT') != null) {
            # This takes the most processing. Surprisingly enough, when I
            # Experimented on my own machine, this was the most efficient
            # algorithm. Certainly more so than regexes.
            # Also, Caching didn't help much, with real-world caches.
            $s = strtolower($request->header('USER-AGENT'));

            if (strpos($s, 'applewebkit') !== false) {
                $request->isWebkit = true;
            }

            if (strpos($s, 'ipad') !== false) {
                $request->isIosDevice = true;
                $request->isTouchDevice = true;
                $request->isWideDevice = true;

                return $next($request);
            }

            if (strpos($s, 'iphone') !== false || strpos($s, 'ipod') !== false) {
                $request->isIosDevice = true;
                $request->isTouchDevice = true;
                $request->isWideDevice = true;
                $request->mobile = true;

                return $next($request);
            }

            if (strpos($s, 'android') !== false) {
                $request->isAndroidDevice = true;
                $request->isTouchDevice = true;
                $request->isWideDevice = false; # TODO add support for andriod tablets
                $request->mobile = true;

                return $next($request);

            }

            if (strpos($s, 'webos') !== false) {
                $request->isWebosDevice = true;
                $request->isTouchDevice = true;
                $request->isWideDevice = false; # TODO add support for webOS tablets
                $request->mobile = true;

                return $next($request);
            }

            if (strpos($s, 'windows phone') !== false) {
                $request->isWindowsPhoneDevice = true;
                $request->isTouchDevice = true;
                $request->isWideDevice = false;
                $request->mobile = true;

                return $next($request);
            }

            foreach ($this->loadFromSearchStringsFile() as $ua) {
                if (strpos($s, $ua) !== false) {
                    $request->isSimpleDevice = true;
                    $request->mobile = true;

                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}

