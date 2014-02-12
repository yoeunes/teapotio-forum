<?php

namespace Teapotio\ForumBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TeapotioForumBundle extends Bundle
{
	public function getParent()
	{
		return 'TeapotioBaseForumBundle';
	}
}
