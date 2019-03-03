<?php

namespace MSansen\Command;

use ActualReports\PDFGeneratorAPI\Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
class TemplateListCommand extends AbstractApiCommand
{
    /** {@inheritdoc} */
    protected function configure()
    {
        $this
            ->setName('template:list')
            ->setDescription(<<<'EOT'
Returns list of templates in the workspace.
   By default, display a list of ids.
   Use -vv to display a table with ids, names and tags.
EOT
            )
            ->registerApiParameters()
            ->registerJsonParameters()
            ->registerAccessParameter()
            ->registerTagsParameter()
        ;
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->checkApiParameters($input, $output)) {
            return 1;
        }

        $access = $input->getOption(self::OPTION_ACCESS);
        $tags = $input->getOption(self::OPTION_TAGS);

        try {
            $templates = $this->getClient()->getAll($access, $tags);
        } catch (Exception $e) {
            $output->writeln('<error>PDF Generator API Exception:</error>');
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        if ($input->getOption(self::OPTION_JSON)) {
            $output->writeln(\json_encode(
                $templates,
                $input->getOption(self::OPTION_JSON_PRETTY_PRINT) ? JSON_PRETTY_PRINT : 0
            ));
        } elseif ($output->getVerbosity() === OutputInterface::VERBOSITY_NORMAL) {
            foreach ($templates as $template) {
                $output->writeln($template->id);
            }
        } elseif ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $table = new Table($output);
            $table->setHeaders(['# Id', 'Name', 'Tags']);

            foreach ($templates as $template) {
                $table->addRow([$template->id, $template->name, implode(', ', $template->tags)]);
            }

            $table->render();
        }

        return 0;
    }
}
