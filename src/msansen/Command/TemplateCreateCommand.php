<?php

namespace MSansen\Command;

use ActualReports\PDFGeneratorAPI\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
class TemplateCreateCommand extends AbstractApiCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('template:create')
            ->setDescription(<<<'EOT'
Create a new template.
    use --from-file path/to/file.json to use that file as the new template content. Id will be overwritten.
EOT
            )
            ->registerApiParameters()
            ->registerJsonParameters()
            ->addOption('from-file', null, InputOption::VALUE_REQUIRED)
            ->addOption('name', null, InputOption::VALUE_REQUIRED)
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkApiParameters($input, $output)) {
            return 1;
        }

        $filename = $input->getOption('from-file');
        $templateName = $input->getOption('name');
        $content = [];

        if ($filename && !file_exists($filename)) {
            $output->writeln(sprintf('<error>File %s does not exist.', $filename));

            return 1;
        } elseif ($filename) {
            $content = \json_decode(file_get_contents($filename), true);
        } elseif (null === $templateName) {
            $output->writeln('<error>You must provide a template name (--name)</error>');

            return 1;
        }

        $response = null;
        try {
            if ($filename) {
                $response = $this->getClient()->createFromContent($content, $templateName);
            } else {
                $response = $this->getClient()->create($templateName);
            }
        } catch (Exception $e) {
            $output->writeln('<error>PDF Generator API Exception:</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        if ($input->getOption(self::OPTION_JSON)) {
            $output->writeln(\json_encode(
                $response,
                $input->getOption(self::OPTION_JSON_PRETTY_PRINT) ? JSON_PRETTY_PRINT : 0
            ));
        } else {
            $output->writeln('<info>OK</info>');
        }

        return 0;
    }
}
