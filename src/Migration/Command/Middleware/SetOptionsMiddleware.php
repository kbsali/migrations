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

namespace Baleen\Migrations\Migration\Command\Middleware;

use Baleen\Migrations\Migration\Capabilities\OptionsAwareInterface;
use Baleen\Migrations\Migration\Command\MigrateCommand;

/**
 * Checks if a migration is an instance of OptionsAwareInterface and if so sends it
 * the options available in the command.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
final class SetOptionsMiddleware extends AbstractMiddleware
{
    /**
     * {@inheritDoc}
     */
    protected function doExecute(MigrateCommand $command, callable $next)
    {
        $migration = $command->getMigration();
        if ($migration instanceof OptionsAwareInterface) {
            $migration->setOptions($command->getOptions());
        }
        return $next($command);
    }
}
