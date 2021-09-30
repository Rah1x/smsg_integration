<?php
namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use App\Http\Helpers\curl_helper;

/**
 * Based on SMSGlobal APIs
**/
class SMSGlobal
{
    private $base_uri = 'api.smsglobal.com';
    private $base_url = 'https://api.smsglobal.com/v2/';
    private $user_cred = [];
    public $errorMsg = [];

    function __construct(Request $request)
    {
        $this->get_user_keys($request);
    }

    /** Check API Key of the user sending the request */
    private function get_user_keys(Request $request): bool
    {
        $smsglobal_key = $request->getUser();
        $smsglobal_secret = $request->getPassword();

        if(empty($smsglobal_key) || empty($smsglobal_secret)) {
            $this->errorMsg = ['Unable to determine API Key or Secret!', 400];
            return false;
        }

        $this->user_cred = [
            'smsglobal_key'=> $smsglobal_key,
            'smsglobal_secret'=> $smsglobal_secret,
        ];

        return true;
    }

    /** Generate the required MAC header by the sms global api
     * @param $method (GET, PATCH)
     * @return string
    */
    private function generate_auth_header($method): string
    {
        $ts_now = time();
        $nonce = Str::random(30);

        $key = $this->user_cred['smsglobal_secret'];

        $mac_arr = [$ts_now, $nonce, $method, '/v2/sms/', $this->base_uri, 443, ''];
        //$mac_arr = ['1325376000', 'random-string', 'POST', '/v2/sms/', 'api.smsglobal.com', 443, '']; //debug
        $mac_payload = sprintf("%s\n", implode("\n", $mac_arr));

        $mac = base64_encode(hash_hmac('sha256', $mac_payload, $key, true));
        //var_dump($mac); exit;

        $auth_header = "MAC id=\"{$this->user_cred['smsglobal_key']}\", ts=\"{$ts_now}\", nonce=\"{$nonce}\", mac=\"$mac\"";
        return $auth_header;
    }

    public function get_messages()
    {
        $auth_header = $this->generate_auth_header('GET');

        $pull_url = "{$this->base_url}sms";
        $data_ret = curl_helper::run_curl($pull_url, 'GET', ['Authorization: '.$auth_header]);
        var_dump($pull_url, $data_ret); exit;
    }


    /**
     * @param $POST
     * @return none
     */
    public function post_message($POST)
    {
        if(empty($POST['message']) || empty($POST['destination'])) {
            $this->errorMsg = ["Unable to locate Message or Destination in the payload!", 400];
            return false;
        }

        $post_payload = json_encode([
            'message'=> $POST['message'],
            'destination'=> $POST['destination'],
        ], JSON_FORCE_OBJECT);

        $req_hdrs = [
            'Authorization: '.$this->generate_auth_header('GET'),
            'Content-Type: application/json',
            'Accept application/json',
        ];

        $push_url = "{$this->base_url}sms";
        $data_ret = curl_helper::run_curl($push_url, 'POST', $req_hdrs, '', $post_payload);
        var_dump($push_url, $data_ret); exit;
    }
}