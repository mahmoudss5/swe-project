<?php
require_once 'models/UserModel.php';

class UserController {
    private $model;

    public function __construct($conn) {
        $this->model = new UserModel($conn);
    }

    public function profile($userId) {
        $userPoints = $this->model->getUserPoints($userId);
        $userBadges = $this->model->getUserBadges($userId);

        // send to view
        require 'views/userprofile - Copy.php';
    }
}
?>
