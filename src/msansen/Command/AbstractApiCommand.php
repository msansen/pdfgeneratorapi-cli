<?php

namespace MSansen\Command;

use MSansen\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Sansen <matthieu.sansen@outlook.com>
 */
abstract class AbstractApiCommand extends Command
{
    const OPTION_API_KEY = 'api-key';
    const OPTION_API_SECRET = 'api-secret';
    const OPTION_API_WORKSPACE = 'api-workspace';

    const OPTION_JSON = 'json';
    const OPTION_JSON_PRETTY_PRINT = 'json-pretty';

    const OPTION_ACCESS = 'access';
    const OPTION_TAGS = 'tags';

    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $apiSecret;

    /** @var string */
    protected $apiWorkspace;

    protected function registerApiParameters(): self
    {
        return $this
            ->addOption(
                self::OPTION_API_KEY,
                null,
                InputOption::VALUE_REQUIRED,
                'PdfGenerator API key.',
                'env(PDFGENERATORAPI_KEY)'
            )

            ->addOption(
                self::OPTION_API_SECRET,
                null,
                InputOption::VALUE_REQUIRED,
                'PdfGenerator API secret.',
                'env(PDFGENERATORAPI_SECRET)'
            )

            ->addOption(
                self::OPTION_API_WORKSPACE,
                null,
                InputOption::VALUE_REQUIRED,
                'PdfGenerator API workspace.',
                'env(PDFGENERATORAPI_WORKSPACE)'
            )
        ;
    }

    protected function registerAccessParameter(): self
    {
        return $this->addOption(
            self::OPTION_ACCESS,
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Allows to filter templates by access type. Repeat options for multiple tags. Available access types: organization, private'
        );
    }

    protected function registerTagsParameter(): self
    {
        return $this->addOption(
            self::OPTION_TAGS,
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Allows to filter templates by assigned tags. Repeat options for multiple tags.'
        );
    }

    protected function registerJsonParameters(): self
    {
        return $this
            ->addOption(
                self::OPTION_JSON,
                null,
                InputOption::VALUE_NONE,
                'Output in json format.'
            )
            ->addOption(
                self::OPTION_JSON_PRETTY_PRINT,
                null,
                InputOption::VALUE_NONE,
                'Output pretty json.'
            )
        ;
    }

    protected function checkApiParameters(InputInterface $input, OutputInterface $output): bool
    {
        $this->resolveInputEnvVars($input);

        $this->apiKey = $input->getOption(self::OPTION_API_KEY);
        $this->apiSecret = $input->getOption(self::OPTION_API_SECRET);
        $this->apiWorkspace = $input->getOption(self::OPTION_API_WORKSPACE);

        $error = false;
        if (!$this->apiKey) {
            $error = true;
            $output->writeln('<error>You must provide the api key.');
        }

        if (!$this->apiSecret) {
            $error = true;
            $output->writeln('<error>You must provide the api secret.');
        }

        if (!$this->apiWorkspace) {
            $error = true;
            $output->writeln('<error>You must provide the default workspace.');
        }

        return !$error;
    }

    protected function resolveInputEnvVars(InputInterface $input)
    {
        $this->resolveEnvVars($input->getOptions(), function ($name, $value) use ($input) {
            $input->setOption($name, $value);
        });

        $this->resolveEnvVars($input->getArguments(), function ($name, $value) use ($input) {
            $input->setArgument($name, $value);
        });
    }

    protected function resolveEnvVars(array $values, \Closure $setValue)
    {
        foreach ($values as $name => $value) {
            if (\is_array($value)) {
                continue;
            }

            if (0 === strpos($value, 'env(') && ')' === substr($value, -1) && 'env()' !== $value) {
                $env = substr($value, 4, -1);
                $setValue($name, getenv($env));
            }
        }
    }

    protected function getClient(): Client
    {
        return new Client($this->apiKey, $this->apiSecret, $this->apiWorkspace);
    }
}
