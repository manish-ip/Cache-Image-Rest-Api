<?php
/**
 * Copyright 2016 iPragmatech. All rights reserved.
 */

namespace Ipragmatech\Imagepath\Api;

/**
 * Interface ImagePathInterface
 * @package Ipragmatech\Imagepath\Api
 */

interface ImagePathInterface
{
	/**
	 * add items to thre registry.
	 *
	 * @api
	 * @return array.
	 */
	public function getImagPath();
}