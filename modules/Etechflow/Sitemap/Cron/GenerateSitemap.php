<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Cron;

use Etechflow\Sitemap\Model\Config;
use Etechflow\Sitemap\Model\Generator;
use Psr\Log\LoggerInterface;

/**
 * Nightly sitemap rebuild. No-op unless scheduled generation is enabled.
 */
class GenerateSitemap
{
    public function __construct(
        private readonly Config $config,
        private readonly Generator $generator,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        if (!$this->config->isCronEnabled()) {
            return;
        }
        try {
            $result = $this->generator->generate();
            $this->logger->info(sprintf(
                'Etechflow_Sitemap: cron generated %d URLs across %d store view(s).',
                $result['total_urls'],
                count($result['stores'])
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Etechflow_Sitemap: cron generation failed: ' . $e->getMessage());
        }
    }
}
