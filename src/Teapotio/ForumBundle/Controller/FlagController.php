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
use Teapotio\BaseForumBundle\Form\CreateBoardType;

class FlagController extends BaseController
{

    public function flagListAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $params = array(
            'containerClass' => '',
        );

        return $this->render('TeapotioForumBundle:Flag:modules/list.html.twig', $params);
    }

    public function ignoreAction($flagId)
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $flag = $this->container
                     ->get('teapotio.forum.flag')
                     ->getById($flagId);

        if ($flag !== null) {
            $this->container
                 ->get('teapotio.forum.flag')
                 ->ignore($flag, $this->getUser());
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }

    public function deleteAction($flagId)
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $flag = $this->container
                     ->get('teapotio.forum.flag')
                     ->getById($flagId, $this->getUser());

        if ($flag !== null) {
            $this->container
                 ->get('teapotio.forum.flag')
                 ->delete($flag);
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }

}