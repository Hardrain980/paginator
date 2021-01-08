<?php

namespace Leo;

class Paginator
{
	public const COLLAPSED = -1;

	/**
	 * @var int Number of neighbour pages
	 */
	private int $neighbours;

	public function __construct(int $neighbours = 3)
	{
		// Reject negative neighbours count
		if ($neighbours < 0)
			throw new \InvalidArgumentException("Invalid count of neighbour pages \"{$neighbours}\", >= 0 expected.");

		$this->neighbours = $neighbours;
	}

	public function __invoke(int $page, int $pages):array
	{
		if ($pages < 0)
			throw new \RangeException("Pages is expected to be >= 0");

		// Special consideration for no page or single page.
		if ($pages == 0 || $pages == 1)
			return [];

		// If there are pages, 1 <= $page <= $pages applies.
		if ($pages < 1 || $page > $pages)
			throw new \RangeException("Page should be in range of [1, pages]");

		$slice = [];

		// Generate the central slice, 
		// make sure all items clamped within [0 ,$pages]
		$start = $page - $this->neighbours;
		$end   = $page + $this->neighbours;

		for ($i = $start; $i <= $end; $i++)
			if ($i >= 1 && $i <= $pages)
				$slice[] = $i;

		// If slice does not contain first page or last page ...
		if (!in_array(1, $slice, true))
			$slice = [...[1, self::COLLAPSED], ...$slice];

		if (!in_array($pages, $slice, true))
			$slice = [...$slice, ...[self::COLLAPSED, $pages]];

		// Remove unreasonable dots
		// e.g. 1-...-2-3-4-5-6 => 1-2-3-4-5-6
		// or   1-...-3-4-5-6-7 => 1-2-3-4-5-6-7
		foreach ($slice as $i => $p) {
			if ($p == self::COLLAPSED && $slice[$i - 1] + 1 == $slice[$i + 1])
				unset($slice[$i]);

			if ($p == self::COLLAPSED && $slice[$i - 1] + 2 == $slice[$i + 1])
				$slice[$i] = $slice[$i - 1] + 1;
		}

		return array_values($slice);
	}
}

?>
