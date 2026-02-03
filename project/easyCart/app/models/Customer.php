<?php
/**
 * Customer Model
 */

require_once __DIR__ . '/../config/database.php';

class Customer {
    
    /**
     * Create a new customer
     */
    public static function create($data) {
        try {
            $db = Database::connect();
            $sql = "INSERT INTO customer_entity (firstname, lastname, email, password_hash, phone) 
                    VALUES (:firstname, :lastname, :email, :password_hash, :phone)";
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'firstname'     => $data['firstname'],
                'lastname'      => $data['lastname'],
                'email'         => $data['email'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'phone'         => $data['phone'] ?? null
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get customer by email
     */
    public static function getByEmail($email) {
        try {
            $db = Database::connect();
            $sql = "SELECT * FROM customer_entity WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Check if email already exists
     */
    public static function emailExists($email) {
        $customer = self::getByEmail($email);
        return $customer !== false;
    }
}
