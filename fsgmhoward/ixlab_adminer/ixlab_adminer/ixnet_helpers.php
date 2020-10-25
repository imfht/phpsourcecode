<?php
/* This library is published under MIT License
 * Copyright (C) 2016 Howard Liu
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
/**
 * Helper Functions for Plugins / Programs by IX Network Studio
 * @author Howard Liu <howard@ixnet.work>
 */
/**
 * Check the current version whether it is the latest one
 * App should be named following the format: <AppName>.<TypeOfApp>.<AuthorName>.<ProgrammingLanguage>
 * Example: ixnet_tsapi.plugin.fsgmhoward.php
 *
 * @version 3
 * @param string $appName
 * @param string $currentVersion
 * @param string $branch
 * @param string $apiAddress
 * @return array
 */
function ixnet_helpers_version($appName, $currentVersion, $branch = 'stable', $apiAddress = null)
{
    // By default, use the info panel API (version 3)
    $apiAddress = $apiAddress ?: 'https://info.ixnet.work/development/version/query';
    $queryString = "?api_version=3&program=$appName&current_branch=$branch&current_version=$currentVersion";
    // Retrieve data from the server
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiAddress.$queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = json_decode(curl_exec($ch), true);
    } else {
        $data = json_decode(file_get_contents($apiAddress.$queryString, false, stream_context_create(array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        ))), true);
    }
    if ($data['status'] != 200) {
        // Return raw data when error occurs at the server
        return $data;
    } else {
        return array(
            'status'         => 200,
            'isUpToDate'     => !$data['update_available'],
            'currentVersion' => $currentVersion,
            'remoteVersion'  => $data['latest'][$branch],
            'branch'         => $branch,
            'raw'            => $data
        );
    }
}
