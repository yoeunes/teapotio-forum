<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    ForumBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\ForumBundle\Controller;

class ModerationController extends BaseController
{
    public function listAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $title = $this->generateTitle('List.of.moderations');

        $moderationsPerPage = 100;
        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $moderationsPerPage;

        $params = array(
            'moderations'          => $this->get('teapotio.forum.moderation')->getLatestModerations($offset, $moderationsPerPage),
            'moderations_per_page' => $moderationsPerPage,
            'page_title'           => $title,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioForumBundle:partial:moderation/list.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioForumBundle:page:moderation/list.html.twig', $params);
    }
}
