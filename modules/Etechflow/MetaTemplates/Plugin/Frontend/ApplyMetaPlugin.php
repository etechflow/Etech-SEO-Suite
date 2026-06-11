<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Plugin\Frontend;

use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\App\Request\Http;
use Etechflow\MetaTemplates\Service\MetaResolver;

/**
 * Applies the resolved template meta to the page result's own config, right
 * before render — the most reliable point to override title/description/keywords.
 */
class ApplyMetaPlugin
{
    public function __construct(
        private MetaResolver $resolver,
        private Http $request
    ) {
    }

    public function beforeRenderResult(ResultPage $subject, $response)
    {
        try {
            if ($this->request->isAjax()) {
                return null;
            }
            $meta = $this->resolver->resolve();
            if (!$meta) {
                return null;
            }
            $config = $subject->getConfig();
            if (!empty($meta['title'])) {
                $config->getTitle()->set($meta['title']);
                if (method_exists($config, 'setMetaTitle')) {
                    $config->setMetaTitle($meta['title']);
                }
            }
            if (!empty($meta['description'])) {
                $config->setDescription($meta['description']);
            }
            if (!empty($meta['keywords'])) {
                $config->setKeywords($meta['keywords']);
            }
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }
}
