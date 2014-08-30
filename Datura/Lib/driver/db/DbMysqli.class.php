<?php

/**
 * DB操作类库，使用效率更高、功能更强大的mysqli扩展
 * mysql扩展在PHP5.5已经不推荐使用
 */
class DbMysqli {

    private $dbLink;
    protected $queryNum = 0;
    private static $instance;
    private $sqls; //用来拼接的SQL

    private function __construct($config) {
        $this->connect($config);
    }

    public static function getInstance($config) {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function connect($config) {
        $this->dbLink=mysqli_init();
        $this->dbLink->real_connect($config['host'], $config['login'], $config['password'], $config['database'], $config['port'] ? intval($config['port']) : 3306,'',MYSQLI_CLIENT_FOUND_ROWS);
        if ($this->dbLink->connect_error) {
            throw new BaseException("数据库连接失败", 3001);
        }
        if ($config['charset']) {
            $this->dbLink->query("SET NAMES '{$config['charset']}',sql_safe_updates=1;");
        }
        if($config['dummy']==1){
            //开启傻瓜模式，防止UPDATE和DELETE不带where条件造成不良后果
            $this->dbLink->query("SET sql_safe_updates=1;");
        }
        return $this->dbLink;
    }

    public function query($sql) {
        if (!$this->dbLink)
            return false;
        $this->result = $this->dbLink->query($sql);
        $this->queryNum++;
        return $this->result;
    }

    /**
     * 异步查询，仅mysqlnd驱动下可用,性能更佳
     * @param type $sql
     * @return mixed
     */
    public function queryAsync($sql) {
        if (!$this->dbLink)
            return false;
        $this->result = $this->dbLink->query($this->dbLink, $sql, MYSQLI_ASYNC);
        $this->queryNum++;
        return $this->result;
    }

    /**
     * 执行SQL，用于手工构造的SQL
     * @param type $sql
     */
    public function execute($sql) {
        $this->query($sql);
    }

    public function getAll($sql) {
        $result = array();
        $res = $this->query($sql);
        while ($rows = $res->fetch_assoc()) {
            $result[] = $rows;
        }
        $res->close();
        return $result;
    }

    /**
     * 返回单个记录行
     * @param type $sql
     * @return null
     */
    public function getOne($sql) {
        $result = array();
        $res = $this->query($sql);
        while ($rows = $res->fetch_assoc()) {
            $result[] = $rows;
        }
        $res->close();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    /**
     * 向表中插入数据
     * @param type $table
     * @param type $data
     * @return type
     */
    public function insert($table, $data) {
        $keys = implode(',', array_keys($data));
        $param = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `{$table}` ({$keys}) VALUES ({$param})";
        $stmt = $this->dbLink->prepare($sql);
        if (!$stmt)
            return FALSE;
        call_user_func_array(array($stmt, 'bind_param'), array_merge(self::typeValue($data), self::refValues($data)));
        $stmt->execute();
        return $this->dbLink->insert_id;
    }

    /**
     * 为了防止误操作，此处update强制必须带WHERE语句，否则不执行
     * 如果确实不需要带WHERE条件，则可使用execute函数,同时也受数据库配置的限制
     * @param type $table
     * @param type $data
     * @param type $where
     * @return type
     */
    public function update($table, $data, $where) {
        if ($where == null || func_get_arg(2) == null)
            return;
        $fields = array();
        foreach ($data as $key => $value) {
            if (!is_numeric($value)) {
                $fields[] = $key . ' = \'' . addslashes($value) . '\'';
            } else {
                $fields[] = $key . ' = ' . $value;
            }
        }
        $this->execute('UPDATE ' . $table . ' SET ' . implode(',', $fields) . ' WHERE ' . $where);
        return $this->dbLink->affected_rows;
    }

    public function find($find = '*') {
        if ($find == '*') {
            $this->sqls = 'SELECT * ';
        } elseif (is_string($find)) {
            $this->sqls = 'SELECT  ' . $find . ' ';
        } else {
            $this->sqls = 'SELECT ' . implode(',', $find);
        }
        return $this;
    }

    public function from($table) {
        $this->sqls.=" FROM `{$table}` ";
        return $this;
    }

    public function where($condition = '') {
        if ($condition == '') {
            $this->sqls.='';
        } else {
            $this->sqls.=' WHERE ' . $condition;
        }
        return $this->getAll($this->sqls);
    }

    public function beginTrans() {
        $this->dbLink->autocommit(false);
    }

    public function commit() {
        $result = $this->dbLink->commit();
        $this->dbLink->autocommit(true);
        if (!$result) {
            throw new BaseException('事务提交失败', 3002);
        }
    }

    public function rollback() {
        $result = $this->dbLink->rollback();
        if (!$result) {
            throw new BaseException('事务回滚失败', 3003);
        }
    }

    public function close() {
        $this->dbLink->close();
        unset($this->dbLink);
    }
    
    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            $func($args);
        }
    }
    

    /**
     * 获取数据的type，用于绑定
     * @param type $data
     * @return string
     */
    public static function typeValue($data) {
        $type = '';
        foreach ($data as $v) {
            if (is_int($v)) {
                $type.='i';
            } elseif (is_double($v)) {
                $type.='d';
            } else {
                $type.='s';
            }
        }
        return array($type);
    }

    /**
     * 获取data引用，用于参数绑定
     * @param type $arr
     * @return type
     */
    public static function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //PHP 5.3+需要传引用
            $refs = array();
            foreach ($arr as $key => $v){
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

}
