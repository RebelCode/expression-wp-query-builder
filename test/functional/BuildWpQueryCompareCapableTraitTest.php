<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\RelationalTypeInterface;
use OutOfRangeException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\BuildWpQueryCompareCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryCompareCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryCompareCapableTrait';

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
                                    '_getWpQueryCompareKey',
                                    '_getWpQueryCompareValue',
                                    '_normalizeString',
                                    '_createOutOfRangeException',
                                    '__',
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createOutOfRangeException')->willReturnCallback(
            function ($m, $c, $p) {
                return new OutOfRangeException($m, $c, $p);
            }
        );
        $mock->method('_normalizeString')->willReturnCallback(
            function($input) {
                return strval($input);
            }
        );

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
     * Tests the WP Query comparison build method to ensure that the result contains the information returned by the
     * abstracted methods.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryCompare()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(RelationalTypeInterface::T_EQUAL_TO);

        $subject->expects($this->once())
                ->method('_getWpQueryCompareKey')
                ->with($expression)
                ->willReturn($key = uniqid('key-'));
        $subject->expects($this->once())
                ->method('_getWpQueryCompareValue')
                ->with($expression)
                ->willReturn($value = uniqid('value-'));

        $expected = [$key => $value];

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryCompare($expression),
            'Expected and retrieved results are not equal'
        );
    }

    /**
     * Tests the WP Query comparison build method to assert whether an exception is thrown if an expression is not
     * supported.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryCompareUnsupported()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(RelationalTypeInterface::T_GREATER_THAN);

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryCompare($expression);
    }

    /**
     * Tests the WP Query comparison build method to assert whether an exception is thrown if the comparison key could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryCompareNoKey()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(RelationalTypeInterface::T_EQUAL_TO);

        $subject->expects($this->once())
                ->method('_getWpQueryCompareKey')
                ->with($expression)
                ->willThrowException(new OutOfRangeException());

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryCompare($expression);
    }

    /**
     * Tests the WP Query comparison build method to assert whether an exception is thrown if the comparison value
     * could not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryCompareNoValue()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(RelationalTypeInterface::T_EQUAL_TO);

        $subject->expects($this->once())
                ->method('_getWpQueryCompareValue')
                ->with($expression)
                ->willThrowException(new OutOfRangeException());

        $this->setExpectedException('OutOfRangeException');

        $reflect->_buildWpQueryCompare($expression);
    }
}
