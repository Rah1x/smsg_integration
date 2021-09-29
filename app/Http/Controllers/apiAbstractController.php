<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *    title="SMSGlobal Intgration Testing via API",
 *    version="1.0.0",
 * )
 */

abstract class apiAbstractController extends Controller {

    function __construct()
    {
        parent::__construct();
    }
}