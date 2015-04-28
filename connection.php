<?php
    require_once 'pdo_connection.php';

    if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['type']) && !empty($_POST['type'])) {

        $username   = htmlspecialchars($_POST['username']);
        $password   = hash('sha256', htmlspecialchars($_POST['password']));
        $type       = htmlspecialchars($_POST['type']);

        if (preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {

            if ($type == 'signin') {
                $query = pdo_connection::getPdo()->prepare("SELECT * FROM utilisateur WHERE login_utilisateur = :username AND password_utilisateur = :password");
                $query->execute(array(
                    ':username' => $username,
                    ':password' => $password
                ));
                $result = $query->fetch();

                if ($result !== false) {
                    $_SESSION['username'] = $username;
                }
            }
            elseif ($type == 'signup') {
                $query = pdo_connection::getPdo()->prepare("INSERT INTO utilisateur (login_utilisateur, password_utilisateur ) VALUES (:username, :password)");
                $success = $query->execute(array(
                    ':username' => $username,
                    ':password' => $password
                ));

                if ($success) {
                    $_SESSION['username'] = $username;
                }
            }
        }
    }