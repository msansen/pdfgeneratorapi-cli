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
class TemplateGetCommand extends AbstractApiCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('template:get')
            ->setDescription(<<<'EOT'
Get info on a specific template.
    use --save-to path/to/file.json to save to file.json
    use --save-to path/to/ to save to [TEMPLATE_ID].json
    use --json to output the content to stdout
    use --json-pretty to beautify the json for both file & stdout writing 
EOT
            )
            ->registerApiParameters()
            ->registerJsonParameters()
            ->addArgument('template-id', InputArgument::REQUIRED)
            ->addOption('save-to', null, InputOption::VALUE_REQUIRED)
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkApiParameters($input, $output)) {
            return 1;
        }

        $templateID = $input->getArgument('template-id');

        try {
            $template = $this->getClient()->get($templateID);
        } catch (Exception $e) {
            $output->writeln('<error>PDF Generator API Exception:</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        if (null !== $saveTo = $input->getOption('save-to')) {
            var_dump($saveTo);

            $pathInfos = pathinfo($saveTo);
            if (!\array_key_exists('extension', $pathInfos)) {
                // Assume it's a directory
                if (!is_dir($saveTo)) {
                    @mkdir($saveTo);
                }

                $saveTo = $saveTo.'/'.$templateID.'.json';
            }

            file_put_contents($saveTo, \json_encode(
                $template,
                $input->getOption(self::OPTION_JSON_PRETTY_PRINT) ? JSON_PRETTY_PRINT : 0
            ));
        }

        if ($input->getOption(self::OPTION_JSON)) {
            $output->writeln(\json_encode(
                $template,
                $input->getOption(self::OPTION_JSON_PRETTY_PRINT) ? JSON_PRETTY_PRINT : 0
            ));
        }

        return 0;
    }
}
