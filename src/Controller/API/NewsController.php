<?php

/**
 * NewsController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * News API controller
 */
class NewsController extends BaseController
{
    use AzureBlobStorage;

    /**
     * list action
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeList(Request $request, Response $response, array $args)
    {
        $headline = $request->getParam('headline');
        $data     = [];

        if (! empty($headline)) {
            $newsList = $this->em
                ->getRepository(Entity\News::class)
                ->findForListApi($headline);

            foreach ($newsList as $news) {
                /** @var Entity\News $news */

                $image = null;

                if ($news->getImage()) {
                    $image = $this->getBlobUrl(
                        Entity\File::getBlobContainer(),
                        $news->getImage()->getName()
                    );
                }

                $data[] = [
                    'id'             => $news->getId(),
                    'headline'       => $news->getHeadline(),
                    'image'          => $image,
                    'category_label' => $news->getCategoryLabel(),
                ];
            }
        }

        return $response->withJson(['data' => $data]);
    }
}
