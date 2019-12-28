<?php


namespace App\Services;


class ObjectCompare
{
    public function compareTwoObj($orgObj, $subData)
    {
        unset($subData['_token']);
        unset($subData['save']);

        foreach ( $subData as $key => $value) {

            if($orgObj->{'get'.ucfirst($key)}() != $value ){
                return false;
                break;
            }
        }

        return true;
    }

    public function getUpdatedFields($orgObj, $subData)
    {
        unset($subData['_token']);
        unset($subData['save']);

        $arr= [];

        foreach ($subData as $key => $value) {

            if($orgObj->{'get'.ucfirst($key)}() != $value ){
                $arr[] = $key;
            }

        }

        $label = [];

        foreach ($arr as $_arr) {
            $parts = preg_split('/(?=[A-Z])/', $_arr, -1, PREG_SPLIT_NO_EMPTY);
            $string = implode(' ', $parts);
            $label[] = $string;
        }

        return $label;
    }

}