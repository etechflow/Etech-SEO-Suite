<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Console\Command;

use Etechflow\Sitemap\Model\Generator;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * bin/magento etechflow:sitemap:generate
 */
class GenerateCommand extends Command
{
    public function __construct(
        private readonly State $state,
        private readonly Generator $generator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('etechflow:sitemap:generate')
            ->setDescription('Generate the Etechflow XML sitemap for all enabled store views.');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->state->setAreaCode(Area::AREA_FRONTEND);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Area already set (e.g. invoked within another command) — fine.
        }

        try {
            $result = $this->generator->generate();
        } catch (\Throwable $e) {
            $output->writeln('<error>Sitemap generation failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        if (!$result['stores']) {
            $output->writeln('<comment>No store views are enabled for the sitemap. '
                . 'Enable it under Stores > Configuration > Etechflow > Sitemap.</comment>');
            return Command::SUCCESS;
        }

        foreach ($result['stores'] as $storeId => $info) {
            $output->writeln(sprintf(
                '  store %d: %d URLs -> %s',
                $storeId,
                $info['urls'],
                implode(', ', $info['files'])
            ));
        }
        $output->writeln(sprintf(
            '<info>Done. %d URLs across %d store view(s), %d file(s) written.</info>',
            $result['total_urls'],
            count($result['stores']),
            count($result['files'])
        ));

        return Command::SUCCESS;
    }
}
