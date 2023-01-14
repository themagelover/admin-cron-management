<?php

namespace TheMageLover\AdminCronManagement\Controller\Adminhtml\Executor;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * Controller for Cron Executor Listing
 */
class Index extends Action
{
    /** @var string */
    public const ADMIN_RESOURCE = 'TheMageLover_AdminCronManagement::cron_executor';

    /** @var PageFactory */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__("Cron Executor"));
        return $resultPage;
    }
}
