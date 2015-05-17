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

class AdminController extends BaseController
{

    public function permissionsAction($groupId = null)
    {
        if ($this->get('teapotio.user')->isAdmin($this->getUser()) === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $formName = 'board_permissions';

        $groups = $this->get('teapotio.user.group')->getAllGroups();
        $groups->add(new \Teapotio\Base\ForumBundle\Entity\AnonymousUserGroup());

        $group = null;
        if ($groupId !== null) {
            if ($groupId == 0) {
                $group = new \Teapotio\Base\ForumBundle\Entity\AnonymousUserGroup();
            } else {
                $group = $this->get('teapotio.user.group')->getById($groupId);
            }

            if ($group === null) {
                return $this->redirect($this->generateUrl('ForumAdminPermissions'));
            }
        }

        if ($this->get('request')->isMethod('POST') === true) {
            $postData = $this->get('request')->request;

            $boardIds = array();

            foreach ($postData->get('board_permissions') as $id => $value) {
              $boardIds[] = $id;
            }

            $this->container
                 ->get('teapotio.forum.access_permission')
                 ->setPermissionsOnBoardsFromPostData($groupId, $postData->get('board_permissions'), $boardIds);
        }

        $title = $this->generateTitle('Manage.group.permissions');

        $params = array(
            'groups'     => $groups,
            'group'      => $group,
            'form_name'  => $formName,
            'page_title' => $title,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioForumBundle:partial:admin/permissions.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioForumBundle:page:admin/permissions.html.twig', $params);
    }

}
