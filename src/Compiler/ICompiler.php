<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator\Compiler;


use Mathematicator\Vizualizator\RenderRequest;

interface ICompiler
{
	public function compile(RenderRequest $request): string;
}
