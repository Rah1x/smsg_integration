<?php
namespace App\Http\Helpers;

class curl_helper
{
    /**
     * @param $call_url
     * @param $method HTTP method (GET, PATCH, POST, PUT, DELETE)
     * @param $headers http request headers
     * @param $post_data
     * @return array
     */
    public static function curl_process($call_url, $method, $headers, $post_data)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $call_url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 180);
        curl_setopt($handle, CURLOPT_TIMEOUT, 180);

        switch ( strtoupper($method) )
        {
            case 'PATCH':
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
            break;

            case 'POST':
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
            break;

            case 'PUT':
            curl_setopt($handle, CURLOPT_PUT, true);
            $file_handle = fopen($post_data, 'r');
            curl_setopt($handle, CURLOPT_INFILE, $file_handle);
            break;

            case 'DELETE':
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
        }

        $response = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return [
            'http_code' => $code,
            'response_payload' => $response,
        ];
    }

    /**
     * @param $call_url
     * @param $method HTTP method (GET, PATCH, POST, PUT, DELETE). default=GET
     * @param $custom_headers
     * @param $return_type (e.g. application/json)
     * @param $post_payload
     * @return array, string (depending upon $return_type)
     */
    public static function run_curl($call_url, $method='GET', $custom_headers=[], $return_type='', $post_payload=[])
    {
        #/ Setup URL & Call
        $call_url_comp = @parse_url($call_url);
        if(empty($call_url) || !is_array($call_url_comp) || !isset($call_url_comp['path'])){return false;}

        #/ Setup headers
        $header = [
            "GET {$call_url_comp['path']} HTTP/1.1",
            "Host: {$call_url_comp['host']}",
            "Cache-Control: no-cache",
        ];

        if(!empty($custom_headers)) {
            $header = array_merge($header, $custom_headers);
        }

        #/ Set POST data
        $post_data = (!empty($post_payload) && is_array($post_payload))? @http_build_query($post_payload): $post_payload;


        #/ Pull data
        $res_pulled = self::curl_process($call_url, $method, $header, $post_data);

        if($return_type=='application/json')
        return @json_decode($res_pulled['response_payload'], true);

        return $res_pulled['response_payload'];
    }
}