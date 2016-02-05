<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RpcInfoResourceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rpc:info_resource')
            ->setDescription('Remote Procedure Call API - Get Resource Info')
            ->addArgument('CIK', InputArgument::REQUIRED, 'Authenticates as the client identified by the given CIK')
            ->addArgument('RID', InputArgument::REQUIRED, 'Authenticates as the client identified by the given CIK')
            ->addOption('Client_Id', null, InputOption::VALUE_REQUIRED, 'Authenticates as the given client if the CIK identifies an ancestor of the given client')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cik = $input->getArgument('CIK');
        $rid = $input->getArgument('RID');
        $request = json_decode('{
            "auth": {
                "cik": "'.$cik.'"
            },
            "calls": [
            ]
        }');
        $requestCall = json_decode('{
            "procedure": "info",
            "arguments": [
                "'. $rid .'",
                {
                    "aliases": true,
                    "basic": true,
                    "counts": true,
                    "description": true,
                    "key": true,
                    "shares": true,
                    "storage":true,
                    "subscribers":true,
                    "tagged": true,
                    "tags": true,
                    "usage": true
                }
            ],
            "id": 1
        }');
        if ($input->getOption('Client_Id')) {
            $client_id = $input->getOption('Client_Id');
            $request->auth->client_id = $client_id;
        }
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
