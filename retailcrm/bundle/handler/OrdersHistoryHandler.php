<?php

class OrdersHistoryHandler implements HandlerInterface
{
    public function prepare($data)
    {
        $this->container = Container::getInstance();

        $this->logger = new Logger();
        $this->rule = new Rule();

        $this->api = new RequestProxy(
            $this->container->settings['api']['url'],
            $this->container->settings['api']['key']
        );

        $orderGroups = $this->api->statusGroupsList();

        if (!is_null($orderGroups)) {
            $isCanceled = $orderGroups['statusGroups']['cancel']['statuses'];
        }
        foreach($data as $record) {
			if (!empty($record['customerId'])) {
				//Проверка наличия клиента
				$check_user = $this->rule->getSQL('check_user');
				$this->check_user = $this->container->db->prepare($check_user);
				$this->check_user->bindParam(':customerId', $record['customerId']);
				 try {
					$this->check_user->execute();
				} catch (PDOException $e) {
					$this->logger->write(
						'PDO: ' . $e->getMessage(),
						$this->container->errorLog
					);
				}
				$count = $this->check_user->fetch(PDO::FETCH_ASSOC);
			}else{
				$count['id'] = 0;
			}
			if($count['id'] == 0){
				if (!empty($record['customerId'])) {
					//Добавление пользователя если отсутствует
					$create_user = $this->rule->getSQL('orders_history_users_create');
					$this->create_user = $this->container->db->prepare($create_user);
					$this->create_user->bindParam(':id', $record['customerId']);
					$this->create_user->bindParam(':name', $record['firstName']);
					try {
						$this->create_user->execute();
						$customerId =  $this->container->db->lastInsertId();
					} catch (PDOException $e) {
						$this->logger->write(
							'PDO: ' . $e->getMessage(),
							$this->container->errorLog
						);
					}
					
					//Добавляем информацию о пользователе
					$create_userinfos = $this->rule->getSQL('orders_history_userinfos_create');
					$this->create_userinfos = $this->container->db->prepare($create_userinfos);
					$this->create_userinfos->bindParam(':id', $record['customerId']);
					if (!empty($record['firstName'])) {
						$this->create_userinfos->bindParam(':firstName', $record['firstName']);
					} else {
						$this->create_userinfos->bindParam(':firstName', $var = NULL);
					}
					if (!empty($record['lastName'])) {
						$this->create_userinfos->bindParam(':lastName', $record['lastName']);
					} else {
						$this->create_userinfos->bindParam(':lastName', $var = NULL);
					}
					if (!empty($record['patronymic'])) {
						$this->create_userinfos->bindParam(':patronymic', $record['patronymic']);
					} else {
						$this->create_userinfos->bindParam(':patronymic', $var = NULL);
					}
				   if (!empty($record['phone'])) {
						$this->create_userinfos->bindParam(':phone', $record['phone']);
					} else {
						$this->create_userinfos->bindParam(':phone', $var = NULL);
					}
					
					if (!empty($record['delivery']['address']['index'])) {
						$this->create_userinfos->bindParam(':postcode', $record['delivery']['address']['index']);
					} else {
						$this->create_userinfos->bindParam(':postcode', $var = NULL);
					}
					if (!empty($record['delivery']['address']['text'])) {
						$this->create_userinfos->bindParam(':address', $record['delivery']['address']['text']);
					} else {
						$this->create_userinfos->bindParam(':address', $var = NULL);
					}
					try {
						$this->create_userinfos->execute();
					} catch (PDOException $e) {
						$this->logger->write(
							'PDO: ' . $e->getMessage(),
							$this->container->errorLog
						);
					}
				}
				
				//Добавление информации о пользователе сделавшем заказ
				$create_order_userinfos = $this->rule->getSQL('orders_history_order_userinfos_create');
				$this->create_order_userinfos = $this->container->db->prepare($create_order_userinfos);
				$this->create_order_userinfos->bindParam(':id', $record['customerId']);
				if (!empty($record['firstName'])) {
					$this->create_order_userinfos->bindParam(':firstName', $record['firstName']);
				} else {
					$this->create_order_userinfos->bindParam(':firstName', $var = NULL);
				}
				if (!empty($record['lastName'])) {
					$this->create_order_userinfos->bindParam(':lastName', $record['lastName']);
				} else {
					$this->create_order_userinfos->bindParam(':lastName', $var = NULL);
				}
				if (!empty($record['patronymic'])) {
					$this->create_order_userinfos->bindParam(':patronymic', $record['patronymic']);
				} else {
					$this->create_order_userinfos->bindParam(':patronymic', $var = NULL);
				}
			   if (!empty($record['phone'])) {
					$this->create_order_userinfos->bindParam(':phone', $record['phone']);
				} else {
					$this->create_order_userinfos->bindParam(':phone', $var = NULL);
				}
				
				if (!empty($record['delivery']['address']['index'])) {
					$this->create_order_userinfos->bindParam(':postcode', $record['delivery']['address']['index']);
				} else {
					$this->create_order_userinfos->bindParam(':postcode', $var = NULL);
				}
				if (!empty($record['delivery']['address']['text'])) {
					$this->create_order_userinfos->bindParam(':address', $record['delivery']['address']['text']);
				} else {
					$this->create_order_userinfos->bindParam(':address', $var = NULL);
				}
				try {
					$this->create_order_userinfos->execute();
				} catch (PDOException $e) {
					$this->logger->write(
						'PDO: ' . $e->getMessage(),
						$this->container->errorLog
					);
				}	
			}else{
				//Обновление информации о пользователе в учётной записи joomla
				$update_user = $this->rule->getSQL('orders_history_users_update');
				$this->update_user = $this->container->db->prepare($update_user);
				$this->update_user->bindParam(':id', $record['customerId']);
				$this->update_user->bindParam(':name', $record['firstName']);
				try {
					$this->update_user->execute();
				} catch (PDOException $e) {
					$this->logger->write(
						'PDO: ' . $e->getMessage(),
						$this->container->errorLog
					);
				}
				
				//Обновляем информацию о пользователе в учётной записи virtuemart
				$update_userinfos = $this->rule->getSQL('orders_history_userinfos_update');
				$this->update_userinfos = $this->container->db->prepare($update_userinfos);
				$this->update_userinfos->bindParam(':id', $record['customerId']);
				if (!empty($record['firstName'])) {
					$this->update_userinfos->bindParam(':firstName', $record['firstName']);
				} else {
					$this->update_userinfos->bindParam(':firstName', $var = NULL);
				}
				if (!empty($record['lastName'])) {
					$this->update_userinfos->bindParam(':lastName', $record['lastName']);
				} else {
					$this->update_userinfos->bindParam(':lastName', $var = NULL);
				}
				if (!empty($record['patronymic'])) {
					$this->update_userinfos->bindParam(':patronymic', $record['patronymic']);
				} else {
					$this->update_userinfos->bindParam(':patronymic', $var = NULL);
				}
			   if (!empty($record['phone'])) {
					$this->update_userinfos->bindParam(':phone', $record['phone']);
				} else {
					$this->update_userinfos->bindParam(':phone', $var = NULL);
				}
				
				if (!empty($record['delivery']['address']['index'])) {
					$this->update_userinfos->bindParam(':postcode', $record['delivery']['address']['index']);
				} else {
					$this->update_userinfos->bindParam(':postcode', $var = NULL);
				}
				if (!empty($record['delivery']['address']['text'])) {
					$this->update_userinfos->bindParam(':address', $record['delivery']['address']['text']);
				} else {
					$this->update_userinfos->bindParam(':address', $var = NULL);
				}
				try {
					$this->update_userinfos->execute();
				} catch (PDOException $e) {
					$this->logger->write(
						'PDO: ' . $e->getMessage(),
						$this->container->errorLog
					);
				}
				
				//Обновляем информации о пользователе сделавшем заказ
				$update_order_userinfos = $this->rule->getSQL('orders_history_order_userinfos_update');
				$this->update_order_userinfos = $this->container->db->prepare($update_order_userinfos);
				$this->update_order_userinfos->bindParam(':id', $record['customerId']);
				if (!empty($record['firstName'])) {
					$this->update_order_userinfos->bindParam(':firstName', $record['firstName']);
				} else {
					$this->update_order_userinfos->bindParam(':firstName', $var = NULL);
				}
				if (!empty($record['lastName'])) {
					$this->update_order_userinfos->bindParam(':lastName', $record['lastName']);
				} else {
					$this->update_order_userinfos->bindParam(':lastName', $var = NULL);
				}
				if (!empty($record['patronymic'])) {
					$this->update_order_userinfos->bindParam(':patronymic', $record['patronymic']);
				} else {
					$this->update_order_userinfos->bindParam(':patronymic', $var = NULL);
				}
			   if (!empty($record['phone'])) {
					$this->update_order_userinfos->bindParam(':phone', $record['phone']);
				} else {
					$this->update_order_userinfos->bindParam(':phone', $var = NULL);
				}
				
				if (!empty($record['delivery']['address']['index'])) {
					$this->update_order_userinfos->bindParam(':postcode', $record['delivery']['address']['index']);
				} else {
					$this->update_order_userinfos->bindParam(':postcode', $var = NULL);
				}
				if (!empty($record['delivery']['address']['text'])) {
					$this->update_order_userinfos->bindParam(':address', $record['delivery']['address']['text']);
				} else {
					$this->update_order_userinfos->bindParam(':address', $var = NULL);
				}
				try {
					$this->update_order_userinfos->execute();
				} catch (PDOException $e) {
					$this->logger->write(
						'PDO: ' . $e->getMessage(),
						$this->container->errorLog
					);
				}					
			}
			
			//Добавление заказа
			$update = $this->rule->getSQL('orders_history_orders_update');
			$create = $this->rule->getSQL('orders_history_orders_create');
			
            if (!empty($record['externalId'])) {
                $this->sql = $this->container->db->prepare($update);
                $this->sql->bindParam(':orderExternalId', $record['externalId']);
				
            } else {
                $this->sql = $this->container->db->prepare($create);
                if (!empty($record['createdAt'])) {
                    $this->sql->bindParam(':createdAt', $record['createdAt']);
                } else {
                    $this->sql->bindParam(':createdAt', $var = NULL);
                }
            }
			
			if (!empty($record['customerId'])) {
                $this->sql->bindParam(':customerId', $record['customerId']);
            } else {
                $this->sql->bindParam(':customerId', $var = NULL);
            }

            if (!empty($record['delivery']['service']['code'])) {
                $this->sql->bindParam(':deliveryType', $record['delivery']['service']['code']);
            } else {
                $this->sql->bindParam(':deliveryType', $var = NULL);
            }

            if (!empty($record['paymentType'])) {
                $this->sql->bindParam(':paymentType', $record['paymentType']);
            } else {
                $this->sql->bindParam(':paymentType', $var = NULL);
            }

            if (!empty($record['paymentStatus']) && $record['paymentStatus'] == 'paid') {
                $this->sql->bindParam(':paymentStatus', $status = 1);
            } else {
                $this->sql->bindParam(':paymentStatus', $status = 0);
            }

            try {
                $this->sql->execute();
                $this->oid =  $this->container->db->lastInsertId();
                if (empty($record['externalId'])) {
                    $response = $this->api->ordersFixExternalIds(
                        array(
                            array(
                                'id' => (int) $record['id'],
                                'externalId' => $this->oid
                            )
                        )
                    );
                }
            } catch (PDOException $e) {
                $this->logger->write(
                    'PDO: ' . $e->getMessage(),
                    $this->container->errorLog
                );
                return false;
            }
			
			//Удаляем все товары данного заказа в virtuemart
			$items_delete = $this->rule->getSQL('orders_history_order_items_delete');
			$this->del = $this->container->db->prepare($items_delete);
			$this->del->bindParam(':order_id', $this->oid);
			try {
				$this->del->execute();
			} catch (PDOException $e) {
				$this->logger->write(
					'PDO: ' . $e->getMessage(),
					$this->container->errorLog
				);
				return false;
			}
			
			//Добавляем товары к данному заказу, если они есть
            if (!empty($record['items']) && empty($record['externalId'])) {
                foreach($record['items'] as $item) {					
                    $items = $this->rule->getSQL('orders_history_order_items_create');
                    $this->query = $this->container->db->prepare($items);
                    $this->query->bindParam(':order_id', $this->oid);
                    $this->query->bindParam(':items_catalog_items_id', $item['offer']['externalId']);
                    $this->query->bindParam(':orders_items_name', $item['offer']['name']);
                    $this->query->bindParam(':orders_items_quantity', $item['quantity']);
                    $this->query->bindParam(':orders_items_price', $item['initialPrice']);

                    try {
                        $this->query->execute();
                    } catch (PDOException $e) {
                        $this->logger->write(
                            'PDO: ' . $e->getMessage(),
                            $this->container->errorLog
                        );
                        return false;
                    }
                }
            }
        }
    }
}
