<?php

namespace MoaAlaa\ApiResponder\Http\Resources;

use MoaAlaa\ApiResponder\Http\Resources\StanderApiResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponder
{
	public static $paginate = 10;
	
	private $statusCodes = [200, 201, 202];
	private $api;
	
	/**
	 * Return The Api For The Given Data
	 * 
	 * @param  object      $data
	 * @param  string|array|null $error 
	 * @param  int|integer $code
	 * 
	 * @return object
	 */
	public function api($data, $error = null, int $code = 200)
	{
		return $this->apiResourceCollection($data, $error, $code);	
	}

	/**
	 * Send An Error Response 
	 * Just Wrapper For $this->api() for more clear
	 * 
	 * @param  string|array|null $error
	 * @param  int    $code 
	 * 
	 * @return object
	 */
	public function error($error, int $code)
	{
		return $this->api(null, $error, $code);
	}

	/**
	 * Send An Safe Error Response 
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
	 * Validate The Incoming Request
	 * 
	 * @param  array  $attribute
	 * 
	 * @return mixed
	 */
	public function apiValidate(array $attribute)
	{
		return request()->validate($attribute);
	}

	/**
	 * Determine Whether Return A Resource, A Collection Or Error Response
	 * 
 	 * @param  object      $data
	 * @param  string|null $error 
	 * @param  int|integer $code
	 * 
	 * @return object
	 */
	private function apiResourceCollection($data, $error, $code)
	{
	    if (is_null($data) || (is_array($data) && count($data) <= 0)) {
			return response()->json([
				'data' => [], 
				'status' => $this->checkStatusCodes($code), 
				'code' => $code, 
				'messages' => $error 
			], $code);
		}

		if (is_object($data) && ($data instanceof Collection || $data instanceof LengthAwarePaginator)) {
			$this->api = $this->apiCollection($data);	
		} else {
			$this->api = $this->apiResource($data);
		}
		
		return $this->api->additional([ 
			'status' => $this->checkStatusCodes($code), 
			'code' => $code, 
			'messages' => $error 
		]);
	}

	/**
	 * Return A Collection Response
	 * 
	 * @param  object      $data
	 * 
	 * @return object
	 */
	private function apiCollection($data)
	{
		return StanderApiResource::collection($data);
	}

	/**
	 * Return A Resource Response
	 * 
	 * @param  object      $data
	 * 
	 * @return object
	 */
	private function apiResource($data)
	{
		return new StanderApiResource($data);
	}

    /**
	 * Determine If The Code Is Listed In Whitelist Or Not 
	 * 
	 * @param  int|integer $code
	 * 
	 * @return bool|boolean
	 */
	private function checkStatusCodes($code = 200)
	{
	    return in_array($code, $this->statusCodes);
	}
}
