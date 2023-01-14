<?php

namespace TheMageLover\AdminCronManagement\Helper;

use Magento\Cron\Model\ConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * Data Helper for Cron Management
 */
class Data extends AbstractHelper
{
    /** @var ConfigInterface */
    protected $cronConfig;

    /** @var WriterInterface */
    protected $configWriter;

    /**
     * @param Context $context
     * @param ConfigInterface $cronConfig
     * @param WriterInterface $configWriter
     */
    public function __construct(Context $context, ConfigInterface $cronConfig, WriterInterface $configWriter)
    {
        $this->cronConfig = $cronConfig;
        $this->configWriter = $configWriter;
        parent::__construct($context);
    }

    /**
     * Get All Jobs
     *
     * @return array
     */
    public function getAllJobs()
    {
        $result = [];
        $allJobs = $this->cronConfig->getJobs();
        foreach ($allJobs as $group => $jobs) {
            foreach ($jobs as $code => $jobData) {
                if (!isset($jobData['instance'], $jobData['method'])) {
                    continue;
                }

                $result[] = [
                    'name' => $code,
                    'group' => $group,
                    'status' => $jobData['status'] ?? '1',
                    'instance' => $jobData['instance'],
                    'method' => $jobData['method'],
                    'schedule' => $this->getSchedule($jobData) ?? ''
                ];
            }
        }

        return $result;
    }

    /**
     * Get Job Schedule
     *
     * @param array $job
     * @return string
     */
    public function getSchedule($job)
    {
        if (isset($job['schedule'])) {
            return $job['schedule'];
        }
        if (isset($job['config_path'])) {
            return $this->scopeConfig->getValue($job['config_path'], ScopeInterface::SCOPE_STORE);
        }
        return '';
    }

    /**
     * Get Job by Job Name
     *
     * @param string $jobName
     * @return array|null
     */
    public function getJobByName($jobName)
    {
        $allJobs = $this->getAllJobs();
        foreach ($allJobs as $job) {
            if ($job['name'] == $jobName) {
                return $job;
            }
        }
        return null;
    }

    /**
     * Update Job Status
     *
     * @param string $jobName
     * @param string $status
     * @return bool
     */
    public function updateJobStatus($jobName, $status)
    {
        $job = $this->getJobByName($jobName);

        // Check required values
        if ($job == null || $job['group'] = null || $job['name'] == null) {
            return false;
        }

        // Save path
        $path = 'crontab' . '/' . $job['group'] . '/' . 'jobs' . '/' . $job['name'] . '/' . 'status';
        $this->configWriter->save($path, $status);
        return true;
    }

    /**
     * Get selected jobs
     *
     * @param array $data
     * @return array
     */
    public function getSelectedRecords($data)
    {
        // Selected records
        if (isset($data['selected'])) {
            return $data['selected'];
        }

        $allJobs = $this->getAllJobs();

        // Has excluded value
        if (isset($data['excluded']) && $data['excluded'] !== 'false') {
            $result = [];
            $excluded = $data['excluded'];
            foreach ($allJobs as $job) {
                if (!in_array($job['name'], $excluded)) {
                    $result[] = $job['name'];
                }
            }
            return $result;
        }

        // Filter to get the records
        $filters = (array)$data['filters'];
        unset($filters['placeholder']);
        foreach ($filters as $column => $value) {
            $allJobs = array_filter($allJobs, function ($item) use ($column, $value) {
                if (is_array($value)) {
                    return in_array($item[$column], $value);
                }
                return str_contains($item[$column], $value);
            });
        }
        return array_column($allJobs, 'name');
    }
}
