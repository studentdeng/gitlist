<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function common_confirmation_code($bits)
    {
        // 36 alphanums - lookalikes (0, O, 1, I) = 32 chars = 5 bits
        static $codechars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $chars = ceil($bits/5);
        $code = '';
        for ($i = 0; $i < $chars; $i++) {
            // XXX: convert to string and back
            $num = hexdec(common_good_rand(1));
            // XXX: randomness is too precious to throw away almost
            // 40% of the bits we get!
            $code .= $codechars[$num%32];
        }
        return $code;
    }
    
    /**
 * returns $bytes bytes of random data as a hexadecimal string
 * "good" here is a goal and not a guarantee
 */
function common_good_rand($bytes)
{
    // XXX: use random.org...?
    if (@file_exists('/dev/urandom')) {
        return common_urandom($bytes);
    } else { // FIXME: this is probably not good enough
        return common_mtrand($bytes);
    }
}

function common_urandom($bytes)
{
    $h = fopen('/dev/urandom', 'rb');
    // should not block
    $src = fread($h, $bytes);
    fclose($h);
    $enc = '';
    for ($i = 0; $i < $bytes; $i++) {
        $enc .= sprintf("%02x", (ord($src[$i])));
    }
    return $enc;
}

function common_mtrand($bytes)
{
    $enc = '';
    for ($i = 0; $i < $bytes; $i++) {
        $enc .= sprintf("%02x", mt_rand(0, 255));
    }
    return $enc;
}
