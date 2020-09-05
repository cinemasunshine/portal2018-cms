<?php

/**
 * TitleRankingResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Responder;

use Psr\Http\Message\ResponseInterface;
use Slim\Collection;
use Slim\Http\Response;

/**
 * TitleRanking responder
 */
class TitleRankingResponder extends BaseResponder
{
    /**
     * edit
     *
     * @param Response $response
     * @param Collection $data
     * @return ResponseInterface
     */
    public function edit(Response $response, Collection $data)
    {
        return $this->view->render($response, 'title_ranking/edit.html.twig', $data->all());
    }
}
