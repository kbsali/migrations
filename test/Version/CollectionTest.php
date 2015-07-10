<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace BaleenTest\Migrations\Version;

use Baleen\Migrations\Exception\InvalidArgumentException;
use Baleen\Migrations\Exception\CollectionException;
use Baleen\Migrations\Version as V;
use Baleen\Migrations\Version;
use Baleen\Migrations\Version\Collection;
use Baleen\Migrations\Version\Collection\BaseCollection;
use BaleenTest\Migrations\BaseTestCase;
use EBT\Collection\ResourceNotFoundException;
use Mockery as m;
use Zend\Stdlib\ArrayUtils;

/**
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class CollectionTest extends BaseTestCase
{

    public function testConstructorInvalidArgument()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $instance = new Collection('test');
        $this->assertInstanceOf(BaseCollection::class, $instance);
    }

    public function testConstructorIterator()
    {
        $versions = Version::fromArray(['1', '2', '3']);
        $iterator = new \ArrayIterator($versions);
        $instance = new Collection($iterator);
        $this->assertCount(3, $instance);
    }

    public function testConstructorWithInvalidVersions()
    {
        $versions = [new V('1'), '2', 3, new \stdClass()]; // only first one is valid

        $this->setExpectedException(InvalidArgumentException::class);
        $instance = new Collection($versions);
    }

    public function testConstructor()
    {
        $instance = new Collection();
        $this->assertInstanceOf(BaseCollection::class, $instance);
        $this->assertEmpty($instance);

        $version = new V('1');
        $instance = new Collection([$version]);
        $this->assertInstanceOf(BaseCollection::class, $instance);
        $this->assertCount(1, $instance);

        return $instance;
    }

    /**
     * @depends testConstructor
     */
    public function testAdd(Collection $instance)
    {
        $originalCount = count($instance);
        $version2 = new V('2');
        $instance->add($version2);
        $this->assertCount($originalCount + 1, $instance);

        return $instance;
    }

    /**
     * @depends testAdd
     */
    public function testRemove(Collection $instance)
    {
        $originalCount = count($instance);

        // test remove by version object
        $version = new V('1');
        $instance->remove($version);
        $this->assertCount(--$originalCount, $instance);

        // test remove by version id
        $instance->remove('2');
        $this->assertEmpty($instance);
    }

    public function testAddOrUpdate()
    {
        $versions = Version::fromArray('1', '2', '3');
        $instance = new Collection(array_slice($versions, 0, 2));
        $this->assertTrue($instance->has('1'));

        $migrated = clone $versions[0];
        $migrated->setMigrated(true);

        $instance->addOrUpdate($migrated); // should replace the first version
        $this->assertSame(
            $instance->get('1'),
            $migrated
        );

        $this->assertFalse($instance->has('3'));
        $instance->addOrUpdate($versions[2]);
        $this->assertTrue($instance->has('3'));
    }

    public function testAddException()
    {
        $version = new V('1');
        $instance = new Collection([$version]);

        $this->setExpectedException(CollectionException::class);
        $instance->add($version);
    }

    public function testMerge()
    {
        $instance1 = new Collection(Version::fromArray('1', '2', '3', '4', '5'));
        $migrated = Version::fromArray('2', '5', '6', '7');
        foreach ($migrated as $v) {
            $v->setMigrated(true);
        }
        $instance2 = new Collection($migrated);

        $instance1->merge($instance2);

        foreach ($migrated as $v) {
            $this->assertTrue($instance1->getOrException($v)->isMigrated());
        }
    }

    public function testGetOrException()
    {
        $versions = Version::fromArray('1', '2');
        $instance = new Collection($versions);

        $this->setExpectedException(ResourceNotFoundException::class);
        $instance->getOrException('3');
    }

    public function testArrayAccess()
    {
        $instance = new Collection(Version::fromArray('100', '101', '102'));
        $this->assertSame('100', $instance->current()->getId());
        $this->assertSame(100, $instance->key());
        $instance->next();
        $this->assertSame('101', $instance->current()->getId());
        $this->assertSame(101, $instance->key());
        $instance->prev();
        $this->assertSame('100', $instance->current()->getId());
        $this->assertSame(100, $instance->key());
        $instance->end();
        $this->assertSame('102', $instance->current()->getId());
        $this->assertSame(102, $instance->key());
        $instance->rewind();
        $this->assertSame('100', $instance->current()->getId());
        $this->assertSame(100, $instance->key());
    }

    public function testGetIdOrVersionThrowsException()
    {
        $instance = new Collection([new V('1')]);

        $this->setExpectedException(InvalidArgumentException::class);
        $instance->has(new \stdClass());
    }
}