<?php

namespace Fixer;

use \GuzzleHttp\Client;

class EntityFixer extends EntityAbstract {

  private $phones=[];

  private function getContact($endpoint, $userId, $token, $id) {
    $ENDPOINT_TEMPLATE = '%s/%s/%s/crm.%s.get.json?ID=%s';
    $response = $this->client->request(
      'GET',
      vsprintf(
        $ENDPOINT_TEMPLATE,
        [
          $endpoint,
          $userId,
          $token,
          $this->entity,
          $id
        ]
      )
    );

    if ($response->getStatusCode() >= 400 ) {
      die('error');
    }
    
    $data = json_decode($response->getBody(), true);
    $contact = $data['result'];
    $phones = $contact['PHONE'];
    foreach($phones as $phStruct) {
      $this->phones[(int)$phStruct['ID']]=$phStruct['VALUE'];
    }
  }

  private function fixPhones() {
    foreach($this->phones as & $phone) {
      $phone = preg_replace('/^\+?380/ius', '0', $phone);
    }
  }

  private function setContact($endpoint, $userId, $token, $id){
    $ENDPOINT_TEMPLATE = '%s/%s/%s/crm.%s.update.json?id=%s';

    $phones = [];

    foreach ($this->phones as $phoneId => $phoneValue) {
      $phones[] = [ 'ID' => $phoneId, 'VALUE' => $phoneValue ];
    }

    $payload = [
      'ID' => $id,
      'fields' => [
        'PHONE' => $phones
      ]
    ];

    $response = $this->client->request(
      'POST',
      vsprintf(
        $ENDPOINT_TEMPLATE,
        [
          $endpoint,
          $userId,
          $token,
          $this->entity,
          $id
        ]
      ), ['json' => $payload]);
  }

  public function fix($endpoint, $userId, $token, $id){
    $this->log->debug('Getting contact ' . $id);
    $this->getContact($endpoint, $userId, $token, $id);
    $this->log->debug('Got phones: ' . var_export($this->phones, true) . ' for contact '. $id);
    $this->fixPhones();
    $this->log->debug('Fixed phones: ' . var_export($this->phones, true) . ' for contact '. $id);
    $this->setContact($endpoint, $userId, $token, $id);
    $this->phones = [];
    return $this->phones;
  }
}

