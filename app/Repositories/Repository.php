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
       $addDates = true;
       foreach ($data as $field => $value) {
           if ($field == 'created_at' || $field == 'updated_at') {
               $addDates = false;
           }

           $fields[] = '"' . $field . '"';
           if (is_bool($value)) {
               $values[] = $value ? 'true' : 'false';
               continue;
           }

           $values[] = '\'' . $value . '\'';
       }

       if ($addDates) {
           $fields[] = '"created_at"';
           $fields[] = '"updated_at"';
           $values[] = '\'' . $this->getNow() . '\'';
           $values[] = '\'' . $this->getNow() . '\'';
       }

       $query .= '(' . implode(',', $fields) . ')';
       $query .= '
            VALUES
            ('. implode(',', $values) . ')';

       return $query;
   }

   protected function getNow()
   {
       $now = new \DateTime();
       return $now->format('Y-m-d H:i:s');
   }

}