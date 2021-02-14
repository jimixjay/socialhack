<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;

abstract class Repository implements RepositoryInterface
{

   protected function addWhereFromData(string &$query, array $data)
   {
       $where = [];
       foreach ($data as $field => $value) {
           $where[] = '"' . $field . '" = \'' . $value . '\'';
       }

       if (count($where) > 0) {
           $query .= implode(' AND ', $where);
       }

       return $query;
   }

   protected function addInsertData(string &$query, array $data)
   {
       $fields = [];
       $values = [];
       foreach ($data as $field => $value) {
           $fields[] = '"' . $field . '"';
           if (is_bool($value)) {
               $values[] = $value ? 'true' : 'false';
               continue;
           }

           $values[] = '\'' . $value . '\'';
       }

       $query .= '(' . implode(',', $fields) . ', "created_at", "updated_at")';
       $query .= '
            VALUES
            ('. implode(',', $values) . ', \'' . $this->getNow() . '\', \'' . $this->getNow() . '\')';

       return $query;
   }

   protected function getNow()
   {
       $now = new \DateTime();
       return $now->format('Y-m-d H:i:s');
   }

}