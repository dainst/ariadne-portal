<?php

namespace App\Services;

use Config;
use Request;
use Log;
use Illuminate\Pagination\Paginator;
use App\Pagination\ElasticSearchPaginator;
use App\Exceptions\ElasticSearchQueryException;
use Illuminate\Support\Facades\Input;

use Elasticsearch\ClientBuilder;

class ElasticSearch {

  private static $client = NULL;

  /**
   * Get a client to perform rest request to Elastic Search
   * 
   * @return Object Elastic Search client
   */
  public static function getClient() {

    if (is_null(self::$client)) {
      
      $hosts = [ Config::get('app.elastic_search_host') ];
      
      $params['host'] = array(Config::get('app.elastic_search_host'));
      self::$client = ClientBuilder::create() // Instantiate a new ClientBuilder
                      ->setHosts($hosts)      // Set the hosts
                      ->build();              // Build the client object
                    
    }

    return self::$client;

  }

  /**
   * Get document from Elastic Search
   *
   * @param type $id id of the document (e.g. dataset id)
   * @param type $index index containing the document
   * @param type $type type of document to look for
   * @return Array all the values in _source from Elastic Search
   * @throws Exception if document is not found
   */
  public static function get($id, $index, $type) {
    $getParams = [
      'id' => $id,
      'index' => $index
      //'type' => $type
    ];
  
    //$getParams['type'] = '_doc';

    $client = self::getClient();
    $result = $client->get($getParams);
    $last = $client->transport->getLastConnection()->getLastRequestInfo();
    $body = $last['request']['body'];

    //dd( $result['_source'] );
    if ($result['found']) {
      return $result;
    } else {
      throw new Exception('No document found by id: ' . $id);
    }

  }

  /**
   * Performs a paginated search against Elastic Search
   * 
   * @param Array $query Array containing the elastic search query
   * @param string $index Index to search in (optional)
   * @param string $type Type of document to search for (optional)
   * @return LengthAwarePaginator paginated result of the search
   */
  public static function search($query, $index = null, $type = null) {

    //Log::info('Searching index: ' . $index);
    //Log::INFO( get_class());

    if ($index) {
      $searchParams['index'] = $index;
    }

    if (!$type) {
      $searchParams['type'] = $type;
    }

    $client = self::getClient();

    if(Request::has('noPagination')) {
      Log::INFO( 'NO PAGINATION' );

      $searchParams['body'] = $query;
      $searchParams['size'] = Request::input('size', 10);
      $resp = $client->search($searchParams); 
      return $resp;
      
    } else {  

      Log::INFO( 'HAS PAGINATION' );

      $perPage = Request::input('perPage', 10);
      $from = $perPage * (Request::input('page', 1) - 1);

      $query['highlight'] = array('fields' => array('*' => (object) array()));
      
      $searchParams['body'] = $query;
      $searchParams['size'] = $perPage;
      $searchParams['from'] = $from;
      
      //print( json_encode($searchParams) );
      //exit;

      $queryResponse = $client->search($searchParams);
      
      $aggregations = null;
      if (array_key_exists('aggregations', $queryResponse)) {
        $aggregations = $queryResponse['aggregations'];
      }

      //dd( $queryResponse );
      /*
      dd( $queryResponse );
      dd( $queryResponse['hits']['hits'] );
      dd( $queryResponse['hits']['total'] );
      dd( $perPage );
      dd( $aggregations );
      */

      $paginator = new ElasticSearchPaginator(
        $queryResponse['hits']['hits'], 
        $queryResponse['hits']['total'], 
        $perPage, 
        $aggregations, 
        Paginator::resolveCurrentPage(), 
        ['path' => Paginator::resolveCurrentPath()]
      );
      
      //$last = $client::connection('default')->transport->lastConnection->getLastRequestInfo();
      $last = $client->transport->getLastConnection()->getLastRequestInfo();
      $body = $last['request']['body'];
      Log::INFO( $last );

      return $paginator;      
      

    }
  }
  
  /**
   * Helper function to get the hit count
   * @param array $query structured query for Elastic Search
   * @param string $index index to search in
   * @param string $type optional type to limit the search for
   * @return int number of results from the query
   */
  public static function countHits($query, $index, $type = null) {

    $searchParams = array(
      'body' => $query
    );

    $searchParams['index'] = $index;
    if ($type) {
      $searchParams['type'] = $type;
    }

    $client = self::getClient();

    $queryResponse = $client->search($searchParams);

    return $queryResponse['hits']['total'];
  }

  public static function allHits($query, $index, $type = null) {

    $searchParams = array(
      'body' => $query
    );

    $searchParams['index'] = $index;
    //if ($type){
    //  $searchParams['type'] = $type;
    //}

    $client = self::getClient();
    
    $queryResponse = $client->search($searchParams);

    return $queryResponse['hits']['hits'];
  }

}
