<?php

if (!function_exists('includeRouteFiles')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function includeRouteFiles($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

/**
 * get base dataTable request params
 * @param $request
 * @return array
 */
function getDataTableRequestParams($request)
{
    $start = $request->input('start');
    $length = $request->input('length');
    $draw = $request->input('draw');

    $order = $request->input('order');
    $columns = $request->input('columns');
    $search = $request->input('search');
    $keyword = $search['value'];

    if ($order) {
        $num = $order[0]['column'];
        $orderBy = $columns[$num]['data'];
        $orderType = $order[0]['dir'];

        return [
            'start' => $start,
            'length' => $length,
            'draw' => $draw,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'keyword' => $keyword
        ];
    } else

        return [
            'start' => $start,
            'length' => $length,
            'draw' => $draw,
            'orderBy' => null,
            'orderType' => null,
            'keyword' => $keyword
        ];
}

/**
 * @param $result
 * @param null $data
 * @return \Illuminate\Http\JsonResponse
 */
function processCommonResponse($result, $data = null)
{
    $code = null;
    if ($result === true) {
        $code = CODE_SUCCESS;
    } else if ($result === false) {
        $code = CODE_ERROR;
    } else if ($result === CODE_ERROR_DELETE_USER_WHEN_HAVE_USER_CREATED_AND_STUDENT_CODE) {
        $code = CODE_ERROR_DELETE_USER_WHEN_HAVE_USER_CREATED_AND_STUDENT_CODE;
    } else if ($result === CODE_ERROR_DELETE_STUDENT_CODE_WHEN_HAVE_STATUS_ACTIVE) {
        $code = CODE_ERROR_DELETE_STUDENT_CODE_WHEN_HAVE_STATUS_ACTIVE;
    } else if ($result === CODE_ERROR_DELETE_ACCOUNT_WHEN_DOMAIN_ASSIGNED_FOR_USER) {
        $code = CODE_ERROR_DELETE_ACCOUNT_WHEN_DOMAIN_ASSIGNED_FOR_USER;
    } else {
        $code = CODE_SUCCESS;
    }

    return response()->json(array(
        'code' => $code,
        'message' => $result ? MESSAGE_SUCCESS : MESSAGE_ERROR,
        'data' => $data
    ));
}

function fireEventActionLog($action_name = null, $class_name = null, $object_id = null, $object_name = null, $old_value = null, $new_value = null)
{
    $data = [
        'user_id' => \Illuminate\Support\Facades\Auth::user()->id,
        'username' => \Illuminate\Support\Facades\Auth::user()->name,
        'action_name' => $action_name,
        'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        'class_name' => $class_name,
        'object_id' => $object_id,
        'object_name' => $object_name,
        'old_value' => $old_value,
        'new_value' => $new_value
    ];
    event(new \App\Events\ActionLog($data));
}

/**
 * @param $url
 * @param array $params
 * @param string $method
 * @param bool $isJSON
 * @param string $authenToken
 * @return string
 */
function sendRequest($url, $params = array(), $authenToken = '', $method = 'POST', $isJSON = false)
{
    $request = \Ixudra\Curl\Facades\Curl::to($url)
        ->withData($params)
        ->withOption('TIMEOUT', 3000)
        ->withOption('CONNECTTIMEOUT', 0)
        ->withOption('SSL_VERIFYPEER', 0)
        ->withOption('FOLLOWLOCATION', true)
        ->returnResponseObject();

    if ($isJSON) {
        $request->asJsonRequest();
    }

    if ($authenToken != '') {
        $request->withHeader('Authorization: Bearer ' . $authenToken);
    }

    $response = '';

    switch ($method) {
        case 'GET':
            $response = $request->get();
            break;
        case 'POST':
            $response = $request->post();
            break;
        case 'PUT':
            $response = $request->put();
            break;
        case 'PATCH':
            $response = $request->patch();
            break;
        case 'DELETE':
            $response = $request->delete();
            break;
        default:
            break;
    }
    if ($response->status == 200 || $response->status == '201 Created' || $response->status == '204 No Content') {
        return $response->content;
    }

    return RESPONSE_ERROR;
}
