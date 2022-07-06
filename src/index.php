<?php
    namespace Gateway;

    require dirname(__FILE__, 5) . '/vendor/autoload.php';
    
    use Exception;
    use GuzzleHttp\Client;
    use GuzzleHttp\Psr7\Request;
    use GuzzleHttp\Promise;
    use GuzzleHttp\Psr7;
    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Exception\ServerException;

    use IframeModal\Data;
    use IframeModal\Modal;
    
    class Index {
        private $request_data;
        private $options;


        /**
         * @return none
         * 
         * @param array  $request_data    request data to get the payment gateway redirect url
         * @param array $options  options for iframe modal (UI options, theme)
        */
        public function __construct($request_data, $options) {
            $this->request_data = $request_data;
            $this->options = $options;
        }


        /**
         * @return none
        */
        function __destruct() {
            // echo 'Destruct called';
        }


        /**
         * show iframe inside a modal
         * 
         * @return none
         */
        function showModal() {
            // check inpuut params

            // get payment gateway redirect url
            try {
                $client = new Client(['base_uri' => Data::$BASE_URI]);
                $res = $client->get(
                    "ipg/gateway/request-transaction/?hash={$this->request_data['params']['hash']}",
                    [
                        'headers' => $this->request_data['headers'],
                        'json' => $this->request_data['body']
                    ]
                );
                // $res_status = $res->getStatusCode();
                $res_body = (array) json_decode($res->getBody()->getContents());

                if($res_body['status'] === 1000) {
                    // show iframe modal
                    $modal = new Modal($res_body['data'], $this->options);
                    $modal->generateModal();
                } 
                return $this->sendResponse($res_body);
            } 
            // catch(ClientException $e1) {
            //     echo 'e1' . '<br>';
            //     echo Psr7\Message::toString($e1->getRequest());
            //     echo Psr7\Message::toString($e1->getResponse());
            // } 
            // catch(ServerException $e2) {
            //     echo 'e2' . '<br>';
            //     echo Psr7\Message::toString($e2->getRequest());
            //     echo Psr7\Message::toString($e2->getResponse());
            // }
            catch(Exception $e3) {
               return $this->sendResponse();
                // echo Psr7\Message::toString($e3->getRequest());
                // echo 'Internal Server Error!';
            } 


            }


             /**
             * return success and error 
             * 
             * @param array  $res_body  response body of requesting payment gateway redirect url
             * 
             * @return array
             */
            private function sendResponse($res_body = null) {
                // var_dump($res_body);

                if(!$res_body) {
                    return [
                        'status'=>'failed',
                        'message'=>'Internal Server Error!'
                    ];
                }

                if(array_key_exists('status_code', $res_body)) {
                    return [
                        'status'=>'failed',
                        'message'=>'Internal Server Error!'
                    ];
                } else {
                    if($res_body['status'] === 1000) {
                        return [
                            'status'=>'success',
                            'message'=>$res_body['message']
                        ];
                    }
                    return [
                        'status'=>'failed',
                        'message'=>$res_body['message']
                    ];
                }
            }

           
    }