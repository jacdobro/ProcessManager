<?php
/**
 * Process Manager.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016 lineofcode.at (http://www.lineofcode.at)
 * @license    https://github.com/dpfaffenbauer/ProcessManager/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ProcessManager\Model;

use Pimcore\Logger;
use Pimcore\Model\AbstractModel;
use Psr\Log\LoggerInterface;

/**
 * Class Process
 * @package ProcessManager\Process
 */
class Process extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $message;

    /**
     * @var int
     */
    public $progress;

    /**
     * @var int
     */
    public $total;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * get Log by id
     *
     * @param $id
     * @return null|Process
     */
    public static function getById($id)
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        } catch (\Exception $ex) {
            Logger::warn(sprintf("Process with id %s not found", $id));
        }

        return null;
    }

    /**
     * Increase Process
     *
     * @param int $steps
     * @param string $message
     */
    public function progress($steps = 1, $message = '') {
        $this->setProgress($this->getProgress() + $steps);

        if($message) {
            $this->setMessage($message);
        }

        $this->save();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param int $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        if(is_null($this->logger)) {
            $loggerFile = new \Monolog\Logger('core');
            $loggerFile->pushHandler(new \Monolog\Handler\StreamHandler($this->getLogFilePath()));

            $this->logger = $loggerFile;
        }

        return $this->logger;
    }

    /**
     * @return string
     */
    public function getLogFilePath() {
        return PIMCORE_LOG_DIRECTORY . "/process-" . $this->getId() . ".log";
    }

    /**
     * @return float
     */
    public function getPercentage() {
        return ((100 / $this->getTotal()) * $this->getProgress()) / 100;
    }
}
