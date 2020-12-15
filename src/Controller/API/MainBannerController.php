<?php

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * MainBanner API controller
 */
class MainBannerController extends BaseController
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
        $name = $request->getParam('name');
        $data = [];

        if (! empty($name)) {
            $mainBannerList = $this->em
                ->getRepository(Entity\MainBanner::class)
                ->findForListApi($name);

            foreach ($mainBannerList as $mainBanner) {
                /** @var Entity\MainBanner $mainBanner */

                $data[] = [
                    'id'    => $mainBanner->getId(),
                    'name'  => $mainBanner->getName(),
                    'image' => $this->getBlobUrl(
                        Entity\File::getBlobContainer(),
                        $mainBanner->getImage()->getName()
                    ),
                ];
            }
        }

        return $response->withJson(['data' => $data]);
    }
}
