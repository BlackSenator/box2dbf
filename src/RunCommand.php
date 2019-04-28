<?php

namespace blacksenator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    use ConfigTrait;

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Download, convert and upload - all in one');

        $this->addConfig();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfig($input);

        $phonebook = downloadPhonebookSOAP($this->config);

        if (isset($this->config['fritzbox']['fritzadr'])) {
            error_log('Selecting and uploading fax number(s) for FRITZ!fax');
            $i = uploadFritzAdr($phonebook, $this->config['fritzbox']);
            if ($i) {
                error_log(sprintf("Uploaded %d fax number entries into fritzadr.dbf", $i));
            }
        }
    }
}
