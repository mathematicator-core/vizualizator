<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


class SvgCompiler implements Compiler
{

	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string
	{
		$return = '';

		foreach ($request->getLines() as $line) {
			$return .= $this->renderLine($line);
		}

		return '<svg width="' . $request->getWidth() . '" height="' . $request->getHeight() . '">' . $return . '</svg>';
	}

	/**
	 * @param string $name
	 * @param string[] $params
	 * @return string
	 */
	private function renderElement(string $name, array $params): string
	{
		$arguments = [];

		foreach ($params as $key => $value) {
			$arguments[] = $key . '="' . $value . '"';
		}

		return '<' . $name . ' ' . implode(' ', $arguments) . ' />';
	}

	/**
	 * @param int[]|null $params
	 * @return string
	 */
	private function getColor(?array $params): string
	{
		if ($params === null) {
			return 'rgb(0,0,0)';
		}

		return 'rgb(' . $params[0] . ',' . $params[1] . ',' . $params[2] . ')';
	}

	private function renderLine(array $line): string
	{
		return $this->renderElement('line', [
			'x1' => $line[0],
			'y1' => $line[1],
			'x2' => $line[2],
			'y2' => $line[3],
			'style' => 'stroke:' . $this->getColor($line[4] ?? null) . ';stroke-width:1',
		]);
	}

}