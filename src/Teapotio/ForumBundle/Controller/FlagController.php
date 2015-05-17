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

class FlagController extends BaseController
{

    public function flagListAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $params = array(
            'flags' => $this->get('teapotio.forum.flag')->getLatestFlags(0, 15, false),
        );

        return $this->render('TeapotioForumBundle:component:flag/list.html.twig', $params);
    }

    public function listAction()
    {
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $title = $this->generateTitle('List.of.flags');

        $flagsPerPage = 100;
        $page = ($this->get('request')->get('page') === null) ? 1 : $this->get('request')->get('page');
        $offset = ($page - 1) * $flagsPerPage;

        $flags = $this->get('teapotio.forum.flag')->getLatestFlags($offset, $flagsPerPage, false);

        $params = array(
            'flags'          => $flags,
            'flags_per_page' => $flagsPerPage,
            'page_title'     => $title,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioForumBundle:partial:flag/list.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioForumBundle:page:flag/list.html.twig', $params);
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
                 ->delete($flag, $this->getUser());
        }

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array('success' => 1));
        }

        return $this->redirect(
            $this->get('request')->headers->get('referer')
        );
    }

}
