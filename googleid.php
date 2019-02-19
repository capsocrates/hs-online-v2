<?php

/* Thu Apr 26 01:08:53 PDT 2012 by epixoip
 * _THE_ simplest Google Federated Login class for PHP _WITH_ validation.
 *
 * Cobbled together from several other "simple" classes in the public domain
 * which just plain didn't work and looked like shit.
 *
 * _USAGE_	
 *
 * On the login page, you just need a few lines:
 *
 * <?php
 *    $openid = new GoogleOpenID;
 *    $openid->login('test_return.php');
 *    exit;
 * ?>
 *
 * Where 'test_return.php' is the relative or absolute URL of the page Google should
 * redirect the user to after authenticating.
 *
 * And on the return page, you just need a few more lines:
 *
 * <?php
 *    $openid = new GoogleOpenID;
 *    if ($openid->is_valid())
 *      print 'logged in as ' . $_GET['openid_ext1_value_email'];
 *    else
 *      print 'Failed validation.';
 * ?>
 *
 * That's all there is to it.
 */

class GoogleOpenID
{
    var $openid_server = 'https://www.google.com/accounts/o8/ud';
    var $openid_url_identity = 'http://specs.openid.net/auth/2.0/identifier_select';
    var $realm;
    var $return_to;


    function split_response($response)
    {
        $r = array();
        $response = explode("\n", $response);

        foreach($response as $line)
        {
            $line = trim($line);

            if (isset($line))
            {
                @list ($key, $value) = explode(":", $line, 2);
                $r[trim($key)] = trim($value);
            }
        }

        return $r;
    }


    function array2url($array)
    {
        if (!is_array($array))
            return false;

        $query = '';

        foreach($array as $key => $value)
            $query .= $key . '=' . $value . '&';

        return $query;
    }


    function request($url, $method = 'GET', $params = '')
    {
        if (is_array($params))
            $params = $this->array2url($params);

        if ($method === 'GET' && isset($params))
        {
            if (stripos($url, '?'))
                $url .= '&' . $params;
            else
                $url .= '?' . $params;
        }

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPGET, ($method === 'GET'));
        curl_setopt($curl, CURLOPT_POST, ($method === 'POST'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST')
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($curl);

        if (curl_errno($curl) === 0)
            $response;

        return $response;
    }


    function login($return_to)
    {
        if (!function_exists('curl_exec'))
            die('this class depends on cURL.');

        $this->realm = 'http://' . $_SERVER['SERVER_NAME'] . '/';

        if (!stripos($return_to, "://"))
        {
            if (substr($return_to, 0, 1) === '/')
                $return_to = substr($return_to, 1);

            $server_name_pos = stripos($return_to, $_SERVER['SERVER_NAME']);

            if ($server_name_pos === 0)
                $return_to = 'http://' . $return_to;
            else
                $return_to = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $return_to;
        }

        $this->return_to = $return_to;

        $params = array(
                'openid.ns' => 'http://specs.openid.net/auth/2.0',
                'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
                'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
                'openid.return_to' => urlencode($this->return_to),
                'openid.realm' => urlencode($this->realm),
                'openid.mode' => 'checkid_setup',
                'openid.ns.ext1' => 'http://openid.net/srv/ax/1.0',
                'openid.ext1.mode' => 'fetch_request',
                'openid.ext1.type.email' => 'http://schema.openid.net/contact/email',
                'openid.ext1.required' => 'email'
        );

        $redirect_to = $this->openid_server . '?' . $this->array2url($params);

        if (headers_sent())
            print '<script type="text/javascript">window.location=\'' . $redirect_to . '\';</script>';
        else
            header('Location: ' . $redirect_to);
    }


    function is_valid()
    {
        $params = array(
                'openid.ns' => 'http://specs.openid.net/auth/2.0',
                'openid.mode' => 'check_authentication',
                'openid.op_endpoint' => urlencode($_GET['openid_op_endpoint']),
                'openid.response_nonce' => urlencode($_GET['openid_response_nonce']),
                'openid.return_to' => urlencode($_GET['openid_return_to']),
                'openid.assoc_handle' => urlencode($_GET['openid_assoc_handle']),
                'openid.signed' => urlencode($_GET['openid_signed']),
                'openid.sig' => urlencode($_GET['openid_sig']),
                'openid.identity' => urlencode($_GET['openid_identity']),
                'openid.claimed_id' => urlencode($_GET['openid_claimed_id']),
                'openid.ns.ext1' => urlencode($_GET['openid_ns_ext1']),
                'openid.ext1.mode' => urlencode($_GET['openid_ext1_mode']),
                'openid.ext1.type.email' => urlencode($_GET['openid_ext1_type_email']),
                'openid.ext1.value.email' => urlencode($_GET['openid_ext1_value_email'])
        );

        /* debug:
        print '<pre>GET';
        print_r($_GET);
        print "\nPARAMS";
        print_r($params);
        print "\n</pre>";
        */

        if ($_GET['openid_identity'] !== $_GET['openid_claimed_id'])
            return false;

        $response = $this->request($this->openid_server, 'POST', $params);
        $data = $this->split_response($response);

        /* debug:
        print '<pre>RESPONSE';
        print_r($data);
        print '</pre>';
        */

        if ($data['is_valid'] === 'true')
            return true;

        return false;
    }
}

?>