<?php

namespace BlackSenator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RunCommand extends Command
{
    use ConfigTrait;
    
    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Download, convert and upload - all in one')
            ->addOption('image', 'i', InputOption::VALUE_NONE, 'download images');

        $this->addConfig();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->loadConfig($input);
		
		// download phonebooks

        $fritzbox = $this->config['fritzbox'];
        
        $client = new \SoapClient(
            null,
            array(
                'location'   => "http://".$fritzbox['url'].":49000/upnp/control/x_contact",
                'uri'        => "urn:dslforum-org:service:X_AVM-DE_OnTel:1",
                'noroot'     => true,
                'login'      => $fritzbox['user'],
                'password'   => $fritzbox['password'],  /*
				'trace'      => true,
                'exceptions' => true  */
            )
        );		

        foreach ($this->config['phonebooks'] as $phonebook) {
            error_log("Downloading contacts from Fritz!Box; IP: ".$fritzbox['url']);
            $result = $client->GetPhonebook(new \SoapParam($phonebook['id'],"NewPhonebookID"));
		    $xml = simplexml_load_file($result['NewPhonebookURL']);
			$nc = exportfa($xml, $this->config['fritzadrpath'][0]);
			error_log(sprintf("Converted %d FAX number(s) in " . $this->config['fritzadrpath'][0], $nc));
        }
    }  
}