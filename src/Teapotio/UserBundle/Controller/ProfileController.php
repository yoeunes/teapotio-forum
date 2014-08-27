<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    UserBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\UserBundle\Controller;

use Teapotio\ImageBundle\Entity\Image;
use Teapotio\ImageBundle\Form\ImageType;

use Teapotio\UserBundle\Entity\UserSettings;
use Teapotio\UserBundle\Form\UserDescriptionType;
use Teapotio\UserBundle\Form\UserGroupType;
use Teapotio\UserBundle\Form\UserSettingsType;

use Teapotio\Components\Controller;

class ProfileController extends Controller
{
    public function indexAction($userSlug, $userId)
    {
        $user = $this->get('teapotio.user')
                     ->find($userId);

        if ($user === null) {
            throw $this->createNotFoundException();
        }

        if ($userSlug !== $user->getSlug()) {
            return $this->redirect(
                $this->generateUrl('TeapotioBaseUserBundle_profile', array(
                    'userSlug' => $user->getSlug(),
                    'userId'   => $user->getId(),
                ))
            );
        }

        $latestTopics = $this->get('teapotio.forum.topic')->getLatestTopicsByUser($user, 0, 5);

        $messagesPerPage = $this->get('teapotio.forum')->getTotalMessagesPerPage();

        $title = $this->generateTitle("%username%'s profile", array('%username%' => $user->getUsername()));

        // Provide a new notice message to explain what's going on the page
        // A moderator or an admin should be able to modify a user info
        $infoNotices = array();
        $infoNoticeLinks = array();
        if ($this->get('teapotio.forum.access_permission')->isModerator($this->getUser()) === true
            && $this->getUser() && $this->getUser()->getId() !== $user->getId()) {
            $infoNotices[] = $this->get('translator')->trans('You.have.enough.rights.to.modify.this.user');

            $infoNoticeLinkActionPath = $this->generateUrl(
                'TeapotioBaseUserBundle_settings',
                array(
                    'userSlug' => $user->getSlug(),
                    'userId'   => $user->getId(),
                )
            );

            $infoNoticeLinkActionLabel = $this->get('translator')->trans('Edit');

            $infoNoticeLinks[] = array(
                'path'   => $infoNoticeLinkActionPath,
                'label'  => $infoNoticeLinkActionLabel,
            );
        }

        $params = array(
            'user'                => $user,
            'latest_topics'       => $latestTopics,
            'messages_per_page'   => $messagesPerPage,
            'page_title'          => $title,
            'info_notices'        => $infoNotices,
            'info_notice_links'   => $infoNoticeLinks,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioUserBundle:partial:profile/index.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioUserBundle:page:profile/index.html.twig', $params);
    }

    public function settingsAction($userSlug, $userId)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $user = $this->get('teapotio.user')
                     ->find($userId);

        if ($user === null) {
            throw $this->createNotFoundException();
        }

        $isCurrentUserModerator = $this->get('teapotio.forum.access_permission')->isModerator($this->getUser());
        $isCurrentUserAdmin = $this->get('teapotio.forum.access_permission')->isAdmin($this->getUser());

        if ($isCurrentUserAdmin === false && $isCurrentUserModerator === false
            && $user->getId() !== $this->get('security.context')->getToken()->getUser()->getId()) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        if ($userSlug !== $user->getSlug()) {
            return $this->redirect(
                $this->generateUrl('TeapotioBaseUserBundle_profile', array(
                    'userSlug' => $user->getSlug(),
                    'userId'   => $user->getId(),
                ))
            );
        }

        $settings = $user->getSettings();
        if ($settings === null) {
            $settings = new UserSettings();
        }

        $infoNotices = array();
        if ($user->getId() !== $this->getUser()->getId()) {
            // Provide a new notice message to explain what's going on the page
            // A moderator should be able to modify a user info
            if ($isCurrentUserModerator === true) {
                $infoNotices[] = $this->get('translator')->trans('You.have.access.to.this.page.because.you.were.granted.some.special.rights');
            }

            // Provide a new notice message to explain what's going on the page
            // Also add extra forms for extended
            if ($isCurrentUserAdmin === true) {
                $infoNotices[] = $this->get('translator')->trans('As.an.admin.you.have.access.to.extra.tools');
            }
        }

        $formImage = $this->createForm(new ImageType(), new Image());
        $formDescription = $this->createForm(new UserDescriptionType(), $user);
        $formSettings = $this->createForm(new UserSettingsType(), $settings);

        $formGroups = false;
        if ($isCurrentUserAdmin === true) {
          $formGroups = $this->createForm(new UserGroupType(), $user)->createView();
        }

        $title = $this->generateTitle("%username%'s settings", array('%username%' => $user->getUsername()));

        $params = array(
            'user'              =>  $user,
            'formImage'         =>  $formImage->createView(),
            'formDescription'   =>  $formDescription->createView(),
            'formSettings'      =>  $formSettings->createView(),
            'formGroups'        =>  $formGroups,
            'page_title'        =>  $title,
            'info_notices'      =>  $infoNotices,
        );

        if ($this->get('request')->isXmlHttpRequest() === true) {
            return $this->renderJson(array(
                'html'   => $this->renderView('TeapotioUserBundle:partial:profile/settings.html.twig', $params),
                'title'  => $title
            ));
        }

        return $this->render('TeapotioUserBundle:page:profile/settings.html.twig', $params);
    }
}
