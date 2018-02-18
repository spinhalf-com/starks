<?php namespace Classes;
/**
 * Created by PhpStorm.
 * User: johnriordan
 * Date: 17/02/2018
 * Time: 20:07
 */

use Carbon\Carbon;

class MbClassValidator
{
    static $indices          = ['PageSize','CurrentPageIndex','XMLDetail','Test','ClientID','ContractID','LocationID','Amount','CreditCardNumber','CreditCardExpMonth','CreditCardExpYear','CustomerName','CustomerAddress','CustomerPostcode','FirstPaymentOccurs'];
    static $numerics         = ['PageSize','CurrentPageIndex','LocationID', 'ClientID', 'ContractID', 'CreditCardExpMonth', 'CreditCardExpYear', 'CreditCardNumber'];
    static $s_indices        = ['PageSize','CurrentPageIndex','XMLDetail','Test','ClientID','ContractID','LocationID','Amount','FirstPaymentOccurs'];
    //static $s_numerics       = ['PageSize','CurrentPageIndex','LocationID', 'ClientID', 'ContractID'];


    public static function validateContractPurchaseData($data, $storedCard = false)
    {
        $indices            = $storedCard ? static::$s_indices : static::$indices;

        foreach ($indices as $index)
        {
            if(!self::exists($data, $index))
            {
                return false;
            }
        }

        return true;
    }

    private static function exists($data, $index)
    {
        if(!array_key_exists($index, $data))
        {
            return $index . " missing.";
        }
        else
        {
            if(strlen($data[$index] == 0))
            {
                return $index . " missing.";
            }

            if(in_array($index, static::$numerics))
            {
                if(!is_numeric($data[$index]))
                {
                    return $index . " must be numeric.";
                }
            }
        }
        return true;
    }

    static function dateCheck($data)
    {
        if(!$data['FirstPaymentOccurs'] == 'Instant')
        {
            if(strtotime($data['FirstPaymentOccurs']) > 0)
            {
                if (Carbon::parse($data['FirstPaymentOccurs'])->toDateString() < Carbon::now()->toDateString())
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        return true;
    }
}
