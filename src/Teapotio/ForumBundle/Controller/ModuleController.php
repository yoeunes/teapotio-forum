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

use Teapotio\ForumBundle\Entity\Board;
use Teapotio\Base\ForumBundle\Form\CreateBoardType;

class ModuleController extends BaseController
{

    public function conciseListAction()
    {
        $params = array(
            'boards'         => $this->get('teapotio.forum.board')->getBoards(false, false),
            'containerClass' => 'module list dark',
        );

        return $this->render('TeapotioForumBundle:Board:component/conciseList.html.twig', $params);
    }

    public function listBoardsAction()
    {
        $params = array(
            'boards'         => $this->get('teapotio.forum.board')->getBoards(false, false),
            'containerClass' => '',
        );

        return $this->render('TeapotioForumBundle:Board:component/list.html.twig', $params);
    }

    public function topUsersAction()
    {
        $params = array(
            'containerClass' => '',
        );

        return $this->render('TeapotioForumBundle:Component:topUsers.html.twig', $params);
    }

    public function moderationListAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $params = array(
            'containerClass' => '',
        );

        return $this->render('TeapotioForumBundle:Moderation:component/list.html.twig', $params);
    }

}
