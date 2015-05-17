<?php

/**
 * Copyright (c) Thomas Potaire
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @category   Teapotio
 * @package    ImageBundle
 * @author     Thomas Potaire
 */

namespace Teapotio\ImageBundle\Service;

use Teapotio\ImageBundle\Entity\Image;

class ImageService
{
    protected $em;
    protected $imageRepositoryClass;

    public function __construct ($doctrine, $imageRepositoryClass)
    {
        $this->em = $doctrine->getManager();
        $this->imageRepositoryClass = $imageRepositoryClass;
    }

    /**
     * Create an image object
     *
     * @return Image
     */
    public function createImage()
    {
        return new Image();
    }

    /**
     * Save an Image object|entity
     *
     * @param  Image  $image
     *
     * @return Image
     */
    public function save(Image $image)
    {
        $this->em->persist($image);
        $this->em->flush();

        return $image;
    }

    /**
     * Return an image entity or null
     *
     * @param  integer  $id   an image id
     *
     * @return Image|null
     */
    public function getById($id)
    {
        return $this->em->getRepository('TeapotioImageBundle:Image')->find($id);
    }

    /**
     * Setup the default image objects
     *
     * @return array
     */
    public function setup()
    {
        $image = $this->createImage();
        $image->setName('Default avatar');
        $image->setPath('default_avatar.png');

        $this->save($image);

        return array($image);
    }

}
