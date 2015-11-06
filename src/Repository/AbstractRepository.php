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
 * <https://github.com/baleen/migrations>.
 */

namespace Baleen\Migrations\Repository;

use Baleen\Migrations\Exception\RepositoryException;
use Baleen\Migrations\Migration\Factory\FactoryInterface;
use Baleen\Migrations\Migration\Factory\SimpleFactory;
use Baleen\Migrations\Version\Collection\Linked;
use Baleen\Migrations\Version\Comparator\ComparatorAwareInterface;
use Baleen\Migrations\Version\Comparator\ComparatorAwareTrait;

/**
 * Class AbstractRepository.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
abstract class AbstractRepository implements RepositoryInterface, ComparatorAwareInterface
{
    use ComparatorAwareTrait;

    /**
     * @var FactoryInterface
     */
    private $factory = null;

    /**
     * {@inheritdoc}
     *
     * @param FactoryInterface $factory
     */
    final public function setMigrationFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns the migration factory. Creates a new SimpleFactory object for it if none was configured.
     *
     * @return FactoryInterface
     */
    final protected function getMigrationFactory()
    {
        if (null === $this->factory) {
            $this->factory = new SimpleFactory();
        }
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     *
     * @return Linked
     *
     * @throws RepositoryException
     */
    final public function fetchAll()
    {
        $collection = $this->doFetchAll();
        if (!is_object($collection) || !$collection instanceof Linked) {
            throw new RepositoryException(sprintf(
                'Method AbstractRepository::doFetchAll() must return a "%s" collection. Got "%s" instead.',
                Linked::class,
                is_object($collection) ? get_class($collection) : gettype($collection)
            ));
        }
        if (!$collection->isSorted()) {
            $collection->sort($this->getComparator());
        }

        return $collection;
    }

    /**
     * Must fetch all versions available to the repository, load them with their migrations, and return them as a
     * Linked collection. It must use a factory (default or supplied by 'setMigrationFactory()') to instantiate
     * each of the migrations.
     *
     * @return mixed
     */
    abstract protected function doFetchAll();
}