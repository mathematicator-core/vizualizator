<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


final class RenderRequest
{
	private Renderer $renderer;

	private int $width;

	private int $height;

	private ?string $title = null;

	/** @var mixed[] */
	private array $border = [
		'size' => 0,
		'color' => 'black',
	];

	/** @var int[]|int[][]|int[][][]|null[] */
	private array $lines = [];


	public function __construct(Renderer $renderer, int $width, int $height)
	{
		if ($width < 1) {
			$width = 1;
		}

		if ($height < 1) {
			$height = 1;
		}

		$this->renderer = $renderer;
		$this->width = $width;
		$this->height = $height;
	}


	public function __toString(): string
	{
		return $this->render();
	}


	public function render(string $format = Renderer::FORMAT_PNG): string
	{
		return $this->renderer->render($this, $format);
	}


	public function getSerialized(): string
	{
		return (string) json_encode([
			'width' => $this->width,
			'height' => $this->height,
			'title' => $this->title,
			'lines' => $this->getLines(),
		], JSON_THROW_ON_ERROR);
	}


	public function getWidth(): int
	{
		return $this->width;
	}


	public function getHeight(): int
	{
		return $this->height;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function setTitle(?string $title): self
	{
		$this->title = $title ?: null;

		return $this;
	}


	/**
	 * @return mixed[]
	 */
	public function getBorder(): array
	{
		return $this->border;
	}


	public function setBorder(?int $size = null, ?string $color = null): self
	{
		if ($size !== null && $size < 0) {
			$size = 0;
		}

		$this->border = [
			'size' => $size ?? 0,
			'color' => $color ?? 'black',
		];

		return $this;
	}


	/**
	 * @return null[][]|int[][][]
	 */
	public function getLines(): array
	{
		$lines = $this->lines;

		if ($this->border['size'] > 0) {
			$color = $this->processColor($this->border['color']);
			$lines[] = [ // top
				'x' => 1,
				'y' => 1,
				'a' => $this->width - 1,
				'b' => 1,
				'color' => $color,
			];
			$lines[] = [ // left
				'x' => 1,
				'y' => 1,
				'a' => 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
			$lines[] = [ // right
				'x' => $this->width - 1,
				'y' => 1,
				'a' => $this->width - 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
			$lines[] = [ // bottom
				'x' => 1,
				'y' => $this->height - 1,
				'a' => $this->width - 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
		}

		/** @phpstan-ignore-next-line */
		return $lines;
	}


	public function addLine(int $x, int $y, int $a, int $b, ?string $color = null): self
	{
		/** @phpstan-ignore-next-line */
		$this->lines[] = [
			'x' => $x,
			'y' => $y,
			'a' => $a,
			'b' => $b,
			'color' => $this->processColor($color),
		];

		return $this;
	}


	/**
	 * @return int[]|null
	 */
	private function processColor(?string $color): ?array
	{
		if ($color === null) {
			return null;
		}

		static $names = [
			'black' => [0, 0, 0],
			'white' => [255, 255, 255],
			'red' => [255, 0, 0],
			'green' => [0, 255, 0],
			'blue' => [0, 0, 255],
		];

		if (isset($names[$color = trim($color)])) { // 1. Named color
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

			return [(int) hexdec($r), (int) hexdec($g), (int) hexdec($b)];
		}

		return null;
	}
}
