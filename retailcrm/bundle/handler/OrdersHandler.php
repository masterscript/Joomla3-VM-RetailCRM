<?php

class OrdersHandler implements HandlerInterface
{
    public function prepare($data)
    {
        $orders = array();

        foreach ($data as $record) {
            $order = array();
            $order['externalId'] = $record['externalId'];

            if ($record['customerId']) {
                $order['customerId'] = ($record['customerId'] == 0)
                ? $record['externalId']
                : $record['customerId']
                ;
            }

            $order['firstName'] = $record['firstName'];
            $order['lastName'] = $record['lastName'];
            $order['patronymic'] = $record['patronymic'];
            $order['email'] = $record['email'];
            $order['phone'] = $record['phone'];

            $order['delivery'] = array(
                'address' => array(
                    'index' => $record['deliveryIndex'] == 0 ? '' : $record['deliveryIndex'],
                    'country' => $record['deliveryCountry'],
                    'region' => $record['deliveryRegion'],
                    'city' => $record['deliveryCity']
                ),
                'code' => $record['deliveryType']
            );

            if (
                !empty($record['deliveryIndex']) &&
                !empty($record['deliveryCountry']) &&
                !empty($record['deliveryCity']) &&
                !empty($record['deliveryAddress'])
            ) {
                $order['delivery']['address']['text'] = implode(
                    ', ',
                    array(
                        $record['deliveryIndex'],
                        $record['deliveryCountry'],
                        $record['deliveryCity'],
                        $record['deliveryAddress']
                    )
                );
            }

            $order['paymentType'] = $record['paymentType'];
            $order['paymentStatus'] = $record['paymentStatus'];
            $order['createdAt'] = $record['createdAt'];

           

            $order['items'] = array();

            $items = explode('|', $record['items']);

            foreach ($items as $item) {
                $data = explode(';', $item);
                $item = array();
                $item['productId'] = $data[0];
                $item['productName'] = (isset($data[1])) ? $data[1] : 'no-name';
                $item['quantity'] = (isset($data[2])) ? (int) $data[2] : 0;
                $item['initialPrice'] = (isset($data[3]) && $data[3] != '') ? $data[3] : 0 ;

                array_push($order['items'], $item);
            }

            $order = DataHelper::filterRecursive($order);
            array_push($orders, $order);
        }

        return $orders;
    }
}
