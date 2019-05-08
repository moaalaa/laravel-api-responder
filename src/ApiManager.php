<?php

namespace MoaAlaa\ApiResponder;

use MoaAlaa\ApiResponder\Http\Resources\StanderApiResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiManager
{

    private $paginateLimit = 10;
    private $statusCodes = [200, 201, 202];
    private $apiContainer;
    private $wrapping;
    private $additional = [];

    /**
	 * Return The Api For The Given Data.
	 *
	 * @example $this->response(...)
	 * 
	 * @param  mixed $data
	 * @param  string|array|null $error 
	 * @param  int|integer $code
	 * @param  array $additional
	 * @param  string $wrap
	 * 
	 * @return object
	 */
    public function response($data, $error = null, int $code = 200, $additional = [], $wrap = 'payload')
    {
        // Change Laravel Wrapper Key Name Default Is "data" 
        StanderApiResource::$wrap = $this->wrapping ?? $wrap;

        if (is_callable($additional)) {
            $additional = $additional();
        }

        return $this->apiResourceCollection($data, $error, $code, $additional);
    }

    /**
	 * Wrap "$this->response" function.
	 *
	 * @example $this->responseWith($data)
	 * 
	 * @param  mixed $data
	 * @param  string|array|null $error 
	 * @param  int|integer $code
	 * @param  array $additional
	 * @param  string $wrap
	 * 
	 * @return object
	 */
    public function responseWith($data, $error = null, int $code = 200, $additional = [], $wrap = 'payload')
    {
        return $this->response($data, $error, $code, $additional, $wrap);
    }

    /**
	 * Send An Error Response.
	 * Just Wrapper For "$this->response()" for more clear.
	 * 
	 * @example $this->error($error)
	 * 
	 * @param  string|array|null $error
	 * @param  int    $code 
	 * 
	 * @return object
	 */
    public function error($error, int $code)
    {
        return $this->response(null, $error, $code);
    }

    /**
	 * Send a Safe Error Response "When Using Try Catch Blocks".
	 * 
     * @example $this->SafeError($exception)
     * 
     * @param  \Exception $exception
	 * @param  int    $code 
     * 
	 * @return object
	 */
    public function safeError($exception, $code = 500)
    {
        $message = '';

        if (method_exists($exception, 'errors')) {
            $message = $exception->errors();
        } else if (method_exists($exception, 'getMessage')) {
            $message = $exception->getMessage();
        }

        return $this->error($message, $code);
    }

    /**
	 * Validate The Incoming Request.
	 *
     * @example $this->apiValidate(...roles...)
     *  
	 * @param  array  $roles
	 * 
	 * @return array
	 */
    public function validate(array $roles)
    {
        return request()->validate($roles);
    }

    /**
	 * Set The Pagination Limit.
	 *
     * @example $this->setPaginationLimit(...)
     * 
	 * @param int|integer $limit
     * 
	 * @return void
	 */
    public function setPaginationLimit(int $limit)
    {
        $this->paginateLimit = $limit;

        return $this;
    }

    /**
	 * Get The Pagination Limit.
	 *
     * @example $this->getPaginationLimit()
     * 
	 * @return int|integer
	 */
    public function getPaginationLimit()
    {
        return $this->paginateLimit;
    }

    /**
	 * Append Additional Data To Response.
	 *
	 * @example $this->with(['key' => 'val'])->response(...)
	 * @example $this->withKey()->response(...)
	 * 
	 * @param array $data
     * 
	 * @return $this
	 */
    public function with(array $data)
    {
        $this->additional = array_merge($this->additional, $data);

        return $this;
    }

    /**
	 * Change The Wrapping Of The Response.
	 *
	 * @example $this->wrapping('data')->response(...)
	 * 
	 * @param string $wrapping
	 * 
     * @return $this
	 */
    public function wrapping(string $wrapping)
    {
        $this->wrapping = $wrapping;

        return $this;
    }

    /**
	 * Change The Wrapping Of The Response.
     * Just An Alias For "$this->wrapping()"
	 *
	 * @example $this->setWrapping('data')->response(...)
	 * 
	 * @param string $wrapping
	 * 
     * @return $this
	 */
    public function setWrapping(string $wrapping)
    {
        return $this->wrapping($wrapping);
    }

    /**
	 * Get The Wrapping Of The Response.
	 *
	 * @example $this->getWrapping()
	 * 
	 * @return string
	 */
    public function getWrapping()
    {
        return $this->wrapping;
    }

    /**
	 * Determine Whether Return A Resource, A Collection Or Error Response.
	 * 
 	 * @param  mixed|null      $data
	 * @param  string|null $error 
	 * @param  int|integer $code
	 * @param  array $additional
	 * 
	 * @return object
	 */
    private function apiResourceCollection($data, $error, $code, $additional)
    {
        if (is_null($data) || (is_array($data) && count($data) <= 0)) {
            return response()->json([
                StanderApiResource::$wrap => [],
                'status' => $this->checkStatusCodes($code),
                'code' => $code,
                'messages' => $error
            ], $code);
        }

        if (is_object($data) && ($data instanceof Collection || $data instanceof LengthAwarePaginator)) {
            $this->apiContainer = $this->apiCollection($data);
        } else {
            $this->apiContainer = $this->apiResource($this->getProperDataType($data));
        }

        $additional = array_merge(['status' => $this->checkStatusCodes($code), 'code' => $code, 'messages' => $error], $additional, $this->additional);

        return $this->apiContainer->additional($additional)->response()->setStatusCode($code);
    }

    /**
	 * Return A Collection Response.
	 * 
	 * @param  mixed      $data
	 * 
	 * @return object
	 */
    private function apiCollection($data)
    {
        return StanderApiResource::collection($data);
    }

    /**
	 * Return A Resource Response.
	 * 
	 * @param  mixed      $data
	 * 
	 * @return object
	 */
    private function apiResource($data)
    {
        return new StanderApiResource($data);
    }

    /**
	 * Determine If The Code Is Listed In Whitelist Or Not.
	 * 
	 * @param  int|integer $code
	 * 
	 * @return bool|boolean
	 */
    private function checkStatusCodes($code = 200)
    {
        return in_array($code, $this->statusCodes);
    }

    /**
	 * Get Proper DataType For Return Response.
	 * EX: If "$data" is "String" It Will Convert To "Collection"
	 *  EX: If "$data" is "Array" It Will Send As It Is
	 *
	 * @param mixed $data
     * 
	 * @return mixed
	 */
    private function getProperDataType($data)
    {
        if (!is_array($data)) {
            return collect($data);
        }

        return $data;
    }

    public function __call($method, $params)
    {
        $methodInfo = preg_split("/([A-Z])/", $method, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $method = $methodInfo[0];

        if ($method == 'with') {
            // Call With Method Dynamically
            /*
				->"withDynamic"
				=>with(['dynamic'])
			*/
            $keyName = strtolower($methodInfo[1] . $methodInfo[2]);

            if (count($params) > 0 && count($params) == 1) {
                $params = $params[0];
            }

            return $this->{$method}([$keyName => $params]);
        }
    }
}
