<?php

namespace TheMageLover\AdminCronManagement\Controller\Adminhtml\Executor;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use TheMageLover\AdminCronManagement\Model\JobExecutor;
use TheMageLover\AdminCronManagement\Helper\Data;

/**
 * Class MassRun
 * Mass Run action for Cron Executor
 */
class MassRun extends Action
{
    /** @var JobExecutor */
    protected $jobExecutor;

    /** @var Data */
    protected $helperData;

    /**
     * @param Context $context
     * @param JobExecutor $jobExecutor
     * @param Data $helperData
     */
    public function __construct(Context $context, JobExecutor $jobExecutor, Data $helperData)
    {
        $this->jobExecutor = $jobExecutor;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // Get selected records
        $params = $this->getRequest()->getParams();
        $jobSelected = $this->helperData->getSelectedRecords($params);

        // Execute the jobs
        $success = $failed = 0;
        $failedCron = [];
        foreach ($jobSelected as $jobName) {
            $status = $this->jobExecutor->executeJob($jobName);
            if ($status) {
                $success++;
            } else {
                $failed++;
                $failedCron[] = $jobName;
            }
        }

        // Status messages
        if ($success > 0) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been executed.', $success));
        }
        if ($failed > 0) {
            $failedCronStr = implode(", ", $failedCron);
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) has been failed: %2',
                    $failed,
                    $failedCronStr
                )
            );
            $this->messageManager->addWarningMessage(__('Check Cron Scheduled Jobs for more detail.'));
        }

        return $this->_redirect('*/*/');
    }
}
