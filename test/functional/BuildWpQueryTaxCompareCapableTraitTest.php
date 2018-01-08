<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see \RebelCode\WordPress\Query\Builder\BuildWpQueryTaxCompareCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryTaxCompareCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryTaxCompareCapableTrait';

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
                                    '_getWpQueryTaxCompareTaxonomy',
                                    '_getWpQueryTaxCompareField',
                                    '_getWpQueryTaxCompareTerms',
                                    '_getWpQueryTaxCompareOperator',
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
     * Tests the WP Query taxonomy compare method to ensure that the result contains the information returned by the
     * abstracted methods.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryTaxCompare()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareTaxonomy')
                ->with($expression)
                ->willReturn($tax = uniqid('tax-'));
        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareField')
                ->with($expression)
                ->willReturn($field = uniqid('field-'));
        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareTerms')
                ->with($expression)
                ->willReturn($terms = uniqid('terms-'));
        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareOperator')
                ->with($expression)
                ->willReturn($op = uniqid('op-'));

        $expected = [
            'taxonomy' => $tax,
            'field' => $field,
            'terms' => $terms,
            'operator' => $op,
        ];

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryTaxCompare($expression),
            'Expected and retrieved arrays are not equal',
            0,
            10,
            true
        );
    }

    /**
     * Tests the WP Query taxonomy compare method to ensure that it throws an exception if the comparison taxonomy could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryTaxCompareTaxonomyException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareTaxonomy')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryTaxCompare($expression);
    }

    /**
     * Tests the WP Query taxonomy compare method to ensure that it throws an exception if the comparison field could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryTaxCompareFieldException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareField')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryTaxCompare($expression);
    }

    /**
     * Tests the WP Query taxonomy compare method to ensure that it throws an exception if the comparison terms could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryTaxCompareTermsException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareTerms')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryTaxCompare($expression);
    }

    /**
     * Tests the WP Query taxonomy compare method to ensure that it throws an exception if the comparison operator could
     * not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryTaxCompareOperatorException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryTaxCompareOperator')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryTaxCompare($expression);
    }
}
