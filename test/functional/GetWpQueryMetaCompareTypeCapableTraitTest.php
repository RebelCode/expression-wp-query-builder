<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\GetWpQueryMetaCompareTypeCapableTrait}.
 *
 * @since [*next-version*]
 */
class GetWpQueryMetaCompareTypeCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\GetWpQueryMetaCompareTypeCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods Optional additional mock methods.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance(array $methods = [])
    {
        $builder = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                        ->setMethods(
                            array_merge(
                                $methods,
                                [
                                    '_getWpQueryMetaCompareValue'
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();

        return $mock;
    }

    /**
     * Creates a mock logical expression instance.
     *
     * @since [*next-version*]
     *
     * @param string $type     The expression type.
     * @param array  $terms    Optional expression terms.
     * @param bool   $negation Optional negation flag. True to negate the expression, false otherwise.
     *
     * @return LogicalExpressionInterface The created mock instance.
     */
    public function createLogicalExpression($type, $terms = [], $negation = false)
    {
        return $this->mock('Dhii\Expression\LogicalExpressionInterface')
                    ->getType($type)
                    ->getTerms($terms)
                    ->isNegated($negation)
                    ->new();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'An instance of the test subject could not be created'
        );
    }

    /**
     * Tests the meta compare type method with an integer meta compare value to assert whether the retrieved compare
     * type is "NUMERIC".
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareTypeInt()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('');

        $subject->method('_getWpQueryMetaCompareValue')->willReturn(9);

        $this->assertEquals('NUMERIC', $reflect->_getWpQueryMetaCompareType($expression));
    }

    /**
     * Tests the meta compare type method with a double/float meta compare value to assert whether the retrieved compare
     * type is "DECIMAL".
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareTypeDouble()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('');

        $subject->method('_getWpQueryMetaCompareValue')->willReturn(15.08);

        $this->assertEquals('DECIMAL', $reflect->_getWpQueryMetaCompareType($expression));
    }

    /**
     * Tests the meta compare type method with a string meta compare value to assert whether the retrieved compare
     * type is "CHAR".
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareTypeString()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('');

        $subject->method('_getWpQueryMetaCompareValue')->willReturn(uniqid('test-'));

        $this->assertEquals('CHAR', $reflect->_getWpQueryMetaCompareType($expression));
    }

    /**
     * Tests the meta compare type method with a boolean meta compare value to assert whether the retrieved compare
     * type is "BINARY".
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareTypeBool()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('');

        $subject->method('_getWpQueryMetaCompareValue')->willReturn(false);

        $this->assertEquals('BINARY', $reflect->_getWpQueryMetaCompareType($expression));
    }

    /**
     * Tests the meta compare type method with a misc meta compare value to assert whether the retrieved compare
     * type is the default "CHAR".
     *
     * @since [*next-version*]
     */
    public function testGetWpQueryMetaCompareTypeMisc()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('');

        $subject->method('_getWpQueryMetaCompareValue')->willReturn(new \stdClass());

        $this->assertEquals('CHAR', $reflect->_getWpQueryMetaCompareType($expression));
    }
}
