<?php

/**
 * @author danil danil.kabluk@gmail.com
 */
class Controller extends \djagya\bitcoin\Controller
{
    /**
     * Fix for gh-pages demo
     * @param $userId
     * @return array
     */
    protected function generateLabels($userId)
    {
        return [$userId];
    }
}
