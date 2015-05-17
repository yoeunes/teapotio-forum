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

class ModuleController extends BaseController
{

    public function conciseListAction()
    {
        $params = array(
            'boards' => $this->get('teapotio.forum.board')->getBoards(false, false),
        );

        return $this->render('TeapotioForumBundle:component:board/conciseList.html.twig', $params);
    }

    public function listBoardsAction()
    {
        $params = array(
            'boards' => $this->get('teapotio.forum.board')->getBoards(false, false),
        );

        return $this->render('TeapotioForumBundle:component:board/list.html.twig', $params);
    }

    public function topUsersAction()
    {
        $params = array();

        return $this->render('TeapotioForumBundle:component:topUsers.html.twig', $params);
    }

    public function moderationListAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $params = array(
          'moderations' => $this->get('teapotio.forum.moderation')->getLatestModerations(0, 15)
        );

        return $this->render('TeapotioForumBundle:component:moderation/list.html.twig', $params);
    }

}
