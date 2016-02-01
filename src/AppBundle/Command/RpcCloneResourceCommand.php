<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class RpcCloneResourceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('rpc:clone_resource')
            ->setDescription('Remote Procedure Call API - Clone Resource')
            ->addArgument('CIK', InputArgument::REQUIRED, 'Authenticates as the client identified by the given CIK')
            ->addArgument('RID', InputArgument::REQUIRED, 'Rid is the resource identifier to clone')
            ->addOption('scale', null, InputOption::VALUE_REQUIRED, 'Set scale number e.g --scale=10')
            ->addOption('repeat', null, InputOption::VALUE_REQUIRED, 'Set repeat number e.g --repeat=10')
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
            "procedure": "create",
            "arguments": [
                "clone",
                {
                     "rid": "'.$rid.'",
                     "noaliases": false,
                     "nohistorical": false
                }
            ],
            "id": null
        }');

        if ($input->getOption('scale')) {
            $scale = $input->getOption('scale');
        } else {
            $scale = 1;
        }

        if ($input->getOption('repeat')) {
            $repeat = $input->getOption('repeat');
        } else {
            $repeat = 1;
        }

        for ($id=1; $id <= $scale; $id++) { 
            $call = clone $requestCall;
            $call->id = $id;
            $call->arguments[1]->name = date('Y/m/d H:i:s');
            $request->calls[] = $call;
        }

        $request = json_encode($request);   
        // $cmd = "curl -is -o /dev/null -w connectTime:%{time_connect}starttransferTime:%{time_starttransfer}totalTime:%{time_total} -XPOST 'https://m2-dev.exosite.com/onep:v1/rpc/process' -H'Content-Type: application/json; charset=utf-8' -d'$request'";
        $cmd = "time curl -is -XPOST 'https://m2-dev.exosite.com/onep:v1/rpc/process' -H'Content-Type: application/json; charset=utf-8' -d'$request'";
        
        $progress = new ProgressBar($output, $repeat);
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $progress->start();    
        for ($i=0; $i < $repeat; $i++) { 
            echo PHP_EOL, `$cmd`, PHP_EOL, PHP_EOL;
            $progress->advance();
        }
        $progress->finish();
        echo PHP_EOL;
    }

}
