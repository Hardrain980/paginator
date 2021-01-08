<?php

use \Leo\Paginator;
use \PHPUnit\Framework\TestCase;

/**
 * @testdox \Leo\Paginator
 */
class PaginatorTest extends TestCase
{
	private Paginator $p;

	public function setUp():void
	{
		$this->p = new Paginator(2);
	}

	public function testRejectInvalidNeighbourCount():void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageMatches(
			'/^Invalid count of neighbour pages/'
		);

		new Paginator(-1);
	}

	public function testRejectInvalidTotalPages():void
	{
		$this->expectException(RangeException::class);
		($this->p)(1, -1);
	}

	/**
	 * @depends PaginatorTest::testNoPage
	 * @depends PaginatorTest::testSinglePage
	 */
	public function testRejectInvalidPage():void
	{
		$this->expectException(RangeException::class);
		($this->p)(100, 10);
	}

	public function testNoPage():void
	{
		$this->assertSame([], ($this->p)(0, 0));
	}

	public function testSinglePage():void
	{
		$this->assertSame([], ($this->p)(1, 1));
	}

	public function testCenterOfLongSlice():void
	{
		$this->assertSame(
			[1, -1, 48, 49, 50, 51, 52, -1, 100],
			($this->p)(50, 100)
		);
	}

	public function testOmittableCollapsedPage():void
	{
		$this->assertSame(
			[1, 2, 3, 4, 5, 6, -1, 20],
			($this->p)(4, 20)
		);
	}

	public function testReplaceableCollapsedPage():void
	{
		$this->assertSame(
			[1, 2, 3, 4, 5, 6, 7, -1, 20],
			($this->p)(5, 20)
		);
	}
}

?>
