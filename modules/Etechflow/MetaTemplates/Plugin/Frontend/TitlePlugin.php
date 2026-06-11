<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Plugin\Frontend;

use Magento\Framework\View\Page\Title;
use Magento\Framework\App\Request\Http;
use Etechflow\MetaTemplates\Service\MetaResolver;

/**
 * Returns the template <title> at read time (afterGet) so it is authoritative
 * even when another extension also intercepts the title getter.
 */
class TitlePlugin
{
    public function __construct(
        private MetaResolver $resolver,
        private Http $request
    ) {
    }

    public function afterGet(Title $subject, $result)
    {
        try {
            if ($this->request->isAjax()) {
                return $result;
            }
            $meta = $this->resolver->resolve();
            if ($meta && !empty($meta['title'])) {
                return $meta['title'];
            }
        } catch (\Throwable $e) {
            // fall through to original
        }
        return $result;
    }
}
