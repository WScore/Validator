<?php
namespace wsTests\Validation;

//require_once( __DIR__ . '/../../autoloader.php' );
use \WScore\Validation\Rules;

class Rules_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Rules */
    var $rule;

    public function setUp()
    {
        require_once( __DIR__ . '/../../../../scripts/require.php' );
        $this->rule = new Rules();
    }
    // +----------------------------------------------------------------------+
    function test_merge_filter()
    {
        $this->rule->mergeFilter( 'test:test 1st' );
        $filter = $this->rule->filter;
        $this->assertArrayHasKey( 'test', $filter );
        $this->assertEquals( 'test 1st', $filter[ 'test' ] );
    }
    function test_mail_type()
    {
        $rule2 = $this->rule->mail( 'test:test 1st' );
        
        // mail should return new rule object.
        $this->assertNotSame( $this->rule, $rule2 );
        
        // new filters. 
        $filter = $rule2->filter;
        $this->assertArrayHasKey( 'test', $filter );
        $this->assertEquals( 'test 1st', $filter[ 'test' ] );

        // email filter have sanitize for email. 
        $this->assertArrayHasKey( 'sanitize', $filter );
        $this->assertEquals( 'mail', $filter[ 'sanitize' ] );
        
        // type is email. 
        $this->assertEquals( 'mail', $rule2->getType() );
    }
    function test_date_type()
    {
        // currently, date type should use __call method. 
        $rule2 = $this->rule->date( 'test:test 1st' );

        // mail should return new rule object.
        $this->assertNotSame( $this->rule, $rule2 );

        // new filters. 
        $filter = $rule2->filter;
        $this->assertArrayHasKey( 'test', $filter );
        $this->assertEquals( 'test 1st', $filter[ 'test' ] );

        // date should have multiple type, date.
        $this->assertArrayHasKey( 'multiple', $filter );
        $this->assertEquals( 'YMD', $filter[ 'multiple' ] );
        
        // email filter have sanitize original value, false. 
        $this->assertArrayHasKey( 'sanitize', $filter );
        $this->assertFalse( $filter[ 'sanitize' ] );

        // type is date. 
        $this->assertEquals( 'date', $rule2->getType() );
    }
    function test_separated()
    {
        $rule1 = $this->rule->start( 'text | required | string:lower' );
        $this->assertEquals( 'text', $rule1->type );
        $this->assertEquals( true, $rule1->getFilters( 'required' ) );
        $this->assertEquals( false, $rule1->getPattern() );
        $this->assertEquals( 'lower', $rule1->getFilters( 'string' ) );
    }
}