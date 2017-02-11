<?php

class M_Ck_Api_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'M_Ck_Api') );
	}

	function test_class_access() {
		$this->assertTrue( mtollwc()->ck-api instanceof M_CK_API );
	}
}
