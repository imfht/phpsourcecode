<?php
/* Copyright 2015  JefferyWang  (email: admin@wangjunfeng.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function jw_wlu_detect_os() {
    global $useragent;

    $title = "";
    $device = 'desktop';
    $class = "";

    if (preg_match('/Android/i', $useragent)) {
        $title = "Android";
        $device = 'android';
        $class = "os_android";
    } elseif (preg_match('/Mac/i', $useragent)) {
        $title = "Mac OS X";
        $device = 'desktop';
        $class = "os_mac";
    } elseif (preg_match('/iphone/i', $useragent)) {
        $title = "iPhone IOS";
        $device = 'mobile';
        $class = "os_mac";
    } elseif (preg_match('/iPad/i', $useragent)) {
        $title = "iPad IOS";
        $device = 'tablet';
        $class = "os_mac";
    } elseif (preg_match('/Symb[ian]?[OS]?/i', $useragent)) {
        $title = "SymbianOS";
        $device = 'mobile';
        $class = "os_nokia";
    } elseif (preg_match('/Unix/i', $useragent)) {
        $title = "Unix";
        $device = 'linux';
        $class = "os_linux";
    } elseif (preg_match('/Windows/i', $useragent)
        || preg_match('/WinNT/i', $useragent)
        || preg_match('/Win32/i', $useragent)
    ) {
        $device = 'desktop';
        if (preg_match('/Windows NT 10.0; Win64; x64/i', $useragent)
            || preg_match('/Windows NT 10.0; WOW64/i', $useragent)
            || preg_match('/Windows NT 6.4; Win64; x64/i', $useragent)
            || preg_match('/Windows NT 6.4; WOW64/i', $useragent)
        ) {
            $title = "Windows 10 x64 Edition";
            $class = "os_8_1";
        } elseif (preg_match('/Windows NT 10.0/i', $useragent)
            || preg_match('/Windows NT 6.4/i', $useragent)
        ) {
            $title = "Windows 10";
            $class = "os_8_1";
        } elseif (preg_match('/Windows NT 6.3; Win64; x64/i', $useragent)
            || preg_match('/Windows NT 6.3; WOW64/i', $useragent)
        ) {
            $title = "Windows 8.1 x64 Edition";
            $class = "os_8_1";
        } elseif (preg_match('/Windows NT 6.3/i', $useragent)) {
            $title = "Windows 8.1";
            $class = "os_8_1";
        } elseif (preg_match('/Windows NT 6.2; Win64; x64/i', $useragent)
            || preg_match('/Windows NT 6.2; WOW64/i', $useragent)
        ) {
            $title = "Windows 8 x64 Edition";
            $class = "os_8";
        } elseif (preg_match('/Windows NT 6.2/i', $useragent)) {
            $title = "Windows 8";
            $class = "os_8";
        } elseif (preg_match('/Windows NT 6.1; Win64; x64/i', $useragent)
            || preg_match('/Windows NT 6.1; WOW64/i', $useragent)
        ) {
            $title = "Windows 7 x64 Edition";
            $class = "os_7";
        } elseif (preg_match('/Windows NT 6.1/i', $useragent)) {
            $title = "Windows 7";
            $class = "os_7";
        } elseif (preg_match('/Windows NT 6.0/i', $useragent)) {
            $title = "Windows Vista";
            $class = "os_vista";
        } elseif (preg_match('/Windows NT 5.2 x64/i', $useragent)) {
            $title = "Windows XP x64 Edition";
            $class = "os_xp";
        } elseif (preg_match('/Windows NT 5.2; Win64; x64/i', $useragent)) {
            $title = "Windows Server 2003 x64 Edition";
            $class = "os_2000";
        } elseif (preg_match('/Windows NT 5.2/i', $useragent)) {
            $title = "Windows Server 2003";
            $class = "os_2000";
        } elseif (preg_match('/Windows NT 5.1/i', $useragent)
            || preg_match('/Windows XP/i', $useragent)
        ) {
            $title = "Windows XP";
            $class = "os_xp";
        } elseif (preg_match('/Windows NT 5.01/i', $useragent)) {
            $title = "Windows 2000, Service Pack 1 (SP1)";
            $class = "os_2000";
        } elseif (preg_match('/Windows NT 5.0/i', $useragent)
            || preg_match('/Windows 2000/i', $useragent)
        ) {
            $title = "Windows 2000";
            $class = "os_2000";
        } elseif (preg_match('/WindowsMobile/i', $useragent)) {
            $title = "Windows Mobile";
            $class = 'os_windows';
            $device = 'mobile';
        } else {
            $title = "Windows";
            $class = 'os_windows';
        }
    } elseif (preg_match('/Ubuntu/i', $useragent)) {
        $title = "Ubuntu";

        if (preg_match('/x86_64/i', $useragent)) {
            $title .= " x64";
        }
        $device = 'linux';
        $class = 'os_linux';
    } elseif (preg_match('/Linux/i', $useragent)) {
        $title = "GNU/Linux";

        if (preg_match('/x86_64/i', $useragent)) {
            $title .= " x64";
        }

        $device = 'linux';
        $class = 'os_linux';
    } elseif (preg_match('/J2ME\/MIDP/i', $useragent)) {
        $title = "J2ME/MIDP Device";
        $device = 'mobile';
        $class = 'os_other';
    } else {
        $title = "其它操作系统";
        $device = 'desktop';
        $class = 'os_other';
    }

    return '<span class="ua"><span class="' . $class . '"><i class="fa fa-' . $device . '"></i>' . $title . '</span></span>';
}
