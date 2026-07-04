<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/database.php';

/**
 * Login Admin
 */
function login($username, $password)
{
    global $conn;

    $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ? LIMIT 1");

    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        if (password_verify($password, $user['password'])) {

            $_SESSION['admin'] = [
                'id'       => $user['id'],
                'username' => $user['username'],
                'nama'     => $user['nama_lengkap'],
                'peran'    => $user['peran']
            ];
            return true;
        }
    }

    return false;
}

/**
 * Cek apakah admin sudah login
 */
function isLoggedIn()
{
    return isset($_SESSION['admin']);
}

/**
 * Lindungi halaman admin
 */
function checkLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Logout
 */
function logout()
{
    session_unset();
    session_destroy();

    header("Location: login.php");
    exit;
}