<?php

class OffersHandler implements HandlerInterface
{
    public function prepare($data)
    {
        $offers = array();
        
        foreach ($data as $record) {
            $offer = array();
            $offer['id'] = $record['id'];
            $offer['productId'] = $record['productId'];
            $offer['quantity'] = $record['quantity'];
            if(isset($record['url'])){
				$offer['url'] = $record['url'];
			}
            $offer['price'] = $record['price'];
            $offer['purchasePrice'] = $record['purchasePrice'];
            $offer['categoryId'] = $record['categoryId'];
            if(isset($record['picture'])){
				$offer['picture'] = $record['picture'];
            }
            $offer['name'] = $record['name'];
            $offer['productName'] = $record['productName'];
            if(isset($record['article'])){
				$offer['article'] = $record['article'];
			}
			if(isset($record['product_size'])){
				$offer['product_size'] = $record['product_size'];
			}
			if(isset($record['weight'])){
				$offer['weight'] = $record['weight'];
			}
			if(isset($record['vendor'])){
				$offer['vendor'] = $record['vendor'];
			}

            $offer = DataHelper::filterRecursive($offer);
            array_push($offers, $offer);
        }

        return $offers;
    }
}
