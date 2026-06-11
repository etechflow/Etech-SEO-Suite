<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Controller\Adminhtml\Sitemap;

use Etechflow\Sitemap\Model\Generator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Generate extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Sitemap::sitemap';

    public function __construct(
        Context $context,
        private readonly Generator $generator
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $result = $this->generator->generate();
            if (!$result['stores']) {
                $this->messageManager->addWarningMessage(
                    __('No store views are enabled for the sitemap. Enable it under '
                        . 'Stores > Configuration > Etechflow > Sitemap.')
                );
            } else {
                $this->messageManager->addSuccessMessage(
                    __('Sitemap generated: %1 URLs across %2 store view(s), %3 file(s).',
                        $result['total_urls'],
                        count($result['stores']),
                        count($result['files']))
                );
            }
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Sitemap generation failed: %1', $e->getMessage()));
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
