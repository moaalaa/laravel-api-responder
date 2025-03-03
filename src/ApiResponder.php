<?php

namespace MoaAlaa\ApiResponder;

trait ApiResponder
{
	/**
	 * Return The ApiManager Instance For More Readability
	 * When Using "ApiResponder" Apis Methods And Properties
	 * 
	 * @example - $this->api()->response(...)
	 * @example - $this->api()->with(...)->response(...)
	 * 
	 * @return ApiManager
	 */
	public function api(): ApiManager
	{
		return new ApiManager;
	}
}
