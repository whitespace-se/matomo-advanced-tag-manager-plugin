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

class AdvancedTagManagerExport extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('tagmanager:export');
        $this->setDescription('AdvancedTagManagerExport');
        $this->addOption('site', null, InputOption::VALUE_REQUIRED, 'Site:');
        $this->addOption('container-id', null, InputOption::VALUE_REQUIRED, 'Container ID:');
        $this->addOption('container-version', null, InputOption::VALUE_OPTIONAL, 'Version:');

        $this->dao = new ContainersDao();
        $this->apiTagManager = APITagManager::getInstance();
    }

    /**
     * Export a tag manager container 
     *
     * Execute the command like: ./console tagmanager:export --site=1 --container-id=abcd1234 --container-version=1
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteId = $input->getOption('site');
        $containerId = $input->getOption('container-id');
        $containerVersion = $input->getOption('container-version')?$input->getOption('container-version'):null;

        if($containerVersion !== null){
            $containerVersionData = $this->dao->getContainer($siteId, $containerId, $containerVersion);

            if($containerVersionData === false) {
                $output->writeln("<error>Container with site id $siteId, container id $containerId and version $containerVersion does not exist.</error>");
                exit;   
            }
        } else {
            $containerData = $this->dao->getContainer($siteId, $containerId);

            if($containerData === false) {
                $output->writeln("<error>Container with site id $siteId and container id $containerId does not exist.</error>");
                exit;   
            }
        }

        // Get container
        $export = $this->apiTagManager->exportContainerVersion($siteId, $containerId, $containerVersion);
        $json = json_encode($export);

        // Output as a string
        $output->writeln($json);
    }
}
