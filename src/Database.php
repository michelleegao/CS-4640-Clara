<?php
require_once __DIR__.'/Config.php';

class Database {
  private static ?\PDO $pdo = null;
  public static function pdo(): \PDO {
    if (self::$pdo) return self::$pdo;
    $c = Config::$db;
    $dsn = "pgsql:host={$c['host']};port={$c['port']};dbname={$c['database']}";
    self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
    return self::$pdo;
  }
}
