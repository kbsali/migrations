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

namespace Baleen\Migrations\Service\DomainBus\Migrate\Collection;

use Baleen\Migrations\Migration\Options;
use Baleen\Migrations\Migration\OptionsInterface;
use Baleen\Migrations\Service\DomainBus\DomainCommandInterface;
use Baleen\Migrations\Service\DomainBus\HasCollectionTrait;
use Baleen\Migrations\Service\DomainBus\HasOptionsTrait;
use Baleen\Migrations\Service\DomainBus\HasVersionRepositoryTrait;
use Baleen\Migrations\Service\DomainBus\Migrate\AbstractMigrateCommand;
use Baleen\Migrations\Service\DomainBus\Migrate\HasTargetTrait;
use Baleen\Migrations\Common\Collection\CollectionInterface;
use Baleen\Migrations\Version\Repository\VersionRepositoryInterface;
use Baleen\Migrations\Version\VersionInterface;

/**
 * Class CollectionCommand
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class CollectionCommand implements DomainCommandInterface
{
    use HasCollectionTrait;
    use HasTargetTrait;
    use HasOptionsTrait;
    use HasVersionRepositoryTrait;

    /**
     * CollectionCommand constructor.
     *
     * @param CollectionInterface $collection
     * @param VersionInterface $target
     * @param OptionsInterface $options
     * @param VersionRepositoryInterface $versionRepository
     */
    public function __construct(
        CollectionInterface $collection,
        VersionInterface $target,
        OptionsInterface $options,
        VersionRepositoryInterface $versionRepository
    ) {
        $this->setCollection($collection);
        $this->setTarget($target);
        $this->setOptions($options);
        $this->setVersionRepository($versionRepository);
    }
}
