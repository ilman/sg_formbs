<?php

use Scienceguard\SG_FormBs;

class SG_FormBsTest extends PHPUnit_Framework_TestCase {

	public function testField()
	{
		$output = SG_FormBs::field('text', 'name', 'value', $attr = array(), 'default', $options = array());

		$is_true = ($output) ? true : false;

		$this->assertTrue($is_true);
	}

}