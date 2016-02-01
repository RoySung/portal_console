<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RpcListingResourceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rpc:listing_resource')
            ->setDescription('Remote Procedure Call API - Listing Resource')
            ->addArgument('CIK', InputArgument::REQUIRED, 'Authenticates as the client identified by the given CIK')
            ->addArgument('RID', InputArgument::REQUIRED, 'Rid is the resource identifier to clone')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Is a list of resource types. Valid types are client, dataport, datarule, and dispatch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cik = $input->getArgument('CIK');
        $rid = $input->getArgument('RID');
        if ($input->getOption('type')) {
            $type = '"'. $input->getOption('type') .'"';
        } else {
            $type = '"client","dataport","datarule","dispatch"';
        }
        $request = json_decode('{
            "auth": {
                "cik": "'.$cik.'"
            },
            "calls": [
            ]
        }');
        $requestCall = json_decode('{
            "procedure": "listing",
            "arguments": [
                "'. $rid .'",
                ['. $type .'],
                {"owned":true}
            ],
            "id": 1
        }');

        $call = clone $requestCall;
        $request->calls[] = $call;
        $request = json_encode($request);   
        $cmd = "time curl -is -XPOST 'https://m2-dev.exosite.com/onep:v1/rpc/process' -H'Content-Type: application/json; charset=utf-8' -d'$request'";
        
        $progress = new ProgressBar($output, 1);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $progress->start();    
        echo PHP_EOL, `$cmd`, PHP_EOL, PHP_EOL;
        $progress->advance();
        $progress->finish();
        echo PHP_EOL;
    }

}
