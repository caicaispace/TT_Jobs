<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;
use Core\Component\Error\Trigger;
use Exception;

/**
 * Class Admin.
 */
class Admin extends Model
{
    public const DELETED    = 1;
    public const UN_DELETED = 0;

    public array $snapshotData    = [];
    protected $autoWriteTimestamp = true;

    public function setSnapshotData(array $snapshotData = []): Admin
    {
        if (empty($snapshotData)) {
            $snapshotData = $this->toArray();
        }
        $this->snapshotData = $snapshotData;
        return $this;
    }

    /**
     * @param null $field
     */
    public function getSnapshotData($field = null): ?array
    {
        if (empty($this->snapshotData)) {
            return null;
        }
        if ($field === null) {
            return $this->snapshotData;
        }
        if (isset($this->snapshotData[$field]) === false) {
            return null;
        }
        return $this->snapshotData[$field];
    }

    protected function initialize()
    {
        parent::initialize();
        $this->setSnapshotData();
        self::beforeInsert(function () {
            $this->_restPassword();
        });
        self::beforeUpdate(function () {
            try {
                if (
                    ($this->getData('password') != '')
                    and ($this->getData('password') != $this->getSnapshotData('password'))
                ) {
                    $this->_restPassword();
                }
            } catch (Exception $e) {
            }
        });
    }

    private function _restPassword()
    {
        try {
            if ($this->getData('password')) {
                $this->setAttr('password', md5($this->getData('password')));
            }
        } catch (Exception $e) {
            Trigger::exception($e);
        }
    }
}
