<?php
require_once __DIR__ . '/../configs/database.php';

abstract class BaseModel {
    // Database connection
    protected static $_connection;

    public function __construct() {
        if (!isset(self::$_connection)) {
            // Khởi tạo connection an toàn hơn
            self::$_connection = mysqli_init();
            // Connect (suppress warning, handle error)
            if (!@mysqli_real_connect(self::$_connection, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT)) {
                error_log('DB connect error: ' . mysqli_connect_error());
                // Tùy: bạn có thể throw exception hoặc exit
                throw new \Exception('Database connection failed');
            }
            // Set proper charset to avoid charset-based bypass
            mysqli_set_charset(self::$_connection, 'utf8mb4');
        }
    }

    /**
     * Prepare + execute statement with optional params
     * $types: string like 'ssi' or '' nếu không có param
     * $params: array of values
     * return mysqli_stmt on success or false
     */
    protected function prepareAndExecute(string $sql, string $types = '', array $params = []) {
        $stmt = mysqli_prepare(self::$_connection, $sql);
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error(self::$_connection) . " SQL: $sql");
            return false;
        }

        if ($types !== '' && !empty($params)) {
            // bind_param needs references
            $bindNames[] = $types;
            for ($i = 0; $i < count($params); $i++) {
                $bindNames[] = &$params[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $bindNames);
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        return $stmt;
    }

    /**
     * Select statement with prepared statement -> returns array
     */
    protected function selectPrepared(string $sql, string $types = '', array $params = []) {
        $stmt = $this->prepareAndExecute($sql, $types, $params);
        if (!$stmt) return [];
        $res = mysqli_stmt_get_result($stmt);
        $rows = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $rows[] = $row;
            }
            mysqli_free_result($res);
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    /**
     * Execute (INSERT / UPDATE / DELETE) prepared -> returns boolean / affected rows
     */
    protected function executePrepared(string $sql, string $types = '', array $params = []) {
        $stmt = $this->prepareAndExecute($sql, $types, $params);
        if (!$stmt) return false;
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected;
    }

    // Legacy helpers (if some code calls them)
    protected function query($sql) {
        return mysqli_query(self::$_connection, $sql);
    }

    protected function select($sql) {
        // fallback (only used if existing code still calls select with raw SQL)
        $res = $this->query($sql);
        $rows = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
        }
        return $rows;
    }

    protected function insert($sql) { return $this->query($sql); }
    protected function update($sql) { return $this->query($sql); }
    protected function delete($sql) { return $this->query($sql); }
}
