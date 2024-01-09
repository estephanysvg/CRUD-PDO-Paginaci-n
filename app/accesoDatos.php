<?php
include_once "cliente.php";
include_once "config.php";

class AccesoDatos
{
    private static $modelo = null;
    private $dbh = null;

    public static function getModelo()
    {
        if (self::$modelo == null) {
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DATABASE;
            $this->dbh = new PDO($dsn, DB_USER, DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    public static function closeModelo()
    {
        if (self::$modelo != null) {
            self::$modelo = null;
        }
    }

    public function numClientes(): int
    {
        $stmt = $this->dbh->query("SELECT id FROM Clientes");
        $num = $stmt->rowCount();
        return $num;
    }

    public function getClientes($primero, $cuantos): array
    {
        $tuser = [];
        $stmt = $this->dbh->prepare("SELECT * FROM clientes LIMIT :primero, :cuantos");
        $stmt->bindParam(':primero', $primero, PDO::PARAM_INT);
        $stmt->bindParam(':cuantos', $cuantos, PDO::PARAM_INT);
        $stmt->execute();

        while ($user = $stmt->fetchObject('Cliente')) {
            $tuser[] = $user;
        }

        return $tuser;
    }

    public function getCliente(int $login)
    {
        $user = false;

        $stmt = $this->dbh->prepare("SELECT * FROM clientes WHERE id = :id");
        $stmt->bindParam(':id', $login, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetchObject('Cliente');

        return $user;
    }

    public function modUsuario($user): bool
    {
        $stmt = $this->dbh->prepare("UPDATE clientes SET first_name=?, email=?, gender=?, ip_address=?, telefono=? WHERE id=?");
        $stmt->bindParam(1, $user->first_name);
        $stmt->bindParam(2, $user->email);
        $stmt->bindParam(3, $user->gender);
        $stmt->bindParam(4, $user->ip_address);
        $stmt->bindParam(5, $user->telefono);
        $stmt->bindParam(6, $user->id);

        $stmt->execute();
        $resu = ($stmt->rowCount() == 1);

        return $resu;
    }

    public function addUsuario($user): bool
    {
        $stmt = $this->dbh->prepare("INSERT INTO clientes (id, first_name, email, gender, ip_address, telefono) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $user->id);
        $stmt->bindParam(2, $user->first_name);
        $stmt->bindParam(3, $user->email);
        $stmt->bindParam(4, $user->gender);
        $stmt->bindParam(5, $user->ip_address);
        $stmt->bindParam(6, $user->telefono);

        $stmt->execute();
        $resu = ($stmt->rowCount() == 1);

        return $resu;
    }

    public function borrarUsuario(int $login): bool
    {
        $stmt = $this->dbh->prepare("DELETE FROM clientes WHERE id = :id");
        $stmt->bindParam(':id', $login, PDO::PARAM_INT);

        $stmt->execute();
        $resu = ($stmt->rowCount() == 1);

        return $resu;
    }

    public function __clone()
    {
        trigger_error('La clonación no está permitida', E_USER_ERROR);
    }
}
