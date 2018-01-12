<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\BuildWpQueryMetaCompareCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryMetaCompareCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryMetaCompareCapableTrait';

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
                                    '_getWpQueryMetaCompareKey',
                                    '_getWpQueryMetaCompareValue',
                                    '_getWpQueryMetaCompareType',
                                    '_getWpQueryMetaCompareOperator',
                                    '_createInvalidArgumentException',
                                    '__',
                                ]
                            )
                        );

        $mock = $builder->getMockForTrait();
        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createInvalidArgumentException')->willReturnCallback(
            function ($m, $c, $p) {
                return new InvalidArgumentException($m, $c, $p);
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
     * Tests the WP Query meta compare method to ensure that the result contains the information returned by the
     * abstracted methods.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryMetaCompare()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareKey')
                ->with($expression)
                ->willReturn($key = uniqid('key-'));
        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareValue')
                ->with($expression)
                ->willReturn($value = uniqid('val-'));
        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareType')
                ->with($expression)
                ->willReturn($type = uniqid('type-'));
        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareOperator')
                ->with($expression)
                ->willReturn($op = uniqid('op-'));

        $expected = [
            'key' => $key,
            'value' => $value,
            'type' => $type,
            'compare' => $op,
        ];

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryMetaCompare($expression),
            'Expected and retrieved arrays are not equal'
        );
    }

    /**
     * Tests the WP Query meta compare method to ensure that it throws an exception if the comparison meta key could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryMetaCompareTaxonomyException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareKey')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryMetaCompare($expression);
    }

    /**
     * Tests the WP Query meta compare method to ensure that it throws an exception if the comparison meta value could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryMetaCompareFieldException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareValue')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryMetaCompare($expression);
    }

    /**
     * Tests the WP Query meta compare method to ensure that it throws an exception if the comparison meta type could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryMetaCompareTermsException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareType')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryMetaCompare($expression);
    }

    /**
     * Tests the WP Query meta compare method to ensure that it throws an exception if the comparison operator could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryMetaCompareOperatorException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryMetaCompareOperator')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryMetaCompare($expression);
    }
}
