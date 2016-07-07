<?php

class CustomersHandler implements HandlerInterface
{
    public function prepare($data)
    {
        $customers = array();

        foreach ($data as $customer) {
            $customers[] = array(
                'externalId' => $customer['externalId'],
                'firstName' => $customer['firstName'],
                'lastName' => $customer['lastName'],
				'patronymic' => $customer['patronymic'],
                'phones' => $customer['phones']['number'],
                'address' => $customer['address']
            );
        }

        return $customers;
    }
}
