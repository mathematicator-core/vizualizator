<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Nette\SmartObject;

final class RenderRequest
{

	use SmartObject;

	/**
	 * @var Renderer
	 */
	private $renderer;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @var int[]
	 */
	private $lines = [];

	/**
	 * @param Renderer $renderer
	 * @param int $width
	 * @param int $height
	 */
	public function __construct(Renderer $renderer, int $width, int $height)
	{
		$this->renderer = $renderer;
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->render();
	}

	/**
	 * Smart shortcut.
	 *
	 * @param string $format
	 * @return string
	 */
	public function render(string $format = Renderer::FORMAT_PNG): string
	{
		return $this->renderer->render($this, $format);
	}

	/**
	 * @return int
	 */
	public function getWidth(): int
	{
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight(): int
	{
		return $this->height;
	}

	/**
	 * @return int[]
	 */
	public function getLines(): array
	{
		return $this->lines;
	}

	public function addLine(int $x, int $y, int $a, int $b, ?string $color = null): self
	{
		$this->lines[] = [$x, $y, $a, $b, $this->processColor($color)];

		return $this;
	}

	/**
	 * @param string|null $color
	 * @return int[]|null
	 */
	private function processColor(?string $color): ?array
	{
		if ($color === null) {
			return null;
		}

		$color = trim($color);

		static $names = [
			'black' => [0, 0, 0],
			'white' => [255, 255, 255],
			'red' => [255, 0, 0],
			'green' => [0, 255, 0],
			'blue' => [0, 0, 255],
		];

		if (isset($names[$color])) { // 1. Named color
			return $names[$color];
		}

		if (($color[0] ?? '') === '#' && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color, $hex)) { // HTML hex
			if (($match = strtolower($hex[1])) && isset($match[3]) === false) {
				$r = $match[0] . $match[0];
				$g = $match[1] . $match[1];
				$b = $match[2] . $match[2];
			} else {
				$r = $match[0] . $match[1];
				$g = $match[2] . $match[3];
				$b = $match[4] . $match[5];
			}

			return [hexdec($r), hexdec($g), hexdec($b)];
		}

		return null;
	}

}