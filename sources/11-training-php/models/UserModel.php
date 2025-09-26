<?php
require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $rows = $this->selectPrepared($sql, 'i', [(int)$id]);
        return $rows;
    }

    public function findUser($keyword) {
        $kw = '%' . $keyword . '%';
        $sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ?";
        $rows = $this->selectPrepared($sql, 'ss', [$kw, $kw]);
        return $rows;
    }

    /**
     * Authentication user
     * @param $usernameOrEmail
     * @param $password
     * @return array|null
     */
    public function auth($usernameOrEmail, $password) {
        // Nếu DB đang lưu MD5, giữ md5 để tương thích. KHuyến nghị migrate sang password_hash.
        $md5Password = md5($password);

        $sql = "SELECT * FROM users 
                WHERE (name = ? OR email = ?) 
                  AND password = ?
                LIMIT 1";
        $rows = $this->selectPrepared($sql, 'sss', [$usernameOrEmail, $usernameOrEmail, $md5Password]);

        if (!empty($rows)) {
            return $rows; // trả mảng user
        }
        return null;
    }

    public function deleteUserById($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->executePrepared($sql, 'i', [(int)$id]);
    }

    public function updateUser($input) {
        // Expect input contains id, name, fullname, email, maybe password
        $id = (int)($input['id'] ?? 0);
        $name = $input['name'] ?? '';
        $fullname = $input['fullname'] ?? '';
        $email = $input['email'] ?? '';

        if (isset($input['password']) && $input['password'] !== '') {
            $password = md5($input['password']);
            $sql = "UPDATE users SET name = ?, fullname = ?, email = ?, password = ? WHERE id = ?";
            return $this->executePrepared($sql, 'ssssi', [$name, $fullname, $email, $password, $id]);
        } else {
            $sql = "UPDATE users SET name = ?, fullname = ?, email = ? WHERE id = ?";
            return $this->executePrepared($sql, 'sssi', [$name, $fullname, $email, $id]);
        }
    }

    public function insertUser($input) {
        // Các cột mặc định
        $defaults = [
            'name' => 'Unknown',
            'fullname' => 'Unknown',
            'email' => 'unknown@example.com',
            'password' => md5('123456'),
            'type' => 'user'
        ];

        $data = array_merge($defaults, $input);
        // Chuẩn bị và chạy prepared insert
        $sql = "INSERT INTO users (name, fullname, email, type, password) VALUES (?, ?, ?, ?, ?)";
        return $this->executePrepared($sql, 'sssss', [
            $data['name'],
            $data['fullname'],
            $data['email'],
            $data['type'],
            $data['password']
        ]);
    }

    /**
     * Search users (safe)
     */
    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $kw = '%' . $params['keyword'] . '%';
            $sql = "SELECT * FROM users WHERE name LIKE ? OR fullname LIKE ? OR email LIKE ?";
            return $this->selectPrepared($sql, 'sss', [$kw, $kw, $kw]);
        } else {
            $sql = "SELECT * FROM users";
            return $this->selectPrepared($sql);
        }
    }
}
