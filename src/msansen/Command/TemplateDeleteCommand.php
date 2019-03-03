<?php

namespace MSansen\Command;

use ActualReports\PDFGeneratorAPI\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
class TemplateDeleteCommand extends AbstractApiCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('template:delete')
            ->setDescription('Delete a template.')
            ->registerApiParameters()
            ->addArgument('template-id', InputArgument::REQUIRED)
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkApiParameters($input, $output)) {
            return 1;
        }

        $templateID = $input->getArgument('template-id');

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion('Are you sure you want to delete this template ? [y/N]', false);

            if (!$this->getHelper('question')->ask($input, $output, $question)) {
                return 0;
            }
        }

        try {
            $template = $this->getClient()->delete($templateID);
        } catch (Exception $e) {
            $output->writeln('<error>PDF Generator API Exception:</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        $output->writeln('<info>OK</info>');

        return 0;
    }
}
