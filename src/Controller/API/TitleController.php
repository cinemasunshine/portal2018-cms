<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\ORM\Entity;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Title API controller
 */
class TitleController extends BaseController
{
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
            $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForListApi($name);

            foreach ($titles as $title) {
                /** @var Entity\Title $title */

                $data[] = [
                    'id'            => $title->getId(),
                    'name'          => $title->getName(),
                    'official_site' => $title->getOfficialSite(),
                    'publishing_expected_date' => $title->getPublishingExpectedDate()
                                               ? $title->getPublishingExpectedDate()->format('Y/m/d')
                                               : null,
                ];
            }
        }

        return $response->withJson(['data' => $data]);
    }

    /**
     * autocomplete action
     *
     * @param array<string, mixed> $args
     */
    public function executeAutocomplete(Request $request, Response $response, array $args): Response
    {
        $titles = $this->em
                ->getRepository(Entity\Title::class)
                ->findForAutocomplete($request->getParams());

        $data = [];

        foreach ($titles as $title) {
            /** @var Entity\Title $title */

            $data[] = [
                'name'          => $title->getName(),
                'name_kana'     => $title->getNameKana(),
                'name_original' => $title->getNameOriginal(),
            ];
        }

        return $response->withJson(['data' => $data]);
    }
}
