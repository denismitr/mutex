<?php

namespace Tests\Traits;

trait LockState
{
    /** @test */
    public function it_reflects_the_state_of_the_lock()
    {
        $this->assertFalse($this->lock->isAcquired());

        $this->lock->ex(function() {
            $this->assertTrue($this->lock->isAcquired());
        });

        $this->assertFalse($this->lock->isAcquired());
    }
}
