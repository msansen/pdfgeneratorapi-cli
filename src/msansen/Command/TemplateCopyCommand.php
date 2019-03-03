<?php

namespace MSansen\Command;

use ActualReports\PDFGeneratorAPI\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
class TemplateCopyCommand extends AbstractApiCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('template:copy')
            ->setDescription('Duplicate a template.')
            ->registerApiParameters()
            ->addArgument('template-id', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkApiParameters($input, $output)) {
            return 1;
        }

        $templateID = $input->getArgument('template-id');
        $newName = $input->getArgument('name');

        try {
            $template = $this->getClient()->copy($templateID, $newName);
        } catch (Exception $e) {
            $output->writeln('<error>PDF Generator API Exception:</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        $output->writeln('<info>OK</info>');

        return 0;
    }
}
