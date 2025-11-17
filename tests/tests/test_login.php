<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {

    public function testLoginBerhasil() {
        
        $_POST['username'] = "admin";
        $_POST['password'] = "admin123";

        ob_start();
        include "login.php";
        $output = ob_get_clean();

        $this->assertStringContainsString("berhasil", $output);
    }

    public function testLoginGagal() {
        $_POST['username'] = "salah";
        $_POST['password'] = "salah";

        ob_start();
        include "login.php";
        $output = ob_get_clean();

        $this->assertStringContainsString("gagal", $output);
    }
}
