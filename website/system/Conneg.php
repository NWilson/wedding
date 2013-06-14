<?php
/*******************************************************************************
 * Provides a function to perform content negotiation
 *
 * Only perform MIME type negotiation. Accept-Charset is totally broken in
 * browsers (they prefer Latin-1 to UTF-8). Accept-Language I have no need of.
 *
 * Global input: (none)
 ******************************************************************************/

class conneg {

/**
 * We pass in an array of what we can send, with various qualities,
 * and match against what the client wants to send the best output.
 *
 * @param $possible An array with entries `<mime> => "0.x" | "1.0"`
 * @param $default  Either a string `<mime>` or an array {d1, d2}, where d1 is
 *                      sent if an accept all "∗/∗" header was sent, d2 else.
 *                      This is an ugly form of browser sniffing, because an
 *                      empty Accepts entry can be trusted, but IE's cannot.
 * @param $accepts  A third argument may be passed if matching is not being
 *                      done against $_SERVER['HTTP_ACCEPT'].
 */
static function negotiate($possible, $default) {
    //For reference, we need to at least work with these:
//     $webkit = 'text/xml,application/xml,application/xhtml+xml, text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
//     $firefox = 'text/html;application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
//     $ie = 'image/png,*/*';
//     $validator = '';

    if(func_num_args() > 2) $accepts = func_get_arg(2);
    else                    $accepts = 'Accept';
    $accepts_val = (string)@$_SERVER['HTTP_'.str_replace('-','_',strtoupper($accepts))];
    $split_accepts = explode(',', $accepts_val);
    $accepts_all_symbol = func_num_args() > 3 ? func_get_arg(3) : '*/*';

    foreach ($split_accepts as $accept) {
        preg_match('#(?P<mime>[^;]*)(?:;q=(?P<qual>.*))?#', $accept, $match);
        $matches[] = $match;
    }

    $accept_all = ($accepts_all_symbol == '') ? true : false;
    $mimes = array();
    foreach ($matches as $match) {
        $mime = trim($match['mime']);
        if (array_key_exists('qual', $match))
             $qual = sprintf('%03.1f', floatval(trim($match['qual'])));
        else $qual = '1.0';//default

        if (array_key_exists($mime, $possible) && $possible[$mime] != '0.0') {
            $mimes[$mime] = "$qual/{$possible[$mime]}";
            unset($possible[$mime]);
        }

        if ($mime == $accepts_all_symbol && $qual != '0.0') $accept_all = true;
    }

    //Now we have the correctly merged set of preferences in $mimes, containing
    //only the ones we can serve and the order of preference.
    arsort($mimes);
    reset($mimes);

    //Politely inform the client content negotiation was performed.
    header("Vary: $accepts", false);

    if (count($mimes) > 0)
        return key($mimes);
    if (trim($accepts_val) != '' && !$accept_all) {
        //An Accepts was sent, so negotiation is being attempted, but we are not
        //allowed to fall back.
        header('HTTP/1.0 406: Not Acceptable');
        return false;
    }
    if (is_string($default))
        return $default;
    if ($accept_all)
        return $default[0];
    return $default[1];
}

/**
   We can handle gzip output with the same system as mimes. We always have the gzip'ed output file from cached to use, and may also have the unencoded version to hand if we only just cached the page. We call the function with the encoded data filename which is output directly if possible.*/
static function gzoutput($unencoded) {
    if(!function_exists('gzencode')) {
        echo $unencoded;
        return;
    } //Else we can gzip, and will try.
    header('Vary: Accept-Encoding', false);

    $gzip = conneg::negotiate(
        array('gzip'=>'1.0', 'deflate'=>'0.5'), 'none', 'Accept-Encoding', ''
        );

    $etag = '"'.md5($unencoded).'-'.$gzip.'"';
    if (@$_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
        header('HTTP/1.0 304 Not Modified');
        exit(0);
    }
    header('ETag: '.$etag);

    switch($gzip) {
      case 'none':
        echo $unencoded;
        break;
      case 'gzip':
        header('Content-Encoding: gzip');
        echo gzencode($unencoded);
        break;
      case 'deflate':
        header('Content-Encoding: deflate');
        echo gzcompress($unencoded);
        break;
    }
}

}
