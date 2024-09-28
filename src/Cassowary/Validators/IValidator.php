<?php

namespace Raven\Cassowary\Validators;

/**
 * @template T
 *
 **/

interface IValidator
{
	/*
	* @param T $value
	**/
	public function validate(string $propertyName, $value): bool;
}
