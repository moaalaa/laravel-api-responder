<?php

namespace MoaAlaa\ApiResponder;

trait ApiResponder
{
	/**
	 * Return The ApiManager Instance For More Readability
	 * When Using "ApiResponder" Apis Methods And Properties
	 * 
	 * @example $this->api()->response(...)
	 * @example $this->api()->with(...)->response(...)
	 * 
	 * @return $this
	 */
	public function api()
	{
		return resolve(ApiManager::class);
	}
}
