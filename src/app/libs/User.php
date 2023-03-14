<?php


namespace easyadmin\app\libs;


use JetBrains\PhpStorm\Pure;
use think\Exception;
use think\facade\Db;
use think\facade\Session;

class User
{

    const user_key = 'easyadmin_session_user';

    private $user;


    /**
     * 私有属性，用于保存实例
     * @var User
     */
    private static $instance;

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
        if (empty($this->user)) {
            $this->user = Session::get(self::user_key);
        }
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    /**
     * 公有方法，用于获取实例
     * @return User
     */
    public static function getInstance(): User
    {
        //判断实例有无创建，没有的话创建实例并返回，有的话直接返回
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * 存储用户
     * @param $user
     * @return array
     * @throws Exception
     */
    public function save($user): array
    {
        if (!isset($user['id'])) throw new Exception('缺少用户ID');
        if (!isset($user['username'])) throw new Exception('缺少用户名');

        if (empty($user['user_role'])) {
            $user['user_role'] = [];
        }

        if (!is_array($user['user_role'])) {
            $roles = unserialize($user['user_role']);
            $user['user_role'] = $roles ? $roles : [];
        }

        $user['user_role'] = array_unique($user['user_role']);

        $this->user = $user;

        Session::set(self::user_key, $user);
        return $user;
    }

    public function clear()
    {
        Session::delete(self::user_key);
    }

    /**
     * 获取用户ID
     * @return int|string
     */
    #[Pure] public function getUserId():int|string
    {
        $lib = new Lib();
        return $lib->getArrayValue($this->user, 'id');
    }

    /**
     * 获取用户信息
     * @param null $field
     * @return array|null
     */
    #[Pure] public function getUser($field = null): ?array
    {
        if ($field === null) {
            return $this->user;
        }

        $lib = new Lib();
        return $lib->getArrayValue($this->user, $field);
    }

    /**
     * 获取用户权限信息
     * @return array
     */
    public function getRoles(): array
    {
        $lib = new Lib();
        return $lib->getArrayValue($this->user, 'user_role', []);
    }


    /**
     * 设置用户的权限
     * @param array $roles
     * @return array
     * @throws Exception
     */
    public function setRoles(array $roles): array
    {
        $user = $this->getUser();
        $user['user_role'] = $roles;
        $this->save($user);

        Db::name(config('login.table_name'))->where('id', $this->getUserId())->update([
            'user_role' => serialize($roles)
        ]);

        return $roles;
    }

    /**
     * 添加权限, 返回添加后的权限列表
     * @param $role
     * @return array
     * @throws Exception
     */
    public function addRole($role): array
    {
        $roles = $this->getRoles();
        array_push($roles, strtolower($role));

        return $this->setRoles($roles);
    }

    /**
     * 删除一个权限
     * @param $role
     * @return array
     * @throws Exception
     */
    public function removeRole($role): array
    {
        $roles = $this->getRoles();

        $index = array_search($role, $roles);
        if ($index === false) {
            throw new Exception("{$role} 权限不存在");
        }

        unset($roles[$index]);

        return $this->setRoles($roles);
    }

    /**
     * 判断用户是否有这个权限
     * @param $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        $roles = $this->getRoles();

        $index = array_search(strtolower($role), $roles);
        return $index === false ? false : true;
    }

    /**
     * 加密密码
     * @param $username
     * @param $password
     * @return string
     */
    public function encrypt($username, $password): string
    {
        $md5 = md5($username . $password . config('login.crypt_salt'));
        $num = preg_replace('/\D/s', '', $md5);
        return sha1($md5 . $num) . $num;
    }

}
