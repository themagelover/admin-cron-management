<?php

namespace TheMageLover\AdminCronManagement\Model;

use Magento\Cron\Model\ResourceModel\Schedule\Collection;
use Magento\Cron\Model\Schedule;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TheMageLover\AdminCronManagement\Helper\Data;

/**
 * Class JobExecutor
 * Business Model to execute the Cron Jobs
 */
class JobExecutor
{
    /** @var Data */
    protected $helperData;

    /** @var Collection */
    protected $cronScheduleCollection;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /** @var TimezoneInterface */
    protected $timezone;

    /**
     * @param Data $helperData
     * @param Collection $cronScheduleCollection
     * @param ObjectManagerInterface $objectManager
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Data $helperData,
        Collection $cronScheduleCollection,
        ObjectManagerInterface $objectManager,
        TimezoneInterface $timezone
    ) {
        $this->helperData = $helperData;
        $this->cronScheduleCollection = $cronScheduleCollection;
        $this->objectManager = $objectManager;
        $this->timezone = $timezone;
    }

    /**
     * Execute cron job
     *
     * @param string $jobName
     * @return bool
     * @throws \Exception
     */
    public function executeJob($jobName)
    {
        // Get schedule
        /* @var $schedule Schedule */
        $schedule = $this->cronScheduleCollection->getNewEmptyItem();
        try {
            // Get job data
            $job = $this->helperData->getJobByName($jobName);
            if ($job == null || $job['instance'] == null || $job['method'] == null) {
                return false;
            }
            $instance = $job['instance'];
            $method = $job['method'];

            // Init schedule
            $schedule
                ->setJobCode($jobName)
                ->setStatus(Schedule::STATUS_RUNNING)
                ->setScheduledAt(date('Y-m-d H:i:s', $this->getCronTimestamp()))
                ->setExecutedAt(date('Y-m-d H:i:s', $this->getCronTimestamp()))
                ->save();

            // Execute
            $model = $this->objectManager->create($instance, []);
            $model->{$method}($schedule);

            // Save status
            $schedule
                ->setStatus(Schedule::STATUS_SUCCESS)
                ->setFinishedAt(date('Y-m-d H:i:s', $this->getCronTimestamp()))
                ->save();
        } catch (\Exception $e) {
            // Save status
            $schedule
                ->setStatus(Schedule::STATUS_ERROR)
                ->setMessages($e->getMessage())
                ->setFinishedAt(date('Y-m-d H:i:s', $this->getCronTimestamp()))
                ->save();
            return false;
        }
        return true;
    }

    /**
     * Get Time
     *
     * @return int
     */
    public function getCronTimestamp()
    {
        return $this->timezone->scopeTimeStamp();
    }
}
