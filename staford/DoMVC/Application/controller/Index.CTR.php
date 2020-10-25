<?php

class IndexCTR extends Controller
{
    function index()
    {
        Mcrypt::set_config('1234567890ytfccgvjn');
        $mcrypt = new Mcrypt;
        $str = $mcrypt->encrypt('草泥54defer48e44hg8th4马',9);
        dump($str);
        dump($mcrypt->decrypt($str, 9));
    }
}


?>