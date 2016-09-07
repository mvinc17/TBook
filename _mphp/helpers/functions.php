<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/7/16
 * Time: 7:28 PM
 */

function need_params($func_name)
{
	$reflect = new ReflectionFunction($func_name);

	return !empty($reflect->getParameters());
}