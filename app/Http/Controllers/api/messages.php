<?php
namespace App\Http\Controllers\api;

#/ Core
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;

#/ Abstract Parent
use App\Http\Controllers\apiAbstractController;

#/ Helper
use App\Http\Helpers\SMSGlobal;

/**
 * @OA\Post(
 *  path="api/v1/message",
 *  summary="Post Message via SMSGlobal",
 *  description="Post message via SMSGlobal API",
 *  tags={"Messages"},
 *
 *  @OA\Parameter(
 *      description="Your SMSGlobal REST API Key",
 *      in="header",
 *      name="API key",
 *      required=true
 *  ),
 *
 *  @OA\Parameter(
 *      description="Your SMSGlobal REST API Secret",
 *      in="header",
 *      name="API Secret",
 *      required=true
 *  ),
 *
 *  @OA\RequestBody(
 *    required=true,
 *    description="POST your message and destination number",
 *    @OA\JsonContent(
 *       required={"message", "destination"},
 *       @OA\Property(property="message", type="string", format="", example="Test Message"),
 *       @OA\Property(property="destination", type="string", format="", example="04xxxxxxxx"),
 *    ),
 *  ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Unable to determine API Key or Secret!",
 *  ),
 *  @OA\Response(
 *    response=400,
 *    description="Unable to locate Message or Destination in the payload!",
 *  ),
 *  @OA\Response(
 *    response=200,
 *    description="Your message has been dispatched successfully.",
 *  )
 * ),
 *
 * ------------------------------------------------------
 *
 * @OA\Get(
 *  path="api/v1/message",
 *  summary="Get list of sent Message via SMSGlobal",
 *  description="Get list of sent via SMSGlobal API",
 *  tags={"Messages"},
 *
 *  @OA\Parameter(
 *      description="Your SMSGlobal REST API Key",
 *      in="header",
 *      name="API key",
 *      required=true
 *  ),
 *
 *  @OA\Parameter(
 *      description="Your SMSGlobal REST API Secret",
 *      in="header",
 *      name="API Secret",
 *      required=true
 *  ),
 *
 *  @OA\Response(
 *    response=400,
 *    description="Unable to determine API Key or Secret!",
 *  ),
 *  @OA\Response(
 *    response=400,
 *    description="Unable to locate messages!",
 *  ),
 *  @OA\Response(
 *    response=200,
 *    description="List of messages",
 *  ),
 * )
 */

class messages extends apiAbstractController
{
    private $apiObj;

    function __construct(Request $request)
    {
        //die('1'); //test connection
        parent::__construct();

        $this->apiObj = new SMSGlobal($request);
    }

    /**
     * @param array $msg 2d array of [error, code]
     */
    private function error_return($msg)
    {
        return response()
        ->json(['error'=> $msg[0]], $msg[1])
        ->withHeaders([
            'Content-Type'=>'application/json',
        ]);
    }

    ///////////////////////////////////////////////////////////////// PUBLIC Methods below

    public function get()
    {
        #/ Check for initial errors from api
        if(!empty($this->apiObj->errorMsg))
        return $this->error_return($this->apiObj->errorMsg);

        #/ pull messages from api
        $messages_list = $this->apiObj->get_messages();
        var_dump($messages_list); exit;
    }

    public function post()
    {
        #/ Check for initial errors from api
        if(!empty($this->apiObj->errorMsg))
        return $this->error_return($this->apiObj->errorMsg);

        #/ send message
        $this->apiObj->post_message($this->POST);

        #/ check for error
        if(!empty($this->apiObj->errorMsg))
        return $this->error_return($this->apiObj->errorMsg);


        #/ return success
        return response()
        ->json(['success'=> 'Your message has been dispatched successfully.'], 200)
        ->withHeaders([
            'Content-Type'=>'application/json',
        ]);
    }
}
?>