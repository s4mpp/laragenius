<?php
namespace S4mpp\Laragenius\Contracts;

interface GeneratorInterface
{
	public function getNamespace();

	public function getFilename();
}