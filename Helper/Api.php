<?php

namespace OuterEdge\ZebrecoIntegration\Helper;

use OuterEdge\ZebrecoIntegration\Helper\Data;
use ZebrecoPHP\Api as ZebrecoApi;

class Api extends Data
{
    /**
     * @param string $endpoint
     * @return ZebrecoApi
     */
    public function getEndpoint($endpoint)
    {
        return new ZebrecoApi(
            $this->getAccount(),
            $this->getUser(),
            $this->getPassword(),
            $endpoint
        );
    }
    
    public function getContactByEmail($email)
    {
        $contacts = $this->getEndpoint('contact')->getList([
            'query' => [
                'page'  => '1',
                'limit' => '1',
                'q'     => 'email:' . $email
            ]
        ]);
        if (!empty($contacts) && count($contacts['contacts'])) {
            return $contacts['contacts'][0];
        }
        return null;
    }
}