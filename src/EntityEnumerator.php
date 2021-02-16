<?php

namespace Fixer;

use \GuzzleHttp\Client;

class EntityEnumerator extends EntityAbstract {

private $position=0;

private $ids=[];

const ENDPOINT_TEMPLATE='%s/%s/%s/crm.%s.list.json?select[]="ID"&start=%s';

private function parseResult(array $result) {
  $tempData = [];
  foreach ($result as $IDset) {
    $tempData[] = (int) $IDset['ID'];
  }
  $this->log->debug("Got " . count($tempData) . ' items');
  return $tempData;
}

private function getItems($endpoint, $userId, $token)
{
  $response = $this->client->request(
    'GET',
    vsprintf(
      self::ENDPOINT_TEMPLATE,
      [
        $endpoint,
        $userId,
        $token,
        $this->entity,
        $this->position
      ]
    )
  );

    if ($response->getStatusCode() >= 400 ) {
      die('error');
    }

    $data = json_decode($response->getBody(), true);

    if (isset($data['next'])) {
      $this->position = (int) $data['next'];
    } else {
      $this->position = null;
    }

    
    return $data['result'];
}

public function getIds($endpoint, $userId, $token, $sleepInterval) {

  while (null !== $this->position) {
    $data = $this->getItems($endpoint, $userId, $token);
    $this->ids = array_merge($this->ids, $this->parseResult($data));
    usleep($sleepInterval);
  }

  return $this->ids;
}
}