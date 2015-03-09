<?php

class SampleTest extends WP_UnitTestCase {

	function testSample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
	
	function test_sample_number() {

		$string = 'Unit tests for number';

		$this->assertEquals( 'Unit tests for number', $string );
		$this->assertNotEquals( 'Unit tests not number', $string );
	}
	
	function test_sample_string() {

		$string = 'Unit tests for string';

		$this->assertEquals( 'Unit tests for string', $string );
		$this->assertNotEquals( 'Unit tests not string', $string );
	}

}

