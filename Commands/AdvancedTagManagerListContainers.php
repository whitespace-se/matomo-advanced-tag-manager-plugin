<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AdvancedTagManager\Commands;

use Piwik\Plugin\ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Piwik\Plugins\TagManager\API as APITagManager;
use Piwik\Plugins\TagManager\Dao\ContainersDao;

class AdvancedTagManagerListContainers extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('tagmanager:list-containers');
        $this->setDescription('List containers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dao = new ContainersDao();
        $containers = $this->dao->getAllContainers();

        $width = 20;
        $data = [];
        foreach ($containers as $container) {
            $data[$container['idsite']][] = $container;

            if(strlen($container['name']) > $width){
                $width = strlen($container['name']) + 1;
            }
        }

        $output->writeln("");
        foreach($data as $idsite => $siteContainers) {
            $message = sprintf('<comment>Site: %s</comment>', $idsite);
            $output->writeln($message);

            foreach($siteContainers as $container) {
                // $message = sprintf('  <info>Container: %s, id: %s</info>', $container['name'], $container['idcontainer']);
                $spacingWidth = $width - strlen($container['name']);
                $lines = [
                    "Context" => $container['context'],
                    "Updated" => $container['updated_date'],
                    "Created" => $container['created_date'],
                    "Description" => $container['description']
                ];

                $output->writeln(sprintf(' <info>%s</info>%s%s', $container['name'], str_repeat(' ', $spacingWidth), sprintf("<info>Id:</info> %s", $container['idcontainer']) ));
                foreach($lines as $lineTitle => $lineValue) {
                    $output->writeln(sprintf(' %s%s', str_repeat(' ', $width), sprintf("<info>%s:</info> %s", $lineTitle, $lineValue) ));
                }
            }
        }
    }
}
