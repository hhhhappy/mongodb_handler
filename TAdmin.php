<?php
/**
 * Created by PhpStorm.
 * User: Jiawei
 * Date: 2018/4/10
 * Time: 9:55
 */

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use Application\Models\Base\ModelHandler;
use Application\Models\Traits\ModelTraitBase;

class TAdmin extends ModelHandler
{

    use ModelTraitBase;

    /**
     *
     * @var ObjectId
     */
    public $_id;

    /**
     *
     * @var string
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @var integer
     */
    public $status;


    /**
     * 以用戶名查询，返回有效用户全信息
     *
     * @param string $id
     * @return TAdmin
     */
    public static function findByUserId($id)
    {
        $query = array("user_id" => $id);
        return parent::findByQuery($query);
    }

    /**
     * 查询多个用戶，不返回私密信息
     *
     * @param array $data
     * @param integer $start_index
     * @param integer $get_count
     * @return array
     */
    public static function search($data, $start_index = null, $get_count = null)
    {
        $query = array();
        $option = array();

        if (isset($data['status'])) {
            $query["status"] = $data['status'];
        }

        if (isset($data['name'])) {
            $query["name"] = new Regex('.*'.$data['name'].'.*', 'i');
        }

        if(isset($data['field']) && isset($data['order'])){
            $option['sort'] = [$data['field'] => $data['order']];
        }

        if(!empty($get_count)){
            $option['limit'] = $get_count;
            $option['skip'] = $start_index;
        }

        $option['projection'] = [
            'password' => 0,
        ];

        return parent::findAllByQueryStringId($query, $option);
    }

    /**
     * 返回用户数
     *
     * @param array $data
     * @return integer
     */
    public static function searchCount($data)
    {
        $query = array();
        $option = array();

        if (isset($data['status'])) {
            $query["status"] = $data['status'];
        }

        if (isset($data['name'])) {
            $query["name"] = new Regex('.*'.$data['name'].'.*', 'i');
        }

        return parent::countByQuery($query, $option);
    }

    /**
     * 查询所有文档
     * @return array
     */
    public static function list()
    {
        $query = array();
        $option['projection'] = [
            'password' => 0,
        ];

        return parent::findAllByQueryStringId($query, $option);
    }

    /**
     * 以用戶名查询, 不返回私密信息
     *
     * @param string $id
     * @return TAdmin
     */
    public static function GetByUserId($id)
    {
        $query = array("user_id" => $id);
        $option['projection'] = [
            'password' => 0,
        ];
        return parent::findByQuery($query, $option);
    }

    /**
     * 以邮箱查询
     *
     * @param string $email
     * @return TAdmin
     */
    public static function findByEmail($email)
    {
        $query = array(
            "email" => $email,
        );
        return parent::findByQuery($query);
    }

    /**
     * 以邮箱,手机或者账号查询
     *
     * @param string $account
     * @return TAdmin
     * if exit, return Object, if not, return null
     */
    public static function findByAccount($account)
    {
        $query = array(
            '$or' => [
                ["email" => $account],
                ["user_id" => $account]
            ],
        );
        return parent::findByQuery($query);
    }

    /**
     * 假删除
     *
     * @param ObjectID $id
     * @return bool
     */
    public static function falseDeleteById($id)
    {
        $query = [
            '_id' => $id,
        ];
        $set = [
            '$set' => [
                'status' => 0,
            ]
        ];

        return parent::updateByQuery($query, $set);
    }

    /**
     * 以手机查询
     *
     * @param string $mobile
     * @return TAdmin
     * if exit, return Object, if not, return null
     */
    public static function findByMobile($mobile)
    {
        $query = array(
            "mobile" => $mobile,
        );
        return parent::findByQuery($query);
    }
}