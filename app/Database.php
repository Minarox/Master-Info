<?php
declare(strict_types = 1);

namespace app;

use BadRequest;
use NotFound;
use PDO;
use PDOException;

/**
 * Connect and manage database information
 */
class Database
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Make new connection to database
     */
    public function __construct()
    {
        // Get information from .ini file
        $driver = CONFIG["database"]["driver"];
        $host   = CONFIG["database"]["host"];
        $port   = CONFIG["database"]["port"];
        $dbname = CONFIG["database"]["database"];

        // Make new connexion to database
        $pdo = new PDO(
            "$driver:host=$host; port=$port; dbname=$dbname",
            CONFIG["database"]["username"],
            CONFIG["database"]["password"]
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo = $pdo;
    }

    /**
     * Getter for Database object
     *
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Find occurrence by params
     *
     * @param string         $table
     * @param array|string[] $values
     * @param array          $selectors
     * @param bool|int       $findOne
     * @param string|null    $order
     * @param bool           $exception
     *
     * @return array|bool
     * @throws NotFound|BadRequest
     */
    public function find(string $table, array $values = ['*'], array $selectors = ['*'], bool|int $findOne = false, string $order = null, bool $exception = true): bool|array
    {
        // Setting up variables
        $selectors_keys = array_keys($selectors);
        $selectors_list = '';
        $i              = 0;

        // Sort selectors
        if ($selectors != ['*']) {
            foreach ($selectors as $selector) {
                if (count(explode(' ', $selectors_keys[$i])) > 1) {
                    $key             = explode(' ', $selectors_keys[$i]);
                    $selectors_list .= "$key[0] $key[1] '$selector' AND ";
                } else if ($this->isNull($selector)) {
                    $selectors_list .= "$selectors_keys[$i] IS NULL AND ";
                } else {
                    $selectors_list .= "$selectors_keys[$i] = '$selector' AND ";
                }
                $i++;
            }
            $selectors_list = rtrim($selectors_list, " AND ");
        }

        if ($selectors_list != '') {
            $selectors_list = "WHERE " . $selectors_list;
        }

        // Reformatting the values to display
        $values = implode(", ", $values);

        // Add or not an order by
        if ($order) {
            $order = "ORDER BY $order";
        }

        // Returns a single or multiple rows
        try {
            if (is_bool($findOne) && $findOne == true) {
                $data = $this->pdo
                    ->query("SELECT $values FROM $table $selectors_list $order LIMIT 1;")
                    ->fetch();
            } else if (is_bool($findOne) && $findOne == false) {
                $data = $this->pdo
                    ->query("SELECT $values FROM $table $selectors_list $order LIMIT 300;")
                    ->fetchAll();
            } else if (empty($findOne)) {
                $data = $this->pdo
                    ->query("SELECT $values FROM $table $selectors_list $order;")
                    ->fetchAll();
            } else {
                $data = $this->pdo
                    ->query("SELECT $values FROM $table $selectors_list $order LIMIT $findOne;")
                    ->fetchAll();
            }
        } catch (PDOException $e) {
            if ($exception) {
                throw new BadRequest("The database return an error when executing the query");
            } else {
                return false;
            }
        }

        if ($exception) {
            return $this->containsValues($data);
        }
        return $data;
    }

    /**
     * Insert new row
     *
     * @param string $table
     * @param array  $params
     * @param string $returnColumn
     *
     * @return array
     * @throws BadRequest
     * @throws NotFound
     */
    public function create(string $table, array $params, string $returnColumn = '*'): array
    {
        // Setting up variables
        $filteredParams = $this->filterArray($params);
        $fields_list    = implode(", ", array_keys($filteredParams));
        $values_list    = '';

        // Sort values
        foreach ($filteredParams as $value) {
            $values_list .= "'$value', ";
        }
        $values_list = rtrim($values_list, ", ");

        // Create a new record in the database
        return $this->containsValues(
            $this->pdo
                ->query("INSERT INTO $table ($fields_list) VALUES ($values_list) RETURNING $returnColumn;")
                ->fetch(),
            1
        );
    }

    /**
     * Update row
     *
     * @param string $table
     * @param array  $params
     * @param array  $selectors
     * @param string $returnColumn
     *
     * @return array
     * @throws BadRequest
     * @throws NotFound
     */
    public function update(string $table, array $params, array $selectors, string $returnColumn = '*'): array
    {
        // Setting up variables
        $filteredArray  = $this->filterArray($params);
        $fields_list    = array_keys($filteredArray);
        $selectors_keys = array_keys($selectors);
        $values_list    = $selectors_list = '';
        $i              = 0;

        // Sort selectors
        foreach ($selectors as $selector) {
            if (count(explode(' ', $selectors_keys[$i])) > 1) {
                $key             = explode(' ', $selectors_keys[$i]);
                $selectors_list .= "$key[0] $key[1] '$selector' AND ";
            } else if ($this->isNull($selector) || $selector == '') {
                $selectors_list .= "$selectors_keys[$i] IS NULL AND ";
            } else {
                $selectors_list .= "$selectors_keys[$i] = '$selector' AND ";
            }
            $i++;
        }

        // Sorts values to modify
        $i = 0;
        foreach ($filteredArray as $value) {
            if ($this->isNull($value) || $value == '') {
                $values_list .= "$fields_list[$i] = NULL, ";
            } else {
                $values_list .= "$fields_list[$i] = '$value', ";
            }
            $i++;
        }

        // Reformatting texts
        $values_list    = rtrim($values_list, ", ");
        $selectors_list = rtrim($selectors_list, " AND ");

        // Update the database
        return $this->containsValues(
            $this->pdo
                ->query("UPDATE $table SET $values_list WHERE $selectors_list RETURNING $returnColumn;")
                ->fetch(),
            1
        );
    }

    /**
     * Delete row with custom id format
     *
     * @param string $table
     * @param array  $selectors
     *
     * @return bool
     * @throws NotFound|BadRequest
     */
    public function delete(string $table, array $selectors): bool
    {
        // Setting up variables
        $selectors_keys = array_keys($selectors);
        $selectors_list = '';
        $i              = 0;

        // Sorts items to select
        foreach ($selectors as $selector) {
            if (count(explode(' ', $selectors_keys[$i])) > 1) {
                $key             = explode(' ', $selectors_keys[$i]);
                $selectors_list .= "$key[0] $key[1] '$selector' AND ";
            }
            if ($this->isNull($selector)) {
                $selectors_list .= "$selectors_keys[$i] IS NULL AND ";
            } else {
                $selectors_list .= "$selectors_keys[$i] = '$selector' AND ";
            }
            $i++;
        }
        $selectors_list = rtrim($selectors_list, " AND ");

        // Removes a record from the database
        return $this->containsValues(
            $this->pdo
                ->prepare("DELETE FROM $table WHERE $selectors_list;")
                ->execute(),
            1
        );
    }

    /**
     * Remove empty values from array
     *
     * @param array $array
     *
     * @return array
     */
    private function filterArray(array $array): array
    {
        // Setting up variables
        $keys          = array_keys($array);
        $filteredArray = [];
        $i             = 0;

        // Sorts array values
        foreach ($array as $param) {
            if ($param) {
                $filteredArray = array_merge($filteredArray, [$keys[$i] => $param]);
            }
            $i++;
        }

        // Returns the filtered array
        return $filteredArray;
    }

    /**
     * Check if value is null or contain "null"
     *
     * @param $value
     *
     * @return bool
     */
    private function isNull($value): bool
    {
        // Check if the value is null
        if ($value == "null" || $value == "NULL") {
            return true;
        }
        return false;
    }

    /**
     * Check if return value is empty
     *
     * @param boolean|array $data
     * @param int           $error
     *
     * @return array|boolean
     * @throws BadRequest
     * @throws NotFound
     */
    private function containsValues(bool|array $data, int $error = 0): bool|array
    {
        // Checks if the returned value is correct
        if (empty($data)) {
            throw match ($error) {
                1       => new BadRequest("The database return an error when executing the query"),
                default => new NotFound("Nothing was found in the database"),
            };
        }

        // Returns the data
        return $data;
    }
}