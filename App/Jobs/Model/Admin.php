<?php
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

    /**
     * @var array
     */
    public $snapshotData          = [];
    protected $autoWriteTimestamp = true;

    /**
     * @param array $snapshotData
     *
     * @return $this
     */
    public function setSnapshotData($snapshotData = [])
    {
        if (empty($snapshotData)) {
            $snapshotData = $this->toArray();
        }
        $this->snapshotData = $snapshotData;
        return $this;
    }

    /**
     * @param null $field
     *
     * @return null|array
     */
    public function getSnapshotData($field = null)
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
