<?php

namespace RebelCode\WordPress\Query\Builder\FuncTest;

use Dhii\Expression\LogicalExpressionInterface;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see RebelCode\WordPress\Query\Builder\BuildWpQueryRelationCapableTrait}.
 *
 * @since [*next-version*]
 */
class BuildWpQueryRelationCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'RebelCode\WordPress\Query\Builder\BuildWpQueryRelationCapableTrait';

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
                                    '_getWpQueryRelationOperator',
                                    '_buildWpQueryRelationTerm',
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
     * Tests the WP Query relation build method to assert whether the result contains the information returned by the
     * abstracted methods.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelation()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            'AND',
            [
                $child1 = $this->createLogicalExpression('C1'),
                $child2 = $this->createLogicalExpression('C2'),
                $child3 = $this->createLogicalExpression('C3'),
            ]
        );
        $numTerms = count($expression->getTerms());

        $subject->expects($this->once())
                ->method('_getWpQueryRelationOperator')
                ->with($expression)
                ->willReturn($op = uniqid('op-'));

        $subject->expects($this->exactly($numTerms))
                ->method('_buildWpQueryRelationTerm')
                ->withConsecutive([$child1], [$child2], [$child3])
                ->willReturnOnConsecutiveCalls(
                    $bc1 = uniqid('built-child-'),
                    $bc2 = uniqid('built-child-'),
                    $bc3 = uniqid('built-child-')
                );

        $expected = [
            'relation' => $op,
            $bc1,
            $bc2,
            $bc3,
        ];

        $this->assertEquals(
            $expected,
            $reflect->_buildWpQueryRelation($expression),
            'Expected and retrieved results are not equal',
            0,
            10,
            true
        );
    }

    /**
     * Tests the WP Query relation build method to assert whether an exception is thrown when the relation operator
     * could not be determined.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationOperatorException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression('AND');

        $subject->expects($this->once())
                ->method('_getWpQueryRelationOperator')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryRelation($expression);
    }

    /**
     * Tests the WP Query relation build method to assert whether an exception is thrown when an expression term
     * could not be built.
     *
     * @since [*next-version*]
     */
    public function testBuildWpQueryRelationBuildTermException()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $expression = $this->createLogicalExpression(
            'AND',
            [
                $child1 = $this->createLogicalExpression('C1'),
                $child2 = $this->createLogicalExpression('C2'),
                $child3 = $this->createLogicalExpression('C3'),
            ]
        );

        $subject->expects($this->once())
                ->method('_buildWpQueryRelationTerm')
                ->with($expression)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_buildWpQueryRelation($expression);
    }
}
