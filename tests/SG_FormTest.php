<?php

use Scienceguard\SG_Form;

class SG_FormTest extends PHPUnit_Framework_TestCase {

	public function testField()
	{
		$output = SG_Form::field('text', 'name', 'value', $attr = array(), 'default', $options = array());

		$is_true = ($output) ? true : false;

		$this->assertTrue($is_true);
	}

}