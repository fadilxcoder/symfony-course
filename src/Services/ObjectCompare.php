<?php


namespace App\Services;


class ObjectCompare
{
    public function compareTwoObj($orgObj, $subData)
    {
        unset($subData['_token']);
        foreach ( $subData as $key => $value) {

            if($orgObj->{'get'.ucfirst($key)}() != $value ){
                return false;
                break;
            }
        }

        return true;
    }
}