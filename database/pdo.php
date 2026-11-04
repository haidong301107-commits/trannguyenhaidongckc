<?php

class DB
{
    /** @var \PDO|null */
    private static $pdo = null;

    /**
     * Create and return a PDO instance (private helper).
     * Loads config dynamically so this file can be required from anywhere.
     * @return \PDO
     */
    private static function connect_pdo(): \PDO
    {
        $host = DB_HOST;
        $user = DB_USERNAME;
        $pass = DB_PASSWORD;
        $db   = DB_NAME;
        $charset = DB_CHARSET;
        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        try {
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            return new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // don't expose DB details in production; this is a dev helper
            die('Kết nối PDO thất bại: ' . $e->getMessage());
        }
    }

    private static function init()
    {
        if (self::$pdo === null) {
            self::$pdo = self::connect_pdo();
        }
    }

    /**
     * Return underlying PDO instance
     * @return \PDO
     */
    public static function getPdo()
    {
        self::init();
        return self::$pdo;
    }

    /**
     * Execute a SELECT and return all rows (assoc)
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a SELECT and return a single row or null
     */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Execute an INSERT/UPDATE/DELETE SQL with params and return affected rows
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Insert helper: $data is associative array column => value
     * Returns lastInsertId (string)
     */
    public static function insert(string $table, array $data)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Data array is empty for insert');
        }
        $cols = array_keys($data);
        $placeholders = array_map(function ($c) {
            return ':' . $c;
        }, $cols);
        $sql = sprintf('INSERT INTO `%s` (`%s`) VALUES (%s)', $table, implode('`,`', $cols), implode(',', $placeholders));
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($data);
        return self::getPdo()->lastInsertId();
    }

    /**
     * Update helper: $data is associative array column=>value, $where is SQL condition with placeholders, $whereParams are params for where
     * Returns number of affected rows
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Data array is empty for update');
        }
        $setParts = [];
        $params = [];
        foreach ($data as $col => $val) {
            $setParts[] = "`$col` = :upd_$col";
            $params["upd_$col"] = $val;
        }
        // merge where params (they should not conflict with upd_ prefix)
        $params = array_merge($params, $whereParams);
        $sql = sprintf('UPDATE `%s` SET %s WHERE %s', $table, implode(', ', $setParts), $where);
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Delete helper: $where is SQL condition with placeholders, $whereParams are params
     * Returns number of affected rows
     */
    public static function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, $where);
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($whereParams);
        return $stmt->rowCount();
    }

    // Transaction helpers
    public static function beginTransaction(): bool
    {
        return self::getPdo()->beginTransaction();
    }
    public static function commit(): bool
    {
        return self::getPdo()->commit();
    }
    public static function rollBack(): bool
    {
        return self::getPdo()->rollBack();
    }
}