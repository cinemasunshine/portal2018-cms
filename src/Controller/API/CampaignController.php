<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Controller\Traits\AzureBlobStorage;
use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Campaign API controller
 */
class CampaignController extends BaseController
{
    use AzureBlobStorage;

    /**
     * list action
     *
     * @param array<string, mixed> $args
     */
    public function executeList(Request $request, Response $response, array $args): Response
    {
        $name = $request->getParam('name');
        $data = [];

        if (! empty($name)) {
            $campaigns = $this->em
                ->getRepository(Entity\Campaign::class)
                ->findForListApi($name);

            foreach ($campaigns as $campaign) {
                /** @var Entity\Campaign $campaign */

                $data[] = [
                    'id'    => $campaign->getId(),
                    'name'  => $campaign->getName(),
                    'image' => $this->getBlobUrl(
                        Entity\File::getBlobContainer(),
                        $campaign->getImage()->getName()
                    ),
                    'url'   => $campaign->getUrl(),
                ];
            }
        }

        return $response->withJson(['data' => $data]);
    }
}
