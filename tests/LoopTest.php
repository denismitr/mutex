<?php

namespace Tests;

use Denismitr\Mutex\Utilities\Loop;
use PHPUnit\Framework\TestCase;
use phpmock\environment\SleepEnvironmentBuilder;
use phpmock\phpunit\PHPMock;

class LoopTest extends TestCase
{
    use PHPMock;

    public function setUp()
    {
        parent::setUp();

        $builder = new SleepEnvironmentBuilder();
        $builder->addNamespace(__NAMESPACE__);
        $sleep = $builder->build();
        $sleep->enable();

        $this->registerForTearDown($sleep);
    }

    /** @test */
    public function it_executes_within_timeout()
    {
        $loop = new Loop(1);

        $result = $loop->run(function($loop) {
            usleep(999999);

            $loop->stop();

            return 'result';
        });

        $this->assertEquals('result', $result);
    }

    /**
    * @expectedException \InvalidArgumentException
    * @expectedExceptionMessage Timeout cannot be less than 1 second
    * @test
    */
    public function it_throws_on_invalid_timeout()
    {
        $loop = new Loop(0);
    }

    /**
    * @test
    *
    * @expectedException \Denismitr\Mutex\Errors\TimeoutError
    * @expectedExceptionMessage Timeout exceeded.
    */
    public function it_exceeds_the_given_timeout_without_stop()
    {
        $loop = new Loop(1);

        $loop->run(function ($loop) {
            sleep(3);
        });
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function testExceptionStopsIteration()
    {
        $loop = new Loop;

        $loop->run(function () {
            throw new \Exception;
        });
    }

    /** @test */
    public function it_stops_iterating_on_stop_call()
    {
        $i = 0;
        $loop = new Loop(1);

        $loop->run(function ($loop) use (&$i) {
            $i++;
            $loop->stop();
        });

        $this->assertGreaterThan(0, $i);
    }

    /**
     * @test
     */
    public function it_iterates_until_condition_in_callback()
    {
        $i    = 0;
        $loop = new Loop;

        $loop->run(function ($loop) use (&$i) {
            $i++;

            if ($i > 1) {
                $loop->stop();
            }
        });

        $this->assertEquals(2, $i);
    }
}
