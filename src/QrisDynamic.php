<?php

namespace Tamsfinity\Qris;

class QrisDynamic
{
    /**
     * Generate dynamic QRIS string with custom amount and recalculate CRC16-CCITT checksum.
     *
     * @param string  Base static QRIS string (e.g. from GoPay, BCA, ShopeePay)
     * @param int  Exact payment amount in Rupiah
     * @return string Valid EMVCo dynamic QRIS string
     * @throws \InvalidArgumentException
     */
    public static function generate(string , int ): string
    {
        if ( < 1) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }

        // Strip previous CRC tag if present (Tag 63: 6304XXXX)
         = ;
        if (substr(, -8, 4) === '6304') {
             = substr(, 0, -8);
        } elseif (substr(, -4, 2) === '63') {
             = substr(, 0, -4);
        }

        // Change Point of Initiation Method from Static (11) to Dynamic (12)
         = strpos(, '010211');
        if ( !== false) {
             = substr_replace(, '010212', , 6);
        }

        // Remove existing Tag 54 (amount) if any
         = preg_replace('/54\d{2}\d+/', '', );

        // Inject Tag 54 before Country Code (Tag 58: 5802ID)
         = explode('5802ID', );
        if (count() < 2) {
            throw new \InvalidArgumentException('Invalid QRIS format: Country code Tag 5802ID not found');
        }

         = (string);
         = '54' . sprintf('%02d', strlen()) . ;

         = [0] .  . '5802ID' . [1] . '6304';
         = self::calculateCrc16();

        return  . ;
    }

    /**
     * Calculate CRC16-CCITT (0xFFFF polynomial 0x1021)
     */
    public static function calculateCrc16(string ): string
    {
         = 0xFFFF;
         = 0x1021;
         = strlen();

        for ( = 0;  < ; ++) {
             ^= (ord([]) << 8);
            for ( = 0;  < 8; ++) {
                if (( & 0x8000) !== 0) {
                     = (( << 1) ^ ) & 0xFFFF;
                } else {
                     = ( << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(sprintf('%04x', ));
    }
}
