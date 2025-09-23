<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    public function findUserById($id) {
        $sql = 'SELECT * FROM users WHERE id = '.$id;
        $user = $this->select($sql);

        return $user;
    }

    public function findUser($keyword) {
        $sql = 'SELECT * FROM users WHERE user_name LIKE %'.$keyword.'%'. ' OR user_email LIKE %'.$keyword.'%';
        $user = $this->select($sql);

        return $user;
    }

    /**
     * Authentication user
     * @param $userName
     * @param $password
     * @return array
     */
public function auth($usernameOrEmail, $password) {
        // Convert password sang MD5 (DB bạn dùng MD5)
        $md5Password = md5($password);

        // Escape dữ liệu
        $usernameOrEmailEscaped = mysqli_real_escape_string(self::$_connection, $usernameOrEmail);
        $md5PasswordEscaped = mysqli_real_escape_string(self::$_connection, $md5Password);

        // Query chuẩn theo DB schema
        $sql = "SELECT * FROM users 
                WHERE (name = '$usernameOrEmailEscaped' OR email = '$usernameOrEmailEscaped') 
                  AND password = '$md5PasswordEscaped'
                LIMIT 1";

        $result = $this->select($sql);

        if (!empty($result)) {
            return $result; // Trả về user
        }

        return null; // Login failed
    }





    /**
     * Delete user by id
     * @param $id
     * @return mixed
     */
    public function deleteUserById($id) {
        $sql = 'DELETE FROM users WHERE id = '.$id;
        return $this->delete($sql);

    }

    /**
     * Update user
     * @param $input
     * @return mixed
     */
    public function updateUser($input) {
        $sql = 'UPDATE users SET 
                 name = "' . mysqli_real_escape_string(self::$_connection, $input['name']) .'", 
                 password="'. md5($input['password']) .'"
                WHERE id = ' . $input['id'];

        $user = $this->update($sql);

        return $user;
    }

    /**
     * Insert user
     * @param $input
     * @return mixed
     */
   public function insertUser($input) {
    // Các cột bắt buộc với giá trị mặc định
    $defaults = [
        'name' => 'Unknown',
        'fullname' => 'Unknown',
        'email' => 'unknown@example.com',
        'password' => md5('123456'),
        'type' => 'user'
    ];

    // Chỉ giữ các cột hợp lệ
    $data = array_merge($defaults, $input);
    $data = array_intersect_key($data, $defaults); // bỏ các key không tồn tại trong defaults (ví dụ 'submit')

    // Escape dữ liệu và tạo SQL
    $columns = [];
    $values = [];
    foreach ($data as $key => $value) {
        $columns[] = "`$key`";
        $values[] = "'" . mysqli_real_escape_string(self::$_connection, $value) . "'";
    }

    $sql = "INSERT INTO `users` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ")";
    return $this->insert($sql);
}





    /**
     * Search users
     * @param array $params
     * @return array
     */
    public function getUsers($params = []) {
        //Keyword
        if (!empty($params['keyword'])) {
            $sql = 'SELECT * FROM users WHERE name LIKE "%' . $params['keyword'] .'%"';

            //Keep this line to use Sql Injection
            //Don't change
            //Example keyword: abcef%";TRUNCATE banks;##
            $users = self::$_connection->multi_query($sql);

            //Get data
            $users = $this->query($sql);
        } else {
            $sql = 'SELECT * FROM users';
            $users = $this->select($sql);
        }

        return $users;
    }
}