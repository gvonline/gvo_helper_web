<?php

class Database
{
    private $hasTraderCities = array();
    private $goodsType = array();
    private $goodsSaleCities = array();
    private $goodsResistCities = array();
    private $sqlite = NULL;

    function __construct()
    {
        $databasePath = 'sqlite:'.__DIR__.DIRECTORY_SEPARATOR.'gvonline.sqlite3';
        $this->sqlite = new PDO($databasePath);
    }

    public function getHasTraderCities()
    {
        if ($this->sqlite === NULL) {
            return FALSE;
        }

        if (count($this->hasTraderCities) != 0) {
            return $this->hasTraderCities;
        }

        $query = "-- 교역소가 있는 도시 목록
            SELECT city_name
            FROM goods_of_city
            GROUP BY city_name
            ORDER BY city_name ASC;";
        $statement = $this->sqlite->prepare($query);
        $statement->execute();

        $result = array();
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row['city_name'];
        }

        $this->hasTraderCities = $result;
        return $result;
    }

    public function getGoodsType($goodsName='')
    {
        if ($this->sqlite === NULL) {
            return FALSE;
        }

        $goodsName = trim($goodsName);
        if (strlen($goodsName) == 0) {
            return FALSE;
        }

        if (array_key_exists($goodsName, $this->goodsType)) {
            return $this->goodsType[$goodsName];
        }

        $query = "-- 교역품에 대한 품목 목록
            SELECT type
            FROM goods
            WHERE name = :name";
        $statement = $this->sqlite->prepare($query);
        $statement->bindParam(':name', $goodsName);
        $statement->execute();

        $result = '';
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result = $row['type'];
        }

        $this->goodsType[$goodsName] = $result;
        return $result;
    }

    public function getGoodsSaleCities($goodsName='')
    {
        if ($this->sqlite === NULL) {
            return FALSE;
        }

        $goodsName = trim($goodsName);
        if (strlen($goodsName) == 0) {
            return FALSE;
        }

        if (array_key_exists($goodsName, $this->goodsSaleCities)) {
            return $this->goodsSaleCities[$goodsName];
        }

        $query = "-- 교역품에 대한 도시 목록
            SELECT city_name
            FROM goods_of_city
            WHERE goods_name = :name
            GROUP BY city_name
            ORDER BY city_name ASC;";
        $statement = $this->sqlite->prepare($query);
        $statement->bindParam(':name', $goodsName);
        $statement->execute();

        $result = array();
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row['city_name'];
        }

        $this->goodsSaleCities[$goodsName] = $result;
        return $result;
    }

    public function getGoodsResistCities($goodsName='')
    {
        if ($this->sqlite === NULL) {
            return FALSE;
        }

        $goodsName = trim($goodsName);
        if (strlen($goodsName) == 0) {
            return FALSE;
        }

        if (array_key_exists($goodsName, $this->goodsResistCities)) {
            return $this->goodsResistCities[$goodsName];
        }

        $query = "-- 교역품에 대한 내성 도시 목록
            SELECT goods_of_city.city_name
            FROM goods
            LEFT OUTER JOIN goods_of_city
            ON goods.type = goods_of_city.goods_type
            WHERE goods.name = :name
            GROUP BY goods_of_city.city_name
            ORDER BY goods_of_city.city_name ASC;";
        $statement = $this->sqlite->prepare($query);
        $statement->bindParam(':name', $goodsName);
        $statement->execute();

        $result = array();
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row['city_name'];
        }

        $this->goodsResistCities[$goodsName] = $result;
        return $result;
    }
}