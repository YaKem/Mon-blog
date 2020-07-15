<?php

    class FormatDate
    {
        /**
         * @var dateTime $date_time date
         */
        private static $date_time;

        /**
         * Formate une date
         *
         * @param dateTime $dt date non formatée
         * @return dateTime $result date formatée
         */
        public static function format($dt): string
        {
            $date_time = new dateTime($dt);

            $result = date_format($date_time, 'd m Y | H:i:s');

            return $result;
        }
    }
    
?>