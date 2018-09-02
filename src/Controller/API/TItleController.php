<?php
/**
 * TitleController.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\API;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;

use Cinemasunshine\PortalAdmin\Form\API as ApiForm;
use Cinemasunshine\PortalAdmin\ORM\Entity;

/**
 * Title API controller
 */
class TitleController extends BaseController
{
    /**
     * list action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeList($request, $response, $args)
    {
        $name = $request->getParam('name');
        $data = [];
        
        if (!empty($name)) {
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);
            
                
            foreach ($titles as $title) {
                /** @var Entity\Title $title */
                
                $data[] = [
                    'id'   => $title->getId(),
                    'name' => $title->getName(),
                ];
            }
        }
        
        $this->data->set('data', $data);
    }
    
    /**
     * master action
     * 
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeMaster($request, $response, $args)
    {
        $form = new ApiForm\TitleMasterFindForm($this->em);
        $form->setData($request->getParams());
        
        if (!$form->isValid()) {
            $errors = [];
            $messages = $form->getMessages();
            
            foreach ($messages as $field => $fieldMessages) {
                foreach ($fieldMessages as $type => $message) {
                    $errors[] = [
                        'title' => sprintf('%s %s', $field, $type),
                        'detail' => $message,
                    ];
                }
            }
            
            $this->data->set('errors', $errors);
            return;
        }
        
        $cleanData = $form->getData();
        
        $theater = $this->em
            ->getRepository(Entity\Theater::class)
            ->findOneById($cleanData['theater']);
        
        $settings = $this->settings['coa'];
        
        $httpClient = $this->createClient($settings['api_server']);
        
        $accessToken = $this->getAccessToken($httpClient, $settings['refresh_token']);
        
        $masterTitles = $this->findMaster($httpClient, $accessToken, $theater->getMasterCode(), $cleanData);
        $data = [];
        
        foreach ($masterTitles as $masterTitle) {
            // コードと枝番を合わせて7桁コードにする
            $code = $masterTitle->title_code . sprintf('%02d', $masterTitle->title_branch_num);
            $data[] = [
                'name' => $masterTitle->title_name,
                'code' => $code,
            ];
        }
        
        $this->data->set('data', $data);
    }
    
    /**
     * create client
     * 
     * 必要であれば別のクラスに実装する。
     * 
     * @param string $server
     * @return HttpClient
     */
    protected function createClient(string $server)
    {
        $options = [
            'base_uri' => sprintf('http://%s/', $server),
            'timeout' => 5,
            'http_errors' => true,
            'handler' => HandlerStack::create(),
        ];
        
        return new HttpClient($options);
    }
    
    /**
     * アクセストークン取得
     * 
     * 必要であれば別のクラスに実装する。
     * 
     * @todo 取得したトークンをキャッシュする
     *
     * @param HttpClient $client
     * @param string $refreshToken
     * @return string
     */
    protected function getAccessToken(HttpClient $client, string $refreshToken)
    {
        $response = $client->post('/token/access_token', [
            'form_params' => [
                'refresh_token' => $refreshToken,
            ],
        ]);
        
        $json = $response->getBody()->getContents();
        
        /** @var \stdClass $data */
        $data = json_decode($json);
        
        return $data->access_token;
    }
    
    /**
     * find master
     * 
     * 必要であれば別のクラスに実装する。
     *
     * @param HttpClient $client
     * @param string $accessToken
     * @param string $theaterCode
     * @param array $params
     * @return array
     */
    protected function findMaster(HttpClient $client,string $accessToken, string $theaterCode, array $params)
    {
        $response = $client->get(sprintf('/api/v1/theater/%s/title/', $theaterCode), [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
        
        $json = $response->getBody()->getContents();
        
        /** @var \stdClass $data */
        $data = json_decode($json);
        
        if ((int) $data->status !== 0) {
            throw new \RuntimeException($data->message);
        }
        
        $result = [];
        
        foreach ($data->list_title as $title) {
            /** @var \stdClass $title */
            
            if (
                false !== mb_strpos($title->title_name, $params['name'])
                || false !== mb_strpos($title->title_name_orig, $params['name'])
            ) {
                $result[] = $title;
                
            }
        }
        
        return $result;
    }
}