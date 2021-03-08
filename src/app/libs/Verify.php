<?php

namespace easyadmin\app\libs;

/**
 * 表单验证类
 * Class Verify
 * @package easyadmin\app\libs
 */
class Verify
{

    /**
     * 验证规则存放
     * @var array
     */
    private $rules = [];


    /**
     * 添加验证规则
     * @param string $rule 验证规则
     * @param string $msg 提示消息
     * @param void $opt 进一步判断的值
     * @return Verify
     */
    public function addRule(string $rule, string $msg, $opt = null): Verify
    {
        array_push($this->rules, [
            'rule' => $rule,
            'msg' => $msg,
            'opt' => $opt,
        ]);
        return $this;
    }

    /**
     * 获取全部的验证规则
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }


    /**
     * 验证
     * @param $value
     * @return bool|string
     */
    public function verify($value)
    {
        foreach ($this->getRules() as $item) {
            if ($this->checkVal($item['rule'], $value, $item['opt']) !== true) {
                return $item['msg'];
            }
        }

        return true;
    }

    /**
     * 验证值是否正确
     * @param $rule
     * @param $value
     * @param $opt
     * @return bool
     */
    protected function checkVal($rule, $value, $opt): bool
    {
        switch ($rule) {
            case 'reg':
                if (!preg_match($opt, $value)) return false;
                break;
            case 'chinese':
                if (!preg_match('/[\x80-\xff]/', $value)) return false;
                break;
            case 'username':
                if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/', $value)) return false;
                break;
            case 'mobile': //手机号码
                if (!preg_match('/^1(3[0-9]|4[01456879]|5[0-35-9]|6[2567]|7[0-8]|8[0-9]|9[0-35-9])\d{8}$/', $value)) return false;
                break;
            case 'phone_num'://座机号码
                if (!preg_match('/^(0\d{2,3})-?(\d{7,8})$/', $value)) return false;
                break;
            case 'idcard':
                if (!preg_match('/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $value)) return false;
                break;
            case 'number':
                if (!filter_var($value, FILTER_SANITIZE_NUMBER_INT)) return false;
                break;
            case 'float':
                if (!filter_var($value, FILTER_VALIDATE_FLOAT)) return false;
                break;
            case 'boolean':
                if (!filter_var($value, FILTER_VALIDATE_BOOLEAN)) return false;
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) return false;
                break;
            case 'url':
                if (!filter_var($value, FILTER_SANITIZE_URL)) return false;
                break;
            case 'ip':
                if (!filter_var($value, FILTER_VALIDATE_IP)) return false;
                break;
            case 'timestamp':
                if (!strtotime(date('Y-m-d H:i:s', $value)) == $value) return false;
                break;
            case 'date':
                if (!is_integer(strtotime($value))) return false;
                break;
            case 'max':
                if ($value > $opt) return false;
                break;
            case 'min':
                if ($value < $opt) return false;
                break;
            case 'maxlength':
                if (mb_strlen($value) > $opt) return false;
                break;
            case 'minlength':
                if (mb_strlen($value) < $opt) return false;
                break;
            default:
                break;

        }
        return true;
    }
}
